<?php

namespace App\Controller;

use App\Entity\Ban;
use App\Repository\BanRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
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

class BanController extends AbstractController
{
    /**
     * @Route("/ban", name="ban")
     */
    public function index()
    {
        return $this->render('ban/index.html.twig', [
            'controller_name' => 'BanController',
        ]);
    }

    /**
     * @Route("ban", name="ban",methods={"GET"})
     * @param Request $request
     * @param BanRepository $banRepository
     * @return JsonResponse
     */
    public function getBan(Request $request,BanRepository $banRepository) {
        $filter = [];
        $em = $this->getDoctrine()->getManager();
        $metaData = $em->getClassMetadata(Ban::class)->getFieldNames();
        foreach ($metaData as $value) {
            if ($request->query->get($value)) {
                $filter[$value] = $request->query->get($value);
            }
        }
        return JsonResponse::fromJsonString($this->serializeBan($banRepository->findBy($filter)));
    }

    /**
     * @Route("admin/ban", name="DeleteBan", methods={"DELETE"})
     * @param Request $request
     * @param BanRepository $banRepository
     * @return JsonResponse
     */
    public function deleteBan(Request $request,BanRepository $banRepository) {
        $entityManager = $this->getDoctrine()->getManager();
        $data = json_decode(
            $request->getContent(),
            true
        );
        $response = new JsonResponse();
        if (isset($data['id'])) {
            $user = $banRepository->find($data['id']);
            if (!$user) {
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            } else {
                $entityManager->remove($user);
                $entityManager->flush();
                $response->setContent("Server successfull delete");
                $response->setStatusCode(Response::HTTP_OK);
            }
        }else {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }
        return $response;
    }

//    public function createBan(Request $request, ValidatorInterface $validator) {
//        $ban = new Ban();
//        $datas = json_decode($request->getContent(),true);
//        $form = $this->createForm()
//    }


    private function serializeBan($objet) {
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
