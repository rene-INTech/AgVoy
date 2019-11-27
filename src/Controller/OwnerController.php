<?php


namespace App\Controller;


use App\Entity\Owner;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OwnerController extends AbstractController
{
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