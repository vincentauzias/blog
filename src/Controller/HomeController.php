<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(PostRepository $ripo): Response
    {
        $posts = $ripo->findAll();
        
        return $this->render('home/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/posts/{id}", name="show_post")
     */
    public function show(Post $post, Request $request, EntityManagerInterface $em)
    {
        $comment = new Comment();

        $form = $this->createFormBuilder($comment)
            ->add('pseudo', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('content', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->getForm();

            $comment->setCreatedAt(new \DateTime());
            $comment->setPost($post);

            // vient automatiquement alimenter objet $comment via $form lorsqu'il y a des post ou des get effectués via le formulaire
            $form->handleRequest($request);

            // vérifier la soumission du formulaire et si il est valide
            if($form->isSubmitted() && $form->isValid()) {
                $em->persist($comment);
                $em->flush();
                // dd($comment);
            }

        return $this->render('home/post.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }
}
