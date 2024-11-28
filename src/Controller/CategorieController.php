<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\ArticleType;
use App\Form\CategorieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'app_categorie')]
    public function index(): Response
    {
        return $this->render('categorie/index.html.twig', [
            'controller_name' => 'CategorieController',
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/categorie/creer', name: 'app_categorie_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categorie();

        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            
            $categorie = $form->getData();

            $entityManager->persist($categorie);

            $entityManager->flush();

            $this->addFlash(
                'success',
                'Categorie n° ' . $categorie->getId() . ' créer !'
            );

        }

        return $this->render('categorie/creer.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/categorie/liste', name: 'app_categorie_view')]
    public function view(EntityManagerInterface $entityManager): Response
    {
        
        $categorie = $entityManager->getRepository(Categorie::class)->findAll();

        return $this->render('categorie/liste.html.twig', [
            'controller_name' => 'CategorieController',
            'titre' => 'Categorie',
            'categorie' => $categorie,
        ]);
    }
    
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/categorie/maj/{id}', name: 'app_categorie_update')]
    public function update(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $categorie = $entityManager->getRepository(Categorie::class)->find($id);

        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $categorie = $form->getData();

            $entityManager->persist($categorie);

            $entityManager->flush();

            $this->addFlash(
                'success',
                'Categorie n° ' . $categorie->getId() . ' modifié !'
            );

        }

        return $this->render('categorie/maj.html.twig', [
            'form' => $form,
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/categorie/sup/{id}', name: 'app_categorie_delete')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        
        $categorie = $entityManager->getRepository(Categorie::class)->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException(
                'Aucun article avec cette ID '.$id
            );
        }

        
        $entityManager->remove($categorie);
        $entityManager->flush();
        
        

        return $this->redirectToRoute('app_categorie_view');
    }
}