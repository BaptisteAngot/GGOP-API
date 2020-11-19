<?php

namespace App\Controller;

use App\Document\GameSession\Player;
use App\Document\UserProfile\Game;
use App\Document\UserProfile\Team;
use App\Document\UserProfile\UserProfile;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Driver\AbstractDB2Driver;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MatchController extends AbstractController
{
    private $client;
    private $basicUrl = "http://51.255.160.47:8181/";
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    private function getListFromPasserelleMatch($region,$summonerName,$nbMatch=""){
        $url = $this->basicUrl.$region."/passerelle/getHistoryMatchList/".$summonerName;
        $cb = $this->client->request(
            'GET',
            $url
        );
        $responsecb = json_decode($cb->getContent(),true);
        if (isset($nbMatch) && $nbMatch== "")
        {
            $result = array_slice($responsecb["matches"],0,20);
        }else{
            $result = array_slice($responsecb["matches"],0,$nbMatch);
        }
        return $result;
    }

    private function getMatchFromPasserelle($region,$idGame)
    {
        $url = $this->basicUrl.$region."/passerelle/getHistoryMatch/".$idGame;
        $cb = $this->client->request(
            'GET',
            $url
        );
        $cbResult = json_decode($cb->getContent());
        if ($cbResult->code = "404")
        {
            $url = $this->basicUrl.$region."/riot/getHistoryMatch/".$idGame;
            $cb = $this->client->request(
                'GET',
                $url
            );
            $cbResult = json_decode($cb->getContent());
        }
        return $cbResult;
    }

    /**
     * @Route("/api/match/{region}/{summonerName}/{nbGame}",name="getLastMatch", methods={"GET"})
     * @param $region
     * @param $summonerName
     * @param string $nbGame
     * @param DocumentManager $documentManager
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function getLastMatch($region,$summonerName,$nbGame="",DocumentManager $documentManager,UserRepository $userRepository){
        $listMatch = $this->getListFromPasserelleMatch($region,$summonerName,$nbGame);
        $user = $userRepository->findOneBy(['pseudo'=>$summonerName]);
        if (!isset($user)){
            return JsonResponse::fromJsonString(json_encode("this summoner doesn't exist"),Response::HTTP_BAD_REQUEST);
        }
        $userProfile = $documentManager->getRepository(UserProfile::class)->findOneBy(['user_id' => $user->getId()]);
        if (!$userProfile)
            return JsonResponse::fromJsonString(json_encode("this summoner doesn't exist"),Response::HTTP_BAD_REQUEST);
        $gameHistoryPlayer = $userProfile->getGameHistory();

        $matchInfoList = [];
        foreach ($listMatch as $match){
            $var = false;
            $dateMatch = gmdate("Y-m-d\TH:i:s", substr($match['timestamp'], 0, -3));
            foreach ($gameHistoryPlayer as $mongoGameHistory){
                if ($dateMatch == $mongoGameHistory['date']){
                    $var = true;
                }
            }
            if (!$var) {
                $matchInfo = $this->getMatchFromPasserelle($region,$match['gameId']);
                array_push($matchInfoList,$matchInfo);
            }
        }
        foreach ($matchInfoList as $matchinfo) {
            $this->addGameToUserProfile($matchinfo, $user, $userRepository, $userProfile, $documentManager);
        }

        $response = new JsonResponse();
        if ($nbGame !== ""){
            $gamesToReturn = array_slice((array)$userProfile->getGameHistory(),-$nbGame,count((array)$userProfile->getGameHistory()));
        }else {
            $gamesToReturn = array_slice((array)$userProfile->getGameHistory(),0,count((array)$userProfile->getGameHistory()));
        }
        usort($gamesToReturn,function ($element1,$element2){
            $datetime1 = strtotime($element1['date']);
            $datetime2 = strtotime($element2['date']);
            return $datetime1 - $datetime2;
        });
        $response->setContent(json_encode($gamesToReturn));
        return $response;
    }

    private function searchGameResult($gameData, $summoner) {
        $participantId = $this->searchParticipantId($gameData,$summoner);
        $teamId = $this->searchTeamIdByParticipantId($gameData->participants,$participantId);
        return $this->searchResultByParticipantId($gameData->teams,$teamId);
    }

    private function searchParticipantId($game,$summonerName) {
        foreach ($game->participantIdentities as $participantIdentity) {
            if($participantIdentity->player->summonerName == $summonerName){
                return $participantIdentity->participantId;
            }
        }
    }

    private function searchTeamIdByParticipantId($participants,$participantId) {
        foreach ($participants as $participant){
            if ($participant->participantId === $participantId)
                return $participant->teamId;
        }
    }

    private function searchResultByParticipantId($teams,$teamsId) {
        foreach ($teams as $team) {
            if ($team->teamId == $teamsId){
                return $team->win;
            }
        }
    }

    private function sortInfoPlayerByParticipantId($participants,$participantsId)
    {
        foreach ($participants as $participant) {
            if ($participant->participantId == $participantsId){
                return $participant;
            }
        }
    }

    /**
     * @param $matchinfo
     * @param User|null $user
     * @param UserRepository $userRepository
     * @param UserProfile|null $userProfile
     * @param DocumentManager $documentManager
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function addGameToUserProfile($matchinfo, ?User $user, UserRepository $userRepository, ?UserProfile $userProfile, DocumentManager $documentManager): void
    {
        $game = new Game();
        $date = gmdate("Y-m-d\TH:i:s", substr($matchinfo->gameCreation, 0, -3));

        $game->setDate($date);
        $game->setResult($this->searchGameResult($matchinfo, $user->getRiotPseudo()));
        foreach ($matchinfo->teams as $teamData) {
            $team = new Team();
            $team->setWin($teamData->win);
            $team->setTeamId($teamData->teamId);
            foreach ($matchinfo->participantIdentities as $participantIdentity) {
                $teamId = $this->searchTeamIdByParticipantId($matchinfo->participants, $participantIdentity->participantId);
                if ($teamData->teamId == $teamId) {
                    $playerData = new \App\Document\UserProfile\Player();
                    $userFromWebSite = $userRepository->findOneBy(['riot_pseudo' => $participantIdentity->player->summonerName]);
                    if ($userFromWebSite) {
                        $playerData->setUserId($userFromWebSite->getId());
                    }
                    $playerData->setUserPseudo($participantIdentity->player->summonerName);
                    $playerDataParticipantId = $this->searchParticipantId($matchinfo, $participantIdentity->player->summonerName);
                    $playerGameInfo = $this->sortInfoPlayerByParticipantId($matchinfo->participants, $playerDataParticipantId);
                    $playerData->setAssist($playerGameInfo->stats->assists);
                    $playerData->setDeaths($playerGameInfo->stats->deaths);
                    $playerData->setChampion($playerGameInfo->championId);
                    $playerData->setKill($playerGameInfo->stats->kills);
                    $playerData->setSummonerSpells([$playerGameInfo->spell1Id, $playerGameInfo->spell2Id]);
                    $playerData->setItems([$playerGameInfo->stats->item0, $playerGameInfo->stats->item1, $playerGameInfo->stats->item2, $playerGameInfo->stats->item3, $playerGameInfo->stats->item4, $playerGameInfo->stats->item5, $playerGameInfo->stats->item6]);
                    $team->addPlayer($playerData);
                }
            }
            $game->addTeams($team);
        }
        $userProfile->addGameHistory($game);
        $documentManager->persist($userProfile);
        $documentManager->flush();
    }
}
