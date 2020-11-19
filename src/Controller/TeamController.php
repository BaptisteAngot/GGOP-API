<?php

namespace App\Controller;

use App\Document\Request\RequestGGOP;
use App\Document\Team\Team;
use App\Document\Team\TeamPlayer;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use http\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use function Composer\Autoload\includeFile;

class TeamController extends AbstractController
{
    /**
     * @Route("api/team", name="team", methods={"GET"})
     * @param DocumentManager $documentManager
     * @return JsonResponse
     */
    public function index(DocumentManager $documentManager)
    {
        $player = $documentManager->getRepository(Team::class)->findAll();
        return JsonResponse::fromJsonString($this->serializeUser($player));
    }

    /**
     * @Route("api/team/{id}", name="GetTeamByID", methods={"GET"})
     * @param DocumentManager $documentManager
     * @param $id
     * @return JsonResponse
     */
    public function getTeamByIDUser(DocumentManager $documentManager, $id){
        $team = $documentManager->
                    getRepository(Team::class)->
                    findBy(['creator_id'=>$id])
                ;
        return JsonResponse::fromJsonString($this->serializeUser($team));
    }

    /**
     * @Route("api/team", name="createTeam", methods={"POST"})
     * @param DocumentManager $documentManager
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @throws MongoDBException
     */
    public function createTeam(DocumentManager $documentManager, Request $request, UserRepository $userRepository) : JsonResponse
    {
        $team = new Team();
        $datas = json_decode($request->getContent(), true);
        if (!isset($datas['creator_id'])) {
            return JsonResponse::fromJsonString(Response::HTTP_BAD_REQUEST);
        }
        if (isset($datas['name']))
            $team->setName($datas['name']);
        else
            return JsonResponse::fromJsonString($datas['name'],Response::HTTP_BAD_REQUEST);

        $playerCreator = new TeamPlayer();
        $playerCreator->setUserId($datas['creator_id']);
        $playerCreator->setStatus("MEMBER");
        $userCreator = $userRepository->find($datas['creator_id']);
        if (!$userCreator)
        {
            return JsonResponse::fromJsonString(json_encode('User at this id not exist : ' . $datas['creator_id']),Response::HTTP_BAD_REQUEST);
        }
        $tabPlayer = [];
        array_push($tabPlayer,$playerCreator);
        $playerCreator->setUserPseudo($userCreator->getPseudo());
        $team->setCreatorId($datas['creator_id']);
        $team->setPlayers($tabPlayer);
        $team->setWinRate(1);
        $team->setIsComplete(false);
        $documentManager->persist($team);
        $documentManager->flush();
        return JsonResponse::fromJsonString(json_encode($team->getId()),Response::HTTP_OK);
    }

    /**
     * @Route("/api/team/byID/{idTeam}", methods={"GET"}, name="getTeamByIdTeam")
     * @param $idTeam
     * @param DocumentManager $documentManager
     * @return JsonResponse
     */
    public function getTeamByIDTeam($idTeam,DocumentManager $documentManager) {
        $team = $documentManager->getRepository(Team::class)->find($idTeam);
        if ($team){
            return JsonResponse::fromJsonString(json_encode($team),Response::HTTP_OK);
        }else {
            return JsonResponse::fromJsonString(json_encode("team don't exist"),Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/team/addPlayerToTeam",name="requestAddPlayerToTeam" ,methods={"POST"})
     * @param Request $request
     * @param DocumentManager $documentManager
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function requestAddPlayerToTeam(Request $request, DocumentManager $documentManager, UserRepository $userRepository) : JsonResponse
    {
        $datas = json_decode($request->getContent(),true);
        if (isset($datas['idTeam']) && isset($datas['idPlayer'])) {
            $team = $documentManager->getRepository(Team::class)
                ->find($datas['idTeam']);
            if (!isset($team))
                return JsonResponse::fromJsonString(json_encode("Team don't exist."),Response::HTTP_BAD_REQUEST);
            $user = $userRepository->find($datas['idPlayer']);
            if (!$user)
                return JsonResponse::fromJsonString(json_encode("User don't exist."),Response::HTTP_BAD_REQUEST);

            //VERIF PAS PLUS DE 5
            if(!$this->verifMoreThanFivePlayerInTeam($team)){
                $this->addPlayerToTeam($team,$user,$documentManager);
                $reponseReturn = $this->forward('App\Controller\RequestController::createRequest', [
                    'type' => 'INVITE_TEAM',
                    'to' => $datas['idPlayer'],
                    'from' => $team->getCreatorId(),
                    'requestValue' => $datas['idTeam'],
                    'documentManager' => $documentManager
                ]);
                if ($reponseReturn->getContent() === "true") {
                    return JsonResponse::fromJsonString(json_encode("Player successfuly invite to team"),Response::HTTP_OK);
                }else
                    return JsonResponse::fromJsonString(json_encode("Request don't send."),Response::HTTP_BAD_REQUEST);
            }else {
                return JsonResponse::fromJsonString(json_encode("More than 5 player or equals to."),Response::HTTP_BAD_REQUEST);
            }
        }
    }

    /**
     * @Route("api/team", name="deleteTeam", methods={"DELETE"})
     * @param Request $request
     * @param DocumentManager $dm
     * @return JsonResponse
     */
    public function deleteTeam(Request $request, DocumentManager $dm)
    {
        $datas = json_decode($request->getContent(),true);
        if (isset($datas['idTeam'])) {
            $team = $dm->getRepository(Team::class)->find($datas['idTeam']);
            if ($team){
                $dm->remove($team);
                $dm->flush();
                return JsonResponse::fromJsonString(json_encode("Team delete successfuly."),Response::HTTP_OK);
            }else
                return JsonResponse::fromJsonString(json_encode("Team don't exist."),Response::HTTP_BAD_REQUEST);
        }
    }

    //TODO SUPPRESSION UNE PERSONNE D'UNE EQUIPE

    /**
     * @Route("api/team/{idTeam}/{idRequest}/{value}", name="updateAddTeam", methods={"POST"})
     * @param $idTeam
     * @param $idRequest
     * @param $value
     * @param DocumentManager $documentManager
     * @return JsonResponse
     * @throws MongoDBException
     */
    public function updateAddTeam($idTeam,$idRequest,$value,DocumentManager $documentManager){
        if (isset($idRequest) && isset($idTeam) &&  isset($value))
        {
           $requestGGOP = $documentManager->getRepository(RequestGGOP::class)->find($idRequest);
           $team = $documentManager->getRepository(Team::class)->find($idTeam);
           if ($requestGGOP && $team && ($value == "MEMBER" || $value == "REFUSED")) {
                    $players = $team->getPlayers();
                    for ($i = 0; $i < count((array)$players); $i++)
                    {
                        if ($players[$i]['user_id'] == $requestGGOP->getTo()) {
                            if ($value == "MEMBER"){
                                $players[$i]['status'] = "MEMBER";
                            }else {
                                unset($players[$i]);
                            }
                            $this->requestUpdateTeam($requestGGOP,$documentManager);
                        }
                    }
                    $team->setPlayers($players);
                    if ($this->verifMoreThanFivePlayerInTeam($team) && $this->verifPlayerIsMember($team) ){
                        $team->setIsComplete(true);
                    }
                    $documentManager->persist($team);
                    $documentManager->flush();
                    return JsonResponse::fromJsonString(json_encode("Update successfly."),Response::HTTP_OK);
           }else
               return JsonResponse::fromJsonString(json_encode("BAD SYNTAX."),Response::HTTP_BAD_REQUEST);
        }else
            return JsonResponse::fromJsonString(json_encode("BAD SYNTAX."),Response::HTTP_BAD_REQUEST);
    }

    private function addPlayerToTeam(Team $team, User $user,DocumentManager $documentManager)
    {
        $teamPlayer = new TeamPlayer();
        $teamPlayer->setStatus('PENDING');
        $teamPlayer->setUserId($user->getId());
        $teamPlayer->setUserPseudo($user->getPseudo());
        $team->addPlayers($teamPlayer);
        $documentManager->persist($team);
        $documentManager->flush();
    }


    // If return true = more than 5 players or equals to 5 players
    // If return false = it's ok you can add player
    private function verifMoreThanFivePlayerInTeam(Team $team) : bool
    {
        return (count($team->getPlayers()) >= 5) ? true : false;
    }

    private function verifPlayerIsMember(Team $team) {
        $tabPlayers = $team->getPlayers();
        $returnValue = true;
        foreach ($tabPlayers as $player){
            if ($player['status'] !== "MEMBER")
                $returnValue = false;
        }
        return  $returnValue;
    }

    private function requestUpdateTeam(RequestGGOP $requestGGOP, DocumentManager $documentManager) {
        $reponseReturn = $this->forward('App\Controller\RequestController::deleteRequest', [
            'idRequest' => $requestGGOP->getId(),
            'dm' => $documentManager
        ]);
        if ($reponseReturn->getContent() == "true")
            return JsonResponse::fromJsonString(json_encode("Update team successfuly"),Response::HTTP_OK);
        else
            return JsonResponse::fromJsonString(json_encode($reponseReturn->getContent()),Response::HTTP_BAD_REQUEST);
    }

    public function serializeUser($objet) {
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        $serializer = new Serializer([$normalizer], [new JsonEncoder()]);

        return $serializer->serialize($objet, 'json');
    }
}
