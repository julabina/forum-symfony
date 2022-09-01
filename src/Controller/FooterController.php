<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FooterController extends AbstractController
{
    #[Route('/a-propos', name: 'app_footer_about')]
    public function showAbout(): Response
    {
        return $this->render('pages/footer/about.html.twig', [
            
        ]);
    }
}
