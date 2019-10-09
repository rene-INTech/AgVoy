<?php

namespace App\Controller;

use App\Entity\Region;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RegionController extends AbstractController
{
    /**
     * @Route("/region", name="region")
     */
    public function index()
    {
        $regions = $this->getDoctrine()->getRepository(Region::class)->findAll();

        return $this->render('region/index.html.twig', [
            'regions_list' => $regions,
        ]);
    }
}
