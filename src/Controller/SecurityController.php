<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="api_login", methods={"POST"})
     * @param UserRepository $userRepository
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param JWTTokenManagerInterface $JWTTokenManager
     * @return JsonResponse
     */
    public function api_login(UserRepository $userRepository,Request $request,UserPasswordEncoderInterface $encoder, JWTTokenManagerInterface $JWTTokenManager): JsonResponse
    {
        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $datas = json_decode($request->getContent(),true);

        if (isset($datas['email']) && isset($datas['password']) ) {
            $user = $userRepository->findOneBy(['email' => $datas['email']]);
            if ($user) {
                if ($encoder->isPasswordValid($user,$datas['password'])){
                    if ($this->checkBans($user->getBans())) {
                        $responseContent = [
                          'token' => $JWTTokenManager->create($user),
                          'roles' => $user->getRoles(),
                          'username' => $user->getPseudo(),
                          'userId' => $user->getId()
                        ];
                        $response->setContent(json_encode($responseContent));
                        $response->setStatusCode(Response::HTTP_OK);
                    }else {
                        $response->setContent("User is banned");
                        $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
                    }
                }else {
                    $response->setContent("Invalid password or email");
                    $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                }
            }else{
                $response->setContent("User don't exist");
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            }
        } else {
            $response->setContent("Bad syntax");
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }
        return $response;
    }

    protected function checkBans($bans): bool {
        $bool = true;
        if (!isset($bans)) {
            return true;
        } else {
            $currentDate = date('Y-m-d');
            foreach ($bans as $ban) {
                $start = $ban->getStart();
                $end = $ban->getEnd();
                if (($currentDate >= $start->format('Y-m-d')) && ($currentDate <= $end->format('Y-m-d'))) {
                    $bool = false;
                }
            }
        }
        return $bool;
    }

}
