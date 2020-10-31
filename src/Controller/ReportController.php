<?php

namespace App\Controller;

use App\Document\Report\Report;
use App\Entity\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
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

class ReportController extends AbstractController
{
    /**
     * @Route("api/admin/report/{id}", name="getReportPlayer", methods={"GET"})
     * @ParamConverter("user", options={"id"="id"})
     * @param DocumentManager $documentManager
     * @param User $user
     * @return JsonResponse
     */
    public function getReportByUserID(DocumentManager $documentManager,User $user)
    {
        $response = new JsonResponse();
        $report = $documentManager->getRepository(Report::class)->findBy(['for_user' => $user->getId()]);
        $response->setStatusCode(Response::HTTP_OK);
        $response->setContent($this->serializeReport($report));
        return $response;
    }

    /**
     * @Route("api/report", name="createReportPlayer", methods={"POST"})
     * @param Request $request
     * @param DocumentManager $manager
     * @return Response
     * @throws MongoDBException
     */
    public function createReport(Request $request,DocumentManager $manager)
    {
        $response = new Response();
        $datas = json_decode($request->getContent(),true);
        if (isset($datas['from_user']) && isset($datas['for_user']) && isset($datas['description'])) {
            $report = new Report();
            $report->setForUser($datas['for_user']);
            $report->setFromUser($datas['from_user']);
            $report->setDescription($datas['description']);
            $manager->persist($report);
            $manager->flush();
            $response->setStatusCode(Response::HTTP_OK);
            $response->setContent("New report created at id : " . $report->getId());
        }else {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }
        return $response;
    }

    public function serializeReport($objet) {
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
