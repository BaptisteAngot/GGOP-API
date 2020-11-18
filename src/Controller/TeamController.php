<?php

namespace App\Controller;

use App\Document\Team\Team;
use App\Document\Team\TeamPlayer;
use App\Form\Team\TeamFormType;
use App\Repository\UserRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
        return JsonResponse::fromJsonString(json_encode("Team created at id: " . $team->getId()),Response::HTTP_OK);
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
