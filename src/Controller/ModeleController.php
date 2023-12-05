<?php

namespace App\Controller;

use App\Entity\Modele;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/modele')]
class ModeleController extends AbstractController
{
    #[Route('/', name: 'app_modele')]
    public function index(EntityManagerInterface $em , Request $request): Response
        {
            $modele = new Modele();
            $form = $this->createForm(ModeleType::class, $modele);

            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
            // le formulaire a été soumis et est validé
            $em->persist($modele); // prépare la sauvgarde
            $em->flush(); // executer

            $this->addFlash('succes', 'Modèle ajouté');
        }

            $modeles = $em->getRepository(Modele::class)->findAll();
            return $this->render('modele/index.html.twig', [
                'modele' => $modeles,
                'ajout' => $form->createView(),
            ]);
        }

        #[Route('/{id}', name: 'modele')]
        public function marque( Modele $modele, Request $request, EntityManagerInterface $em ): Response
        {

        if($modele == null) {
            $this->addFlash('danger','Modele introuvable');
            return $this->redirectToRoute('app_modele');
        }
        $form = $this->createForm(ModeleType::class, $modele);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
        $em->persist($modele);
            $em->flush();
            $this->addFlash('success','Modele mis a jour');
        }

        return $this->render("modele/show.html.twig" , [
            'marque' => $modele,
            'edit' => $form -> createView()
        ]);
    }
}