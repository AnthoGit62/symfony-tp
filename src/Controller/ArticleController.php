<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/article/creer', name: 'app_article_create')]
    public function create(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, #[Autowire('%kernel.project_dir%/public/uploads/brochures')] string $brochuresDirectory): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            
            $imageFile = $form->get('image')->getData();
            $article = $form->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move($brochuresDirectory, $newFilename);
                } catch (FileException $e) {

                }

                $article->setNomImage($newFilename);
            }

            $entityManager->persist($article);

            $entityManager->flush();

            $this->addFlash(
                'success',
                'Article n° ' . $article->getId() . ' créer !'
            );

        }

        return $this->render('article/creer.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/article/liste', name: 'app_article_view')]
    public function view(EntityManagerInterface $entityManager): Response
    {
        
        $article = $entityManager->getRepository(Article::class)->findAll();

        return $this->render('article/liste.html.twig', [
            'controller_name' => 'ArticleController',
            'titre' => 'Article',
            'article' => $article,
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/article/maj/{id}', name: 'app_article_update')]
    public function update(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $article = $entityManager->getRepository(Article::class)->find($id);

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            $entityManager->persist($article);

            $entityManager->flush();

            $this->addFlash(
                'success',
                'Article n° ' . $article->getId() . ' modifié !'
            );

        }

        return $this->render('article/maj.html.twig', [
            'form' => $form,
        ]);
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/article/sup/{id}', name: 'app_article_delete')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        
        $article = $entityManager->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'Aucun article avec cette ID '.$id
            );
        }

        
        $entityManager->remove($article);
        $entityManager->flush();
        
        

        return $this->redirectToRoute('app_article_view');
    }




    #[Route('/article/register', name: 'app_article_register')]
    public function register(Request $request, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            $entityManager->persist($article);

            $entityManager->flush();



        }
    }

  
    

}

