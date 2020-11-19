<?php

namespace App\Controller;

use App\Document\Report\Report;
use App\Document\UserProfile\Honor;
use App\Document\UserProfile\Reputation;
use App\Document\UserProfile\UserProfile;
use App\Entity\User;
use App\Form\User\UserFormType;
use App\Repository\UserRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UserController extends AbstractController
{

    private $client;
    private $https = "https://";
    private $baseurl = ".api.riotgames.com/lol/";
    private $endPointGetUserInfoBySummonerName = "summoner/v4/summoners/by-name/";
    private $endPointGetRankAccount = "league/v4/entries/by-summoner/";

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @Route("register", name="createUser", methods={"POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param DocumentManager $documentManager
     * @return JsonResponse
     */
    public function createUser(Request $request, ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder,DocumentManager $documentManager)
    {
        $user = new User();
        $form = $this->createForm(UserFormType::class,$user);
        $form->submit(json_decode($request->getContent(),true));

        $violations = $validator->validate($user);
        if (0 !== count($violations)) {
            foreach ($violations as $error) {
                return JsonResponse::fromJsonString($error->getMessage(),Response::HTTP_BAD_REQUEST);
            }
        }
        $entityManager = $this->getDoctrine()->getManager();
        $password = $passwordEncoder->encodePassword($user,$user->getPassword());
        $user->setPassword($password);
        $entityManager->persist($user);
        $entityManager->flush();

        //Création document UserProfile du player
        $userProfile = new UserProfile();
        $userProfile->setUserId($user->getId());
        $reputation = new Reputation();
        $reputation->setRatio(1);
        $TYPEHONOR = ['sang-froid','super-leader','gg'];
        //Création de 3 honors
        for ($i = 0; $i < 3; $i++){
            $honor = new Honor();
            $honor->setType($TYPEHONOR[$i]);
            $honor->setNumber(0);
            $reputation->addHonors($honor);
        }

        $TYPEREPORT = ['feeding','discrimination','afk'];
        for ($i = 0; $i < 3 ; $i++) {
            $report = new \App\Document\UserProfile\Report();
            $report->setNumber(0);
            $report->setType($TYPEREPORT[$i]);
            $reputation->addReports($report);
        }
        $userProfile->addReputation($reputation);

        $documentManager->persist($userProfile);
        $documentManager->flush();

        return JsonResponse::fromJsonString("User created at id: " . $user->getId(),Response::HTTP_OK);
    }

    /**
     * @Route("api/admin/user", name="getAllUser", methods={"GET"})
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function getAllUsers(UserRepository $userRepository) {
        $users = $userRepository->findAll();
        $jsonContent = $this->serializeUser($users);
        $response = JsonResponse::fromJsonString($jsonContent);
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
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
