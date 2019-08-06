<?php

namespace PostBundle\Controller;

use BookBundle\Entity\Book;
use PostBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;

/**
 * Class PostController
 * @package PostBundle\Controller
 */
class PostController extends Controller
{

    /**
     * @Route("/writePost{bookId}", name="write_post")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function writePostAction(Request $request)
    {
        $bookId = $request->attributes->get('bookId');

        $form = $this->createFormBuilder()
            ->add('review', TextareaType::class, [
                'attr' => [
                    'maxlength' => '1000',
                    'rows' => '6',
                    'cols' => '100',
                    'class' => 'reviewBox',
                    'pattern' => '[a-zA-Z0-9]*'
                ]
            ])
            ->add('post', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->add('bookId', HiddenType::class, [
                'data' => $bookId
            ])
            ->getForm();

        return $this->render('PostBundle:Post:writePost.html.twig',
            [
                'form' => $form->createView()
            ]);
    }

    /**
     * @Route("/addPost", name="add_post")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addPostAction(Request $request)
    {
        $userId = $this->getUser()->getId();
        $formData = $request->get('form');
        $reviewText = $formData['review'];
        $bookId = (int)$formData['bookId'];
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $bookRepository = $this->getDoctrine()->getRepository(Book::class);
        $book = $bookRepository->find($bookId);

        $user = $userRepository->find($userId);
        $success  =$this->getDoctrine()->getRepository(Post::class)->addPost(
            $reviewText,
            $user,
            $book
        );

        return $this->redirectToRoute('book_details', [
            'id' => $bookId
        ]);
    }

    /**
     * @Route("/updateHeplful{postId}{bookId}", name="update_helpful")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function updateHelpfulAction(Request $request)
    {
        $postId = (int)$request->get('postId');
        $bookId = (int)$request->get('bookId');
        $postRepository = $this->getDoctrine()->getRepository(Post::class);
        $success =   $postRepository->helpful($postId);
        $userId = $this->getUser()->getId();

        $postRepository->registerVote($postId, $userId);
        return $this->redirectToRoute('book_details', [
                'id' => $bookId
            ]);
    }

    /**
     * @Route("/deletePost{postId}{bookId}", name="delete_post")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePostAction(Request $request)
    {

        $postId = (int)$request->get('postId');
        $bookId = (int)$request->get('bookId');

        $postRepository = $this->getDoctrine()->getRepository(Post::class);
        $success =   $postRepository->deletePostById($postId);

        return $this->redirectToRoute('book_details', [
            'id' => $bookId
        ]);
    }
}