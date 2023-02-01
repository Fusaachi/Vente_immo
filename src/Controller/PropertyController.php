<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PropertyController extends AbstractController
{
    #private $repository;





    #[Route('/biens', name: 'property.index')]
    public function index(): Response
    {
        return $this->render('property/index.html.twig',['controller_name' => 'PropertyController',]);
    }
}
