<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * display home page
     *
     * @param CategoriesRepository $repository
     * @return Response
     */
    #[Route('/', name: 'app_home')]
    public function index(CategoriesRepository $repository): Response
    {
        return $this->render('pages/home/index.html.twig', [
           'categories' => $repository->findAll()
        ]);
    }
}
