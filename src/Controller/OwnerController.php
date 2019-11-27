<?php


namespace App\Controller;


use App\Entity\Owner;
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

}