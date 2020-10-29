<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User\UserFormType;
use App\Repository\UserRepository;
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

class UserController extends AbstractController
{
    /**
     * @Route("user", name="createUser", methods={"POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return JsonResponse
     */
    public function createUser(Request $request, ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder)
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
        return JsonResponse::fromJsonString("User created at id: " . $user->getId(),Response::HTTP_OK);
    }

    /**
     * @Route("admin/user", name="getAllUser", methods={"GET"})
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
