<?php

namespace App\Controller;

use App\Entity\Ban;
use App\Form\BanFormType\BanFormType;
use App\Repository\BanRepository;
use Doctrine\ORM\EntityManagerInterface;
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
use OpenApi\Annotations as OA;


class BanController extends AbstractController
{

    /**
     * @Route("api/admin/ban", name="ban",methods={"GET"})
     * @param Request $request
     * @param BanRepository $banRepository
     * @return JsonResponse
     * @OA\Response(
     *     response="200",
     *     description="Get all ban"
     * )
     * @OA\Tag(name="Ban")
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
     * @Route("api/admin/ban", name="DeleteBan", methods={"DELETE"})
     * @param Request $request
     * @param BanRepository $banRepository
     * @return JsonResponse
     * @OA\Tag(name="Ban")
     * @OA\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Authorization",
     *     @OA\Schema(type="string")
     * )
     *
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



    /**
     * @Route("api/admin/ban", name="createBan", methods={"POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function createBan(Request $request, ValidatorInterface $validator) {
        $ban = new Ban();
        $datas = json_decode($request->getContent(),true);
        $form = $this->createForm(BanFormType::class,$ban);
        $form->submit($datas);

        $violations = $validator->validate($ban);
        if (0 !== count($violations)) {
            foreach ($violations as $error) {
                return JsonResponse::fromJsonString($error->getMessage(),Response::HTTP_BAD_REQUEST);
            }
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($ban);
        $entityManager->flush();
        return JsonResponse::fromJsonString("Ban created at id: " . $ban->getId(),Response::HTTP_OK);
    }

    /**
     * @Route("api/admin/ban/{id}", name="updateBan", methods={"PATCH"})
     * @ParamConverter("ban", options={"id"="id"})
     * @param Ban $ban
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function updateBan(Ban $ban, Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator) {
        $datas = json_decode($request->getContent(),true);
        $form = $this->createForm(BanRepository::class,$ban);
        $form->submit($datas);
        $violations = $validator->validate($ban);
        if (0 !== count($violations)) {
            foreach ($violations as $error) {
                return JsonResponse::fromJsonString($error->getMessage(),Response::HTTP_BAD_REQUEST);
            }
        }
        $entityManager->flush();
        return JsonResponse::fromJsonString("",Response::HTTP_OK);
    }

    private function serializeBan($objet) {
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
