<?php

namespace PostBundle\Controller;

use GuzzleHttp\Client;
use PostBundle\Entity\Poem;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        // add poem of the day
        $client = new Client();
        $response = $client->request("GET", "https://www.poemist.com/api/v1/randompoems");
        $array = json_decode($response->getBody()->getContents(), true);

        if (!empty($array)) {
            $poem = $this->handlePoemData($array[0]);
        }

        return $this->render('PostBundle:Default:index.html.twig',
            ['poem' => $poem]
        );
    }

    private function handlePoemData($poemData)
    {
        if(isset($poemData['title'])) {
            $poemTitle = ['title' => $poemData['title']];
        }
        if(isset($poemData['poet']['name'])) {
            $poemAuthor = ['poet' => $poemData['poet']['name']];
        }
        if (isset($poemData['content'])) {
            $poemContent = $poemData['content'];
        }

        $poem = new Poem();
        $poem->setTitle($poemTitle['title']);
        $poem->setAuthor($poemAuthor['poet']);
        $poem->setContent($poemContent);

        return $poem;
    }
}
