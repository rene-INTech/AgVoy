<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Reservation;
use App\Entity\Room;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use DateTime;
use Doctrine\DBAL\Types\DateTimeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    /**
     * @Route("/reservation/", name="reservation_index", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/reservation/new/{id_room}", name="reservation_new_get", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id_room
     * @return Response
     */
    public function new(Request $request, $id_room): Response
    {
        $reservation = new Reservation();
        $room = $this->getDoctrine()->getRepository(Room::class)->find($id_room);

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'room' => $room,
        ]);
    }

    /**
     * @Route("/reservation/new/{id_room}", name="reservation_new_post", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id_room
     * @return Response
     */
    public function newPost(Request $request, $id_room): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reservation = new Reservation();
        $room = $this->getDoctrine()->getRepository(Room::class)->find($id_room);
        $client = $this->getUser()->getClient();

        //Si l'utilisateur n'est pas déjà client, il le devient
        if ($client == null) {
            $client = new Client();
            $entityManager->persist($client);
            $entityManager->flush();
            $this->getUser()->setClient($client);
        }

        $start = $request->request->get('start');
        $end = $request->request->get('end');
        $startTime = DateTime::createFromFormat('Y-m-d', $start);
        $endTime = DateTime::createFromFormat('Y-m-d', $end);

        $reservation->setRoom($room);
        $reservation->setClient($client);
        $reservation->setDateDebut($startTime);
        $reservation->setDateFin($endTime);

        $entityManager->persist($reservation);
        $entityManager->flush();
        $this->get('session')->getFlashBag()->add('message', "Votre réservation a bien été prise en compte");

        return $this->redirectToRoute('public_room_show', [
            'id' => $id_room,
        ]);
    }

    /**
     * @Route("/client/reservations", name="reservation_show_mines", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function showMyReservations(): Response
    {
        $user = $this->getUser();
        $reservations = null;
        if ($user) {
            $client = $user->getClient();
            if ($client) {
                $reservations = $client->getReservations();
            }
        }
        return $this->render('reservation/frontoffice/mines.twig', [
            'reservations' => $reservations,
        ]);
    }

    /**
     * @Route("/reservation/{id}", name="reservation_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Reservation $reservation): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        if(in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            return $this->redirectToRoute('reservation_index');
        }
        return $this->redirectToRoute('reservation_show_mines');
    }

    /**
     * @Route("/reservation/{id}", name="reservation_show", methods={"GET"})
     */
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * @Route("/reservation/{id}/edit", name="reservation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Reservation $reservation): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('reservation_index');
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }
}
