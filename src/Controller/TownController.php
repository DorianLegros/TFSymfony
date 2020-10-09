<?php

namespace App\Controller;

use App\Entity\Town;
use App\Repository\TownRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class TownController extends AbstractController
{
    private function serializeTown($town)
    {
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getSlug();
            },
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        $serializer = new Serializer([$normalizer], [new JsonEncoder()]);

        return $serializer->serialize($town, 'json');
    }

    /**
     * @Route("/towns", name="getTowns", methods={"GET"})
     * @param TownRepository $townRepository
     * @return JsonResponse
     */
    public function getTowns(Request $request, TownRepository $townRepository) {
        $filter = [];
        $em = $this->getDoctrine()->getManager();
        $champs = $em->getClassMetadata(Town::class)->getFieldNames();
        foreach ($champs as $champ) {
            if ($request->query->get($champ)) {
                $filter[$champ] = $request->query->get($champ);
            }
        }
        return JsonResponse::fromJsonString($this->serializeTown($townRepository->findBy($filter)));
    }

    /**
     * @Route("api/towns", name="createTown", methods={"POST"})
     * @param Request $request
     * @param TownRepository $townRepository
     * @return Response
     */
    public function createTown(Request $request, TownRepository $townRepository) {
        $response = new Response();
        $town = new Town();
        $reqData = json_decode($request->getContent(), true);
        $town->setNom($reqData["nom"])
            ->setCode($reqData["code"])
            ->setCodeDepartement($reqData["codeDepartement"])
            ->setCodeRegion($reqData["codeRegion"])
            ->setCodesPostaux($reqData["codesPostaux"])
            ->setPopulation($reqData["population"]);

        $em = $this->getDoctrine()->getManager();
        $em->persist($town);
        $em->flush();
        $response->setContent("La commune a été créée avec succès avec l'Id: " . $town->getId());
        $response->setStatusCode(Response::HTTP_CREATED);
        return $response;
    }

    /**
     * @Route("api/towns/{id}", name="updateTown", methods={"PUT"})
     * @param int $id
     * @param Request $request
     * @param TownRepository $townRepository
     * @return Response
     */
    public function updateTown(int $id, Request $request, TownRepository $townRepository) {
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $reqData = json_decode($request->getContent(), true);
        $nbChanges = 0;

        if (isset($id)) {
            $town = $townRepository->find($id);
            if ($town === null) {
                $response->setContent("Cette commune n'existe pas");
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
            } else {
                if(isset($reqData["nom"])) {
                    $town->setNom($reqData["nom"]);
                    $nbChanges++;
                }
                if(isset($reqData["code"])) {
                    $town->setCode($reqData["code"]);
                    $nbChanges++;
                }
                if(isset($reqData["codeDepartement"])) {
                    $town->setCodeDepartement($reqData["codeDepartement"]);
                    $nbChanges++;
                }
                if(isset($reqData["codeRegion"])) {
                    $town->setCodeRegion($reqData["codeRegion"]);
                    $nbChanges++;
                }
                if(isset($reqData["codesPostaux"])) {
                    $town->setCodesPostaux($reqData["codesPostaux"]);
                    $nbChanges++;
                }
                if(isset($reqData["population"])) {
                    $town->setPopulation($reqData["population"]);
                    $nbChanges++;
                }

                if($nbChanges === 0) {
                    $response->setContent("Veuillez renseigner des champs pour la modification");
                    $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                } else {
                    $em->persist($town);
                    $em->flush();
                    $response->setContent("La commune a bien été modifiée");
                    $response->setStatusCode(Response::HTTP_OK);
                }
            }
        } else {
            $response->setContent("Vous devez renseigner l'ID de la commune que vous souhaitez modifier.");
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        return $response;
    }

    /**
     * @Route("api/towns/{id}", name="deleteTown", methods={"DELETE"})
     * @param int $id
     * @param TownRepository $townRepository
     * @return Response
     */
    public function deleteTown(int $id, TownRepository $townRepository) {
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        if(isset($id)) {
            $town = $townRepository->find($id);
            if ($town === null) {
                $response->setContent("Cette commune n'existe pas");
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
            } else {
                $em->remove($town);
                $em->flush();
                $response->setContent("La commune a bien été supprimée.");
                $response->setStatusCode(Response::HTTP_OK);
            }
        } else {
            $response->setContent("Vous devez renseigner l'ID de la commune que vous souhaitez supprimer.");
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        return $response;
    }
}
