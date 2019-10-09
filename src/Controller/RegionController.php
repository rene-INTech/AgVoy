<?php

namespace App\Controller;

use App\Entity\Region;
use App\Entity\Room;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegionController extends AbstractController
{
    /**
     * @Route("/region", name="region_index")
     */
    public function index()
    {
        $regions = $this->getDoctrine()->getRepository(Region::class)->findAll();

        return $this->render('region/index.html.twig', [
            'regions_list' => $regions,
        ]);
    }

    /**
     * @Route("/region/{id}", name="region")
     * @param int $id
     * @return Response
     */
    public function listRooms(int $id){
        $region = $this->getDoctrine()->getRepository(Region::class)->find($id);
        $rooms = $region->getRooms();
        return $this->render('region/rooms.html.twig', [
            'region' =>$region,
            'rooms_list' => $rooms,
            ]);
    }
}
