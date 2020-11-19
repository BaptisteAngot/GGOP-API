<?php

namespace App\Controller;

use App\Document\UserProfile\UserProfile;
use App\Repository\UserRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserProfilController extends AbstractController
{
    /**
     * @Route("/api/userProfil/{id}", name="user_profil",methods={"GET"})
     * @param $id
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function getuserProfilFromId($id, UserRepository $userRepository,DocumentManager $documentManager)
    {
        $user = $userRepository->find($id);
        if ($user)
        {
            $userProfil = $documentManager->getRepository(UserProfile::class)->findOneBy(['user_id'=>$user->getId()]);
            if ($userProfil) {
                $response = new JsonResponse();
                $response->setContent(json_encode($userProfil));
                $response->setStatusCode(Response::HTTP_OK);
                return $response;
            }else{
                return JsonResponse::fromJsonString(json_encode("this userProfil don't exist"),Response::HTTP_BAD_REQUEST);
            }
        }else{
            return JsonResponse::fromJsonString(json_encode("this user don't exist"),Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/userProfil/", name="getAllUserProfio",methods={"GET"})
     * @param DocumentManager $documentManager
     * @return JsonResponse
     */
    public function getAllUserProfil(DocumentManager $documentManager)
    {
        return JsonResponse::fromJsonString(json_encode($documentManager->getRepository(UserProfile::class)->findAll()));
    }
}
