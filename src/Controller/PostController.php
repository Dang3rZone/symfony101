<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;

#[Route('/post', name: 'post.')]
class PostController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();

        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, PersistenceManagerRegistry $doctrine): Response
    {
        // create a new post with title
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // entity manager
            $em = $doctrine->getManager();

            $em->persist($post);
            $em->flush();

            return $this->redirect($this->generateUrl('post.index'));
        }

        // return a response
        return $this->render('post/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/show/{id}', name: 'show')]
    public function show(Post $post): Response
    {
        // create show view
        return $this->render('post/show.html.twig', [
                'post' => $post
            ]
        );
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Post $post, PersistenceManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        $em->remove($post);
        $em->flush();

        $this->addFlash('success', 'Post was deleted.');

        return $this->redirect($this->generateUrl('post.index'));
    }
}
