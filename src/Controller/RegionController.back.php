<?php
//
//namespace App\Controller;
//
//use App\Entity\Region;
//use App\Entity\Room;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Routing\Annotation\Route;
//
//class RegionController extends AbstractController
//{
//    /**
//     * @Route("/region.back", name="region_index")
//     */
//    public function index() //Affiche la liste de toutes les régions de la base de données
//    {
//        $regions = $this->getDoctrine()->getRepository(Region::class)->findAll(); //récupère tous les objets de type 'Region'
//
//        return $this->render('region/index.html.twig', [
//            'regions_list' => $regions,
//        ]);
//    }
//
//    /**
//     * @Route("/region.back/{id}", name="region.back")
//     * @param int $id
//     * @return Response
//     */
//    public function listRooms(int $id){ //Affiche la liste des chambres de la région identifiée par $id
//        $region = $this->getDoctrine()->getRepository(Region::class)->find($id); //récupère l'objet de type 'Region' ayant l'identifiant $id
//        $rooms = $region->getRooms();//récupère la liste des chambres de la region.back $region.back
//        return $this->render('region/rooms.html.twig', [
//            'region.back' =>$region,
//            'rooms_list' => $rooms,
//            ]);
//    }
//}
