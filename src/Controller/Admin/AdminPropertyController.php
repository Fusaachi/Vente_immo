<?php

namespace App\Controller\Admin;

use App\Entity\Property;
use App\Form\PropertyFormType;
use App\Repository\PropertyRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdminPropertyController extends AbstractController
{
    /**
     * @var PropertyRepository
     */
    private $repository;
    private $entityManager;
    public function __construct(PropertyRepository $repository, ManagerRegistry $em)
    {
        $this->repository = $repository;
        $this->entityManager = $em->getManager();
    }
    /**
     * 
     * @Route("/admin", name="admin.property.index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $properties = $this->repository->findAll(); // Je veux récupérer tous mes biens 
        return $this->render('admin/property/index.html.twig', compact("properties")); // compact va me permettre d'envoyer un tableau 
    }

      /**
       *
       * @Route("/admin/property/create", name="admin.property.new")
       */
    public function new(Request $request) // On lui injecte la requête
     {
        $property = new Property(); // Entité créé de façon manuelle donc non suivi par l'EntityManager++
        $form = $this->createForm(PropertyFormType::class, $property);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $this->entityManager->persist($property); // Avant de flush nous voulons persister cette nouvelle propriété ainsi elle seras traquée par l'EntityManager qui seras en mesure de faire les changements
            $this->entityManager->flush();
            $this->addFlash('success','Créé avec succès');
            return $this->redirectToRoute('admin.property.index');
        }

        return $this->render('admin/property/new.html.twig',[
            'property' => $property,// les propriétes
            'form'     => $form->createView()//le Form
        ]);
    }

    /**
     * @Route ("/admin/property/{id}", name="admin.property.edit",methods="GET|POST")
     * @param Property $property
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Property $property, Request $request)
    {
        $form = $this->createForm(PropertyFormType::class, $property);
        // La var $property contient nos données(tab ou entity ici ce sont nos entités qui ont toutes les informations nécessaire pour remplir le formulaire)
        $form->handleRequest($request); // Nous demandons à notre Form de gerer la Request
        if ($form->isSubmitted() && $form->isValid()) // est-ce que le Form à été envoyé et est-ce qu'il est valide.
        {
            $this->entityManager->flush(); // Apporter les infos au niveau de la BDD
            $this->addFlash('success', 'Modifié avec succès');
            return $this->redirectToRoute('admin.property.index');
        }
        return $this->render('admin/property/edit.html.twig', [
            'property' => $property,
            'form' => $form->createView() // envoi du $form avec l'objet createView à notre template via la variable form
        ]);
    }

    /**
     * @Route("/admin/property/delete/{id}", name ="admin.property.delete", methods="GET|POST")
     * @param Property $property
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Property $property, Request $request)
    {
        if($this->isCsrfTokenValid('delete' .$property->getId(), $request->get('_token'))) //On vérifie si notre Token générée automatiquement est valide
        {
        $this->entityManager->remove($property);
        $this->entityManager->flush();
        $this->addFlash('success', 'Supprimé avec succès');
        }
        return $this->redirectToRoute('admin.property.index');

    }

}