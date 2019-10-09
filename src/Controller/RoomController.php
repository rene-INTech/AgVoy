<?php

namespace App\Controller;

use App\Entity\Room;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoomController extends AbstractController
{
    /**
     * @Route("/owner/room/{id}", name="room_show")
     * @param $id
     * @return Response
     */
    public function showRoom($id)
    {
        return $this->render('room/private.html.twig', [
            'room' => $this->getDoctrine()->getRepository(Room::class)->find($id),
        ]);
    }

    /**
     * @Route("/room/{id}", name="public_room_show")
     * @param $id
     * @return Response
     */
    public function showRoomPublic($id)
    {
        return $this->render('room/public.html.twig', [
            'room' => $this->getDoctrine()->getRepository(Room::class)->find($id),
        ]);
    }
}
