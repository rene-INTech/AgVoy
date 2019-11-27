<?php


namespace App\Controller;


use App\Entity\Owner;
use App\Entity\Room;
use App\Form\OwnerType;
use App\Form\RoomType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OwnerController extends AbstractController
{
    /**
     * @Route("/owner/edit", name="owner_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_OWNER')")
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request): Response
    {
        $owner = $this->getUser()->getOwner();
        $form = $this->createForm(OwnerType::class, $owner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('owner_show_me');
        }

        return $this->render('owner/edit.html.twig', [
            'owner' => $owner,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/owner/{id}", name="owner_show",methods={"GET"})
     * @param $id
     * @return Response
     */
    public function show($id) : Response{
        $owner = $this->getDoctrine()->getRepository(Owner::class)->find($id);
        $isMe = $owner->getUser() === $this->getUser();
        return $this->render('owner/show.html.twig',[
            'owner' => $owner,
            'isMe' => $isMe,
        ]);
    }

    /**
     * @Route("/owner", name="owner_show_me",methods={"GET"})
     * @Security("is_granted('ROLE_OWNER')")
     * @return Response
     */
    public function showMe() : Response{
        return $this->redirectToRoute('owner_show', [
            'id' => $this->getUser()->getOwner()->getId(),
        ]);
    }
}