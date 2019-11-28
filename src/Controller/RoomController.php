<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Owner;
use App\Entity\Room;
use App\Entity\User;
use App\Form\RoomType;
use App\Repository\RoomRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RoomController extends AbstractController
{
    /**
     * @Route("/backoffice/room/", name="room_index", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     * @param RoomRepository $roomRepository
     * @return Response
     */
    public function index(RoomRepository $roomRepository): Response
    {
        return $this->render('room/backoffice/index.html.twig', [
            'rooms' => $roomRepository->findAll(),
        ]);
    }

    /**
     * @Route("/owner/room/new", name="room_new", methods={"GET","POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $room = new Room();
        $owner = $this->getUser()->getOwner();
        $newOwner = $owner == null;
        if($newOwner){ //Si l'utilisateur n'est pas déjà propriétaire
            $owner = new Owner();
            $entityManager->persist($owner);
            $entityManager->flush();
            $this->getUser()->setOwner($owner);
        }

        $room->setOwner($owner);
        $form = $this->createForm(RoomType::class, $room);
        $form->remove("owner");
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($room);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('message', "Votre annonce a bien été ajoutée");
            if($newOwner){
                $this->get('session')->getFlashBag()->add('message', 'Un profil de propriétaire vierge vient de vous être créé. Venez le compléter <a href="/owner/edit">ici</a>');
            }

            return $this->redirectToRoute('room_show_mines');
        }

        return $this->render('room/frontoffice/new.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/owner/room/list", name="room_show_mines", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function showMyRooms() :Response {
        $user = $this->getUser();
        //$rooms = $this->getUser()->getOwner()->getRooms();
        $rooms = null;
        if($user){
            $owner = $user->getOwner();
            if($owner){
                $rooms = $owner->getRooms();
            }
        }
        return $this->render('room/frontoffice/mines.html.twig',[
            'annonces' => $rooms,
        ]);
    }

    /**
     * @Route("/owner/room/{id}", name="room_show", methods={"GET"})
     * @Security("is_granted('ROLE_OWNER')")
     */
    public function show(Room $room): Response
    {
        $admin = in_array('ROLE_ADMIN', $this->getUser()->getRoles());
        $proprio = $this->getUser()->getOwner() === $room->getOwner();
        if(!$admin && !$proprio){
            return new BadRequestHttpException("Vous n'avez pas le droit de modifier cette annonce");
        }

        return $this->render('room/backoffice/show.html.twig', [
            'room' => $room,
        ]);
    }

    /**
     * @Route("/owner/room/{id}/edit", name="room_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_OWNER')")
     */
    public function edit(Request $request, Room $room): Response
    {
        $admin = in_array('ROLE_ADMIN', $this->getUser()->getRoles());
        $proprio = $this->getUser()->getOwner() === $room->getOwner();
        if(!$admin && !$proprio){
            return new BadRequestHttpException("Vous n'avez pas le droit de modifier cette annonce");
        }
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('room_index');
        }

        return $this->render('room/backoffice/edit.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
            'proprio' => $proprio,
        ]);
    }

    /**
     * @Route("/owner/room/{id}", name="room_delete", methods={"DELETE"})
     * @Security("is_granted('ROLE_OWNER')")
     */
    public function delete(Request $request, Room $room): Response
    {
        $admin = in_array('ROLE_ADMIN', $this->getUser()->getRoles());
        $proprio = $this->getUser()->getOwner() === $room->getOwner();
        if(!$admin && !$proprio){
            return new BadRequestHttpException("Vous n'avez pas le droit de supprimer cette annonce");
        }

        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($room);
            $entityManager->flush();
        }

        return $this->redirectToRoute('room_index');
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


    /**
     * @Route("/room/book/{id}", name="room_book")
     * @param $id
     * @return RedirectResponse
     */
    public function book($id){
        return $this->redirectToRoute('reservation_new_get', [
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
        $user = $this->getUser();
        $isMine = false;
        if($user){
            $owner = $user->getOwner();
            if($owner){
                $isMine = $owner === $room->getOwner();
            }
        }
        $likes = $this->get('session')->get('likes');
        if( $likes != null) {
            $liked = in_array($id, $likes);
        }else{
            $liked = false;
        }

        return $this->render('room/frontoffice/show.html.twig', [
            'room' => $room,
            'liked' => $liked,
            'isMine' => $isMine,
        ]);
    }
}

