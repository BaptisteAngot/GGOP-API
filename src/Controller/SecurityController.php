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
use OpenApi\Annotations as OA;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="api_login", methods={"POST"})
     * @param UserRepository $userRepository
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param JWTTokenManagerInterface $JWTTokenManager
     * @return JsonResponse
     * @OA\Response(
     *     response="200",
     *     description="Login successfully",
     *     @OA\JsonContent(
     *     type="json",
     *     example="{
    'token': 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MDQxNTk2MjEsImV4cCI6MTYwNDE2MzIyMSwicm9sZXMiOlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6ImFkbWluQGFkbWluLmNvbSJ9.YxdM8-9VQ0pRzD02i-9LolEEgXbkXhINway3DQb7V1N4tFBr7gfoFEU4-p-cML3ngYKzCR987YZI1_E2nu8TCi6Mua8z6iOcC94jaqHVk7LWCdHBfny8adE62uWHTaxQihcCl7Td9fVwMPGbbwkL7gEKvaQmihLiFmO3Rkry9tKx6SCNZ7nvm_bx2lRvWgMXQx5_52eYKUi6QehA9fhljWB8YGOXeEwWPCaLw5YgRdr7CLACKWE9l2oS30tIGf84YoCd6V926z9M0w9k3wdr2PzPlVWk2WDDLpjOG2mwGG9FDRmcegUduo1d3I2JEtD8lh0G06Zfv65vDlMURGX7AH6wTsc8uQckVISFbt8C8zj_5Jh60rJzLj4iPW80udyiNnjQgBraa6BIIO2VU1nht6rKNoXETqYo54amczymUnbaHnutfwHij74A9rgNXOsFwpaM04pWAkWrctkzTYhEgs7A7mvelPamQWmWEXGVoKHSKO2bar9WJemcMx3AnDLDdAMMkGzXZqbkz91sLBsspTfXJK15mCeo0o74iFtixxyrxpgSNmR9fYjKO7p6vT-2JSQaAPgU8MuOyXQJ3ytYnBVMztZGcX_CxCcm4pHSmYK-D6sUf8EBWCS4G9yd0020kZXTH2hquqGJB0hY8mvZhQEohJyEob_IraBl0xWXNVI',
    'roles': [
    'ROLE_ADMIN',
    'ROLE_USER'
    ],
    'username': 'admin@admin.com'
}"
     *      )
     * )
     * @OA\Response(
     *     response="401",
     *     description= "User is ban",
     *     @OA\JsonContent(
     *     type="json",
     *     example= "User is banned"
     *      )
     * )
     * @OA\Response(
     *     response="400",
     *     description= "Something went wrong"
     * )
     * @OA\Parameter(
     *     name="email",
     *     in="query",
     *     required=true,
     *     description="Your mail",
     *     @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name="password",
     *     in="query",
     *     required=true,
     *     description="Your password",
     *     @OA\Schema(type="string")
     * )
     * @OA\Tag(name="User")
     */
    public function api_login(UserRepository $userRepository,Request $request,UserPasswordEncoderInterface $encoder, JWTTokenManagerInterface $JWTTokenManager): JsonResponse
    {
        $response = new JsonResponse();
        $datas = json_decode($request->getContent(),true);

        if (isset($datas['email']) && isset($datas['password']) ) {
            $user = $userRepository->findOneBy(['email' => $datas['email']]);
            if ($user) {
                if ($encoder->isPasswordValid($user,$datas['password'])){
                    if ($this->checkBans($user->getBans())) {
                        $responseContent = [
                          'token' => $JWTTokenManager->create($user),
                          'roles' => $user->getRoles(),
                          'username' => $user->getUsername()
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
