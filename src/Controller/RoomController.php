<?php

namespace App\Controller;

use App\Entity\Room;
use mysql_xdevapi\Table;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Sodium\add;

class RoomController extends AbstractController
{
    /**
     * @Route("/owner/room/{id}", name="room_show")
     * @param $id
     * @return Response
     */
    public function showRoom($id) //Affiche les caractéristiques d'une chambre visibles par son propriétaire
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
    public function showRoomPublic($id) //Affiche les caractéristiques d'une chambre pour le public
    {
        $room = $this->getDoctrine()->getRepository(Room::class)->find($id);
        $likes = $this->get('session')->get('likes');
        if( $likes != null) {
            $liked = in_array($id, $likes);
        }else{
            $liked = false;
        }

        return $this->render('room/public.html.twig', [
            'room' => $room,
            'liked' => $liked,
        ]);
    }

    /**
     * @Route("/room/like/{id}", name="room_like")
     * @param $id
     * @return RedirectResponse
     */
    public function like($id){
        $likes = $this->get('session')->get('likes');
        if($likes==null){
            $likes=array();
        }
        // si l'identifiant n'est pas présent dans le tableau des likes, l'ajouter
        if (! in_array($id, $likes) ) {
            $likes[] = $id;
        }
        else {// sinon, le retirer du tableau
            $likes = array_diff($likes, array($id));
        }
        $this->get('session')->set('likes', $likes);

        return $this->redirectToRoute('public_room_show', ['id'=>$id]);
    }
}
