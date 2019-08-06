<?php

namespace BookBundle\Controller;

use BookBundle\BookBundle;
use BookBundle\Entity\Book;
use BookBundle\Form\BookType;
use BookBundle\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PostBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    /**
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $data = $this->getDoctrine()->getRepository(Book::class)->getAllBooks(1, 10);

        $paginator = $this->get('knp_paginator');
        $books = $paginator->paginate(
            $data,
            $request->query->getInt('page',1 ),
            $request->query->getInt('limit', 10)

        );

        return $this->render('BookBundle:Default:index.html.twig', [
            'books' => $books,
            'outSource' => false,
        ]);
    }

    /**
     * @Route("/book{id}", name="book_details")
     *
     * @param Request $request
     * @return Response
     */
    public function clickAction(Request $request)
    {
        $bookId = $request->attributes->get('id');
        $user =$this->getUser();
        $bookRepository = $this->getDoctrine()->getRepository(Book::class);
        $postRepository = $this->getDoctrine()->getRepository(Post::class);
        $book = $bookRepository->find($bookId);
        $reviews = $postRepository->getBookReviews($bookId);
        if(isset($user)) {
            $userId = $this->getUser()->getId();

            $hasUserWrittenPost = $postRepository->checkIfUserWrotePost($userId, $bookId);

        } else {
            $hasUserWrittenPost = false;
        }

        return $this->render('BookBundle:Book:bookDetails.html.twig',
            [
                'book' => $book,
                'reviews' => $reviews,
                'postWritten' => $hasUserWrittenPost
            ]);
    }

    /**
     * @Route("/admin", name="admin_page")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addBookAction(Request $request)
    {

        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /**
             * @var UploadedFile $file
             */
            $file = $book->getImage();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();

            $file->move(
                $this->getParameter('image_directory')
                , $fileName
            );

            $book->setImage($fileName);
            $book->setReviews(new ArrayCollection());
            $bookRepository = $this->getDoctrine()->getRepository(Book::class);
            $bookRepository->addBook($book);
        }

        return $this->render('BookBundle:Book:addBook.html.twig',
            [
                'form' =>$form->createView()
            ]);
    }

    /**
     * @param $isbn
     *
     * @return Book|null
     *
     * @throws GuzzleException
     */
    private function autoloadBook($isbn)
    {


        $client = new Client();
        $url = "https://www.googleapis.com/books/v1/volumes?q=isbn:" . $isbn;
        if (isset($isbn)) {
            $response = $client->request("GET", $url);
            $statusCode = $response->getStatusCode();
            if ($statusCode = Response::HTTP_OK) {

                $bookAsArray = json_decode($response->getBody()->getContents(), true);
                if (!empty($bookAsArray['items'][0])) {
                    $book = $this->createBookFromArray($bookAsArray['items'][0]);
//                    die(var_dump($book));
                    return $book;
                } else {
                    return null;
                }

            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    private function createBookFromArray($bookAsArray)
    {
        $book = new Book();
        $book->setReviews(new ArrayCollection());
        if (isset($bookAsArray['volumeInfo']['title'])) {
            $book->setTitle($bookAsArray['volumeInfo']['title']);
        }
        if (isset($bookAsArray['volumeInfo']['authors'])) {
            $book->setAuthor($bookAsArray['volumeInfo']['authors'][0]);
        }
        if (isset($bookAsArray['volumeInfo']['pageCount'])) {
            $book->setPageCount($bookAsArray['volumeInfo']['pageCount']);
        }
        if (isset($bookAsArray['volumeInfo']['averageRating'])) {
            $book->setRating($bookAsArray['volumeInfo']['averageRating']);
        }
        if (isset($bookAsArray['volumeInfo']['categories'][0])) {
            $book->setCategory($bookAsArray['volumeInfo']['categories'][0]);
        } else {
            $book->setCategory("undefined");
        }
        if (isset($bookAsArray['searchInfo']['textSnippet'])) {
            $book->setSummary($bookAsArray['searchInfo']['textSnippet']);
        }

        return $book;
    }

    /**
     * @Route("/autoload", name="autoload_book")
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws GuzzleException
     */
    public function autoloadBookAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('isbn', TextType::class, [
                'attr' => [
                    'pattern' => '[0-9]*'
                ]
            ])
            ->add('find', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->getForm();

        $autoLoadForm = $request->get('form');
        $isbn = $autoLoadForm['isbn'];
        $book = null;
        $successful = null;
        if (isset($isbn)) {

            $book = $this->autoloadBook($isbn);
            if (isset($book)) {
                $bookRepository = $this->getDoctrine()->getRepository(Book::class);
                $successful = $bookRepository->addBook($book);
//                die(var_dump($successful));
            }
        }


        return $this->render('BookBundle:Book:autoloadBook.html.twig',
            [
                'form' => $form->createView(),
                'book' => $book,
                'successful' => $successful
            ]);
    }
}
