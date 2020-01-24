<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class MainAppController extends Controller
{
    /**
     * @Route("/", name="main_app")
     */
    public function index()
    {
        return $this->render('main_app/index.html.twig', [
            'controller_name' => 'MainAppController',
        ]);
    }
}
