<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController
{
    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function index()
    {
        return new Response('hello world');
    }
}
