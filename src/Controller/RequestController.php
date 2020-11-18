<?php

namespace App\Controller;

use App\Document\Request\RequestOGG;
use App\Entity\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Document\Team\Team;
use App\Document\Request\RequestGGOP;



class RequestController extends AbstractController
{
    const VALIDATE_TYPE = ['INVITE_TEAM','INVITE_MATCHMAKING','REQUEST_MATCHMAKING','REQUEST_TOURNAMENT'];

    public function createRequest($type,$from,$to,$requestValue, DocumentManager $documentManager)
    {
        $response = new Response();
        if (isset($type) && isset($from) && isset($to) && isset($requestValue)) {
            if (in_array($type,self::VALIDATE_TYPE)) {
                $requestGGOP = new RequestGGOP();
                $requestGGOP->setType($type);
                $requestGGOP->setRequestValue($requestValue);
                $requestGGOP->setTo($to);
                $requestGGOP->setFrom($from);
                $documentManager->persist($requestGGOP);
                try {
                    $documentManager->flush();
                } catch (MongoDBException $e) {
                    $response->setContent("false");
                }
                $response->setContent("true");
            }else{
                $response->setContent("false");
            }
        }else
            $response->setContent("false");
        return $response;
    }

    /**
     * @Route("/api/request/{idRequest}/{value}", name="updateRequest", methods={"POST"})
     * @param Request $request
     * @param DocumentManager $documentManager
     * @return JsonResponse
     */
    public function updateRequest(Request $request,DocumentManager $documentManager, $idRequest, $value)
    {
        if (!isset($requestGGOP))
            return JsonResponse::fromJsonString(json_encode("Bad syntax"),Response::HTTP_BAD_REQUEST);
        $requestGGOP = $documentManager->getRepository(RequestGGOP::class)->find($idRequest);
        if (!isset($requestGGOP))
            return JsonResponse::fromJsonString(json_encode("This request doesn't exist"),Response::HTTP_BAD_REQUEST);

    }

    public function deleteRequest($idRequest, DocumentManager $dm) {
        $response = new Response();
        $requestGGOP = $dm->getRepository(RequestGGOP::class)->find($idRequest);
        if ($requestGGOP){
            $dm->remove($requestGGOP);
            $dm->flush();
        }else{
            $response->setContent("false");
        }
        return $response;
    }
}
