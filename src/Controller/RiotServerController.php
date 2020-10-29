<?php

namespace App\Controller;

use App\Entity\RiotServer;
use App\Form\RiotServerForm2Type;
use App\Repository\RiotServerRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use JMS\Serializer\SerializationContext;

class RiotServerController extends AbstractController
{
    /**
     * @Route("riotServer", name="getRiotServer",methods={"GET"})
     * @param RiotServerRepository $riotServerRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getServer(RiotServerRepository $riotServerRepository, SerializerInterface $serializer) {
        $riotServers = $riotServerRepository->findAll();
        $jsonContent = $this->serializeRiotServer2($riotServers,$serializer);
        $response = JsonResponse::fromJsonString($jsonContent);
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }

    /**
     * @Route("api/admin/riotServer", name="createRiotServer", methods={"POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function createServer(Request $request,ValidatorInterface $validator) {
        $riotServer = new RiotServer();
        $datas = json_decode($request->getContent(),true);
        $form = $this->createForm(RiotServerForm2Type::class,$riotServer);
        $form->submit($datas);

        //Validation des champs
        $violations = $validator->validate($riotServer);
        if (0 !== count($violations)) {
            foreach ($violations as $error) {
                return JsonResponse::fromJsonString($error->getMessage(),Response::HTTP_BAD_REQUEST);
            }
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($riotServer);
        $entityManager->flush();
        return JsonResponse::fromJsonString("",Response::HTTP_OK);
    }

    /**
     * @Route("api/admin/riotServer/{id}", name="updateRiotServer", methods={"PATCH"})
     * @ParamConverter("server", options={"id"="id"})
     * @param RiotServer $server
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function updateServer(RiotServer $server,Request $request,ValidatorInterface $validator,EntityManagerInterface $entityManager) {
        $datas = json_decode($request->getContent(),true);
        $form = $this->createForm(RiotServerForm2Type::class,$server);
        $form->submit($datas);
        $violations = $validator->validate($server);
        if (0 !== count($violations)) {
            foreach ($violations as $error) {
                return JsonResponse::fromJsonString($error->getMessage(),Response::HTTP_BAD_REQUEST);
            }
        }
        $entityManager->flush();
        return JsonResponse::fromJsonString("",Response::HTTP_OK);
    }

    /**
     * @Route("api/admin/riotServer", name="DeleteRiotServer", methods={"DELETE"})
     * @param Request $request
     * @param RiotServerRepository $riotServerRepository
     * @return JsonResponse
     */
    public function deleteServer(Request $request, RiotServerRepository $riotServerRepository) {
        $entityManager = $this->getDoctrine()->getManager();
        $data = json_decode(
            $request->getContent(),
            true
        );
        $response = new JsonResponse();
        if (isset($data['idServer'])) {
            $riotServer = $riotServerRepository->find($data['idServer']);
            if (!$riotServer) {
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            } else {
                $entityManager->remove($riotServer);
                $entityManager->flush();
                $response->setContent("Server successfull delete");
                $response->setStatusCode(Response::HTTP_OK);
            }
        }else {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }
        return $response;
    }

    private function serializeRiotServer2($objet,SerializerInterface $serializer,$groupe="riotServer") {
        return $serializer->serialize($objet,"json", SerializationContext::create()->setGroups(array($groupe)));
    }

    private function serializeRiotServer($objet) {
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getName();
            },
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        $serializer = new Serializer([$normalizer], [new JsonEncoder()]);
        return $serializer->serialize($objet, 'json');
    }
}
