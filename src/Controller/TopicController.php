<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\SubCategories;
use App\Entity\Topics;
use App\Repository\SubCategoriesRepository;
use App\Repository\TopicResponsesRepository;
use App\Repository\TopicsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TopicController extends AbstractController
{

    #[Route('/forum/{catId}/{subId}/{id}', name:'app_topic_show')]
    public function listTopic(
        $catId, 
        $subId, 
        Topics $topic, 
        TopicsRepository $topicRepository, 
        TopicResponsesRepository $responseRepository, 
        PaginatorInterface $paginator,
        Request $request
        ): Response 
    {

        $messages = $paginator->paginate(
            $responseRepository->findBy(['topic' => $topic->getId()]),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/topic/show.html.twig',[
            'responses' => $messages,
            'subject' => $topicRepository->findOneBy(['id' => $topic->getId()])
        ]);
    }

    #[Route('/forum/{id}', name: 'app_cat_show')]
    public function showCat(Categories $category, SubCategoriesRepository $repository): Response
    {
        return $this->render('pages/topic/cat.html.twig', [
            'catTitle' => $category->getTitle(),
            'catId' => $category->getId(),
            'subCategories' => $repository->findBy(['category' => $category->getId() ])
        ]);
    }

    #[Route('/forum/{catId}/{id}', name: 'app_sub_show')]
    public function showSub($catId , SubCategories $sub, TopicsRepository $repository, PaginatorInterface $paginator, Request $request): Response {

        $topics = $paginator->paginate(
            $repository->findBy(['subCategory' => $sub->getId()]),
            $request->query->getInt('page', 1),
            10
        );
        
        return $this->render('pages/topic/sub.html.twig', [
            'topics' => $topics,
            'subCatTitle' => $sub->getTitle(),
            'subCatId' => $sub->getId(),
            'catId' => $catId
        ]);
    }
}
