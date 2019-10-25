<?php

namespace App\Controller;

use App\Entity\Room;
use App\Form\RoomType;
use App\Repository\RoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoomController extends AbstractController
{
    /**
     * @Route("/owner/room/", name="room_index", methods={"GET"})
     */
    public function index(RoomRepository $roomRepository): Response
    {
        return $this->render('room/backoffice/index.html.twig', [
            'rooms' => $roomRepository->findAll(),
        ]);
    }

    /**
     * @Route("/owner/room/new", name="room_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($room);
            $entityManager->flush();

            return $this->redirectToRoute('room_index');
        }

        return $this->render('room/backoffice/new.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/owner/room/{id}", name="room_show", methods={"GET"})
     */
    public function show(Room $room): Response
    {
        return $this->render('room/backoffice/show.html.twig', [
            'room' => $room,
        ]);
    }

    /**
     * @Route("/owner/room/{id}/edit", name="room_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Room $room): Response
    {
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('room_index');
        }

        return $this->render('room/backoffice/edit.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="room_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Room $room): Response
    {
        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($room);
            $entityManager->flush();
        }

        return $this->redirectToRoute('room_index');
    }

//    /**
//     * @Route("/owner/room/{id}", name="room_show")
//     * @param $id
//     * @return Response
//     */
//    public function showRoom($id) //Affiche les caractéristiques d'une chambre visibles par son propriétaire
//    {
//        return $this->render('room/private.html.twig', [
//            'room' => $this->getDoctrine()->getRepository(Room::class)->find($id),
//        ]);
//    }

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


    /**
     * @Route("/room/book/{id}", name="room_book")
     * @param $id
     * @return RedirectResponse
     */
    public function book($id){
        return $this->redirectToRoute('reservation_new', [
            'id_room'=>$id,
        ]);
    }

    /**
     * @Route("/room/liked", name="liked_rooms")
     * @return Response
     */
    public function listLiked(){
        $liked = $this->get('session')->get('likes');
        $rooms = $this->getDoctrine()->getRepository(Room::class)->findBy(["id"=>$liked]);
        return $this->render('room/frontoffice/liked.html.twig',[
            'rooms' => $rooms,
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

        return $this->render('room/frontoffice/show.html.twig', [
            'room' => $room,
            'liked' => $liked,
        ]);
    }
}
