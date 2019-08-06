<?php

namespace BookBundle\Controller;

use Beta\B;
use BookBundle\Entity\Book;
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Client;
use PostBundle\Entity\Poem;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class SearchController
 * @package BookBundle\Controller
 */
class SearchController extends Controller
{
    public function searchBarAction()
    {
        $form = $this->createFormBuilder(null)
            ->add('query', TextType::class, [
                'attr' => [
                    'pattern' => '[a-zA-Z0-9]*'
                    ]
                ])
            ->add('search', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                    ]
                ])
            ->add('searchBy', ChoiceType::class, [
                'choices' => [
                    'Title' => 'title',
                    'Author' => 'author',
                    'Category' => 'category',
                    'Poem' => 'poem'
                ]
            ])
            ->getForm();
        return $this->render('BookBundle:Search:search.html.twig',
            [
                'form' => $form->createView()
            ]);
    }

    /**
     * @Route("/search", name="search")
     *
     * @param Request $request
     *
     */
    public function handleSearchAction(Request $request)
    {
        $formData = $request->get('form');
        $searchTerm = $formData['query'];
        $searchBy = $formData['searchBy'];
            if ($searchBy === "poem") {
                $paginator = $this->get('knp_paginator');

                $data = $this->handlePoemSearch($searchTerm);

    //            $poems = $paginator->paginate(
    //                $data,
    //                $request->query->getInt('page', 1),
    //                10
    //            );
                return $this->render('BookBundle:Default:poems.html.twig', [
                    'poems' => $data
                ]);
            }

        $data = $this->getDoctrine()->getRepository(Book::class)->searchBooks($searchTerm, $searchBy);
        $outSource = false;
        if (empty($data)) {
            $data = $this->searchOutsource($searchTerm);
            $outSource = true;
        }

        $paginator = $this->get('knp_paginator');
        $books = $paginator->paginate(
            $data,
            $request->query->getInt('page',1 ),
            $request->query->getInt('limit', 10)

        );
        return $this->render('BookBundle:Default:index.html.twig', [
            'books' => $books,
            'outSource' =>$outSource,
        ]);
    }

    private function handlePoemSearch($searchTerm)
    {
        $client = new Client();
        if (empty($searchTerm)){
            return null;
        }
        $uri = "http://poetrydb.org/title/" . $searchTerm;

        $response = $client->request("GET", "$uri");
        $statusCode = $response->getStatusCode();
        if ($statusCode = Response::HTTP_OK)
        {
            $array = json_decode($response->getBody()->getContents(), true);
            if (!empty($array)) {
              $poems =  $this->handlePoemData($array);
              return $poems;
            } else {
                return null;
            }

        } else {
            return null;
        }
    }

    private function handlePoemData($data)
    {
        $poems = new ArrayCollection();
        foreach ($data as $item) {
            $poem = new Poem();

            if (isset($item['title'])) {
                $poem->setTitle($item['title']);
            }
            if (isset($item['author'])) {
                $poem->setAuthor($item['author']);
            }
            if (!empty($item['lines'])) {
                $content = '';
                foreach ($item['lines'] as $line) {
                    $content = $content . $line;
                }

                $poem->setContent($content);
            }

            $poems->add($poem);
        }

        return $poems;
    }

    private function searchOutsource($searchTerm)
    {
        $client = new Client();
        $response = $client->request("GET", "https://www.googleapis.com/books/v1/volumes", [
            'query' => [
                'q' => $searchTerm
            ]
        ]);
        $array = json_decode($response->getBody()->getContents(), true);

        if (!empty($array['items'])) {

            return $books = $this->handleOutsourceData($array['items']);
        }
    }

    private function handleOutsourceData($data)
    {
        $books = new ArrayCollection();
        $iterator = 0;
        foreach ($data as $item)
        {
            $book = new Book();
            $book->setTitle($item['volumeInfo']['title']);

            if (isset($item['volumeInfo']['description'])) {

                $book->setSummary($item['volumeInfo']['description']);
            }

            if (isset($item['volumeInfo']['authors'])) {

                $book->setAuthor($item['volumeInfo']['authors'][0]);
            }

            if (isset($item['volumeInfo']['averageRating'])) {
                $book->setRating($item['volumeInfo']['averageRating']);
            }

            if (isset($item['volumeInfo']['categories'])) {
                $book->setCategory($item['volumeInfo']['categories'][0]);
            }

            if (isset($item['volumeInfo']['pageCount']))
            {
                $book->setPageCount($item['volumeInfo']['pageCount']);
            }

            $books->add($book);
            $iterator = $iterator + 1;
        }

        return $books;
    }

}
