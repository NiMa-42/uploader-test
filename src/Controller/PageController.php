<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController
{

    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index()
    {
        return $this->render('main.html.twig');
    }
}
