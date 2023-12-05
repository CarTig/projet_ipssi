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
use Symfony\Component\HttpFoundation\File\Exception\FileException;



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
            
                $imageFile = $form->get('image')->getData();
    
                // this condition is needed because the 'brochure' field is not required
                // so the PDF file must be processed only when a file is uploaded
                if ($imageFile) {
                    $newFilename = uniqid().'.'.$imageFile->guessExtension();
        
                    // Move the file to the directory where brochures are stored
                    try {
                        $imageFile->move(
                            $this->getParameter('upload_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                        $this->addFlash('danger', 'Impossible d\'ajouter l\'image du logo');
                    }
        
                    // updates the 'brochureFilename' property to store the PDF file name
                    // instead of its contents
                    $marque->setImage($newFilename);
                }

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
        
        #[Route('/delete/{id}', name:'delete_marque')]
        public function delete( Marque $marque = null , EntityManagerInterface $em ){
            if($marque == null) {
            $this->addFlash('danger','Marque introuvable');
            return $this->redirectToRoute('app_marque');
            }
 
        $em->remove($marque);
        $em->flush();
 
        $this->addFlash('warning','Marque Supprimée');
        return $this->redirectToRoute('app_marque');
        }

    }
      


