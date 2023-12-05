<?php

namespace App\Controller;

use App\Entity\Marque;
use App\Form\MarqueType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



    #[Route('/marque')]
    class MarqueController extends AbstractController
    {

        #[Route('/', name: 'app_marque')]
        public function index(EntityManagerInterface $em , Request $request): Response
        {
            $marque = new Marque();
            $form = $this->createForm(MarqueType::class, $marque);

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
            // le formulaire a été soumis et est validé
            $em->persist($marque); // prépare la sauvgarde
            $em->flush(); // executer

            $this->addFlash('succes', 'Marque ajoutée');
        }

            $marques = $em->getRepository(Marque::class)->findAll();
            return $this->render('marque/index.html.twig', [
                'marque' => $marques,
                'ajout' => $form->createView(),
            ]);
        }

        #[Route('/{id}', name: 'marque')]
        public function marque( Marque $marque, Request $request, EntityManagerInterface $em ): Response
        {

        if($marque == null) {
            $this->addFlash('danger','Marque introuvable');
            return $this->redirectToRoute('app_marque');
        }
        $form = $this->createForm(MarqueType::class, $marque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
        $em->persist($marque);
            $em->flush();
            $this->addFlash('success','Marque mise a jour');
        }

        return $this->render("marque/show.html.twig" , [
            'marque' => $marque,
            'edit' => $form -> createView()
        ]);
    }
    }
      


