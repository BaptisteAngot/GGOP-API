<?php

namespace App\Controller;

use App\Document\UserProfile\Honor;
use App\Document\UserProfile\UserProfile;
use App\Repository\UserRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/api/userProfil/{id}", name="deleteUserProfil",methods={"DELETE"})
     * @param DocumentManager $documentManager
     * @return JsonResponse
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function eraseUserProfil($id,DocumentManager $documentManager){
        $userProfil = $documentManager->getRepository(UserProfile::class)->find($id);
        $documentManager->remove($userProfil);
        $documentManager->flush();
        return JsonResponse::fromJsonString(json_encode("ok"),Response::HTTP_OK);
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

    /**
     * @Route("/api/userProfil/honor/{idUser}", name="addHonor" ,methods={"POST"})
     * @param $idUser
     * @param Request $request
     * @param UserRepository $userRepository
     * @param DocumentManager $documentManager
     */
    public function addHonor($idUser, Request $request,UserRepository $userRepository, DocumentManager $documentManager) {
        $datas = json_decode(
            $request->getContent(),
            true
        );
        $response = new JsonResponse();
        if(isset($idUser)) {
            $user = $userRepository->find($idUser);
            if (isset($user)) {
                $userProfil = $documentManager->getRepository(UserProfile::class)->findOneBy(['user_id'=>$user->getId()]);
                if (isset($userProfil)) {
                    $reputation = $userProfil->getReputation();
                    $newRepu = $reputation[0];
                    for ($k = 0; $k < count($newRepu['honors']) ; $k++) {
                      if ($datas['type'] === $newRepu['honors'][$k]['type']) {
                          $newRepu['honors'][$k]['number']++;
                      }
                    }
                    $reputation[0]=$newRepu;
                    $ratio = $this->calculRatio($reputation[0]);
                    $reputation[0]['ratio'] = $ratio;
                    $userProfil->setReputation((array)$reputation);
                    $documentManager->persist($userProfil);
                    $documentManager->flush();
                }else{
                    $response->setContent(json_encode("This user don't exist"));
                    $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                }
            }else {
                $response->setContent(json_encode("This user don't exist"));
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            }
        }
        return $response;
    }

    /**
     * @Route("/api/userProfil/report/{idUser}", name="addReport" ,methods={"POST"})
     * @param $idUser
     * @param Request $request
     * @param UserRepository $userRepository
     * @param DocumentManager $documentManager
     */
    public function addReport($idUser, Request $request,UserRepository $userRepository, DocumentManager $documentManager) {
        $datas = json_decode(
            $request->getContent(),
            true
        );
        $response = new JsonResponse();
        if(isset($idUser)) {
            $user = $userRepository->find($idUser);
            if (isset($user)) {
                $userProfil = $documentManager->getRepository(UserProfile::class)->findOneBy(['user_id'=>$user->getId()]);
                if (isset($userProfil)) {
                    $reputation = $userProfil->getReputation();
                    $newRepu = $reputation[0];
                    for ($k = 0; $k < count($newRepu['reports']) ; $k++) {
                        if ($datas['type'] === $newRepu['reports'][$k]['type']) {
                            $newRepu['reports'][$k]['number']++;
                        }
                    }
                    $reputation[0]=$newRepu;
                    $ratio = $this->calculRatio($reputation[0]);
                    $reputation[0]['ratio'] = $ratio;
                    $userProfil->setReputation((array)$reputation);
                    $documentManager->persist($userProfil);
                    $documentManager->flush();
                }else{
                    $response->setContent(json_encode("This user don't exist"));
                    $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                }
            }else {
                $response->setContent(json_encode("This user don't exist"));
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            }
        }
        return $response;
    }

    private function calculRatio($reputation) {
        $nbhonor = 0;
        $nbreport = 0;
        foreach ($reputation['honors'] as $honor) {
            $nbhonor += $honor['number'];
        }
        foreach ($reputation['reports'] as $report) {
            $nbreport += $report['number'];
        }
        if ($nbreport == 0) {
            return $nbhonor;
        }elseif ($nbhonor == 0 && $nbreport>0) {
            return 0;
        }else{
            return ($nbhonor/$nbreport);
        }
    }
}
