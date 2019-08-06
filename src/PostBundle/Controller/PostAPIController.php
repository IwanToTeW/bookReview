<?php

namespace PostBundle\Controller;

use BookBundle\Entity\Book;
use FOS\RestBundle\Controller\FOSRestController;
use PostBundle\Entity\Post;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;

class PostAPIController extends FOSRestController
{
        /**
         * @param $postId
         *
         * @return Response
         */
        public function getPostsAction($postId)
        {
            $entityManager = $this->getDoctrine()->getManager();
            $post = $entityManager->getRepository(Post::class)->find($postId);

            if (!empty($post)) {
    //            $serializer = $this->container->get('jms_serializer');
    //            $json = $serializer->serialize($post, 'json');
                $view = $this->view($post, 200);

            } else {
                $view = $this->view(null, 404);

            }

            return $this->handleView($view);
        }

        /**
     *
     * @param $bookId
     *
     * @return Response
     */
    public function getBookPostsAction($bookId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $book = $entityManager->getRepository(Book::class)->find($bookId);

        if(!empty($book)) {
            $posts = $entityManager->getRepository(Post::class)->getBookReviews($bookId);
            if (!empty($posts)) {
                $view = $this->view($posts, 200);
            } else {
                $view = $this->view(null, 201);
            }

        } else {
            $view = $this->view(null, 404);
        }

        return $this->handleView($view);
    }

    /**
     * @param $postId
     *
     * @return Response
     */
    public function getPostsHelpfulAction($postId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $postsHelpful = $entityManager->getRepository(Post::class)->getPostsHelpful($postId);

        if (!empty($postsHelpful)) {
            $view = $this->view($postsHelpful, 200);

        } else {
            $view = $this->view(null, 404);

        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param $bookId
     * @param $authorId
     *
     * @return Response
     */
    public function postBookAuthorAction(Request $request, $bookId, $authorId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $book = $entityManager->getRepository(Book::class)->find($bookId);
        $user = $entityManager->getRepository(User::class)->find($authorId);

        if (isset($book) && isset($user))
        {
            $data = json_decode($request->getContent(), true);

            if(isset($data['post'])) {
                $successful = $entityManager->getRepository(Post::class)->addPost($data['post'], $user, $book);

                if($successful){
                    $view = $this->view(null, 201);
                } else {
                    $view = $this->view(null, 400);
                }
            } else {
                $view = $this->view(null, 400);
            }

        } else {
            $view = $this->view(null, 400);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param $postId
     *
     * @return Response
     */
    public function putPostsAction(Request $request, $postId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository(Post::class)->find($postId);

        if(isset($post)) {
            $data = json_decode($request->getContent(), true);
            $newText = $data["newText"];
            if (isset($newText)) {
                $entityManager->getRepository(Post::class)->updatePost($newText, $post);
                $view = $this->view(null, 201);
            } else {
                $view = $this->view(null, 400);
            }

        } else {
            $view = $this->view(null, 404);
        }

        return $this->handleView($view);
    }

    /**
     * @param $postId
     *
     * @return Response
     */
    public function deletePostsAction($postId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository(Post::class)->find($postId);

        if (isset($post)) {
            $successful = $entityManager->getRepository(Post::class)->deletePostById($postId);
            if ($successful) {
                $view = $this->view(null, 201);

            } else {
                $view = $this->view(null, 404);
            }

        } else {
            $view = $this->view(null, 404);
        }

        return $this->handleView($view);
    }
}
