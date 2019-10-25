<?php

namespace App\Controller;

use App\Entity\Region;
use App\Form\RegionType;
use App\Repository\RegionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegionController extends AbstractController
{
    /**
     * @Route("/region/{id}", name="region_public_show", methods={"GET"})
     * @param $id
     * @return Response
     */
    public function publicShow($id){
        $region = $this->getDoctrine()->getRepository(Region::class)->find($id);
        return $this->render('region/frontoffice/rooms.html.twig', [
            'region' => $region,
            'rooms' => $region->getRooms(),
        ]);
    }

    /**
     * @Route("/region/", name="region_public_index", methods={"GET"})
     */
    public function publicIndex(){
        return $this->render('region/frontoffice/index_public.html.twig', [
            'regions' => $this->getDoctrine()->getRepository(Region::class)->findAll(),
        ]);
    }

    /**
     * @Route("/backoffice/region/", name="region_index", methods={"GET"})
     */
    public function index(RegionRepository $regionRepository): Response
    {
        return $this->render('region/backoffice/index.html.twig', [
            'regions' => $regionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/backoffice/region/new", name="region_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $region = new Region();
        $form = $this->createForm(RegionType::class, $region);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($region);
            $entityManager->flush();

            return $this->redirectToRoute('region_index');
        }

        return $this->render('region/backoffice/new.html.twig', [
            'region' => $region,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/backoffice/region/{id}", name="region_show", methods={"GET"})
     */
    public function show(Region $region): Response
    {
        return $this->render('region/backoffice/show.html.twig', [
            'region' => $region,
        ]);
    }

    /**
     * @Route("/backoffice/region/{id}/edit", name="region_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Region $region): Response
    {
        $form = $this->createForm(RegionType::class, $region);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('region_index');
        }

        return $this->render('region/backoffice/edit.html.twig', [
            'region' => $region,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/backoffice/region/{id}", name="region_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Region $region): Response
    {
        if ($this->isCsrfTokenValid('delete'.$region->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($region);
            $entityManager->flush();
        }

        return $this->redirectToRoute('region_index');
    }
}
