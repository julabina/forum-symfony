<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\SubCategories;
use App\Entity\Topics;
use App\Form\NewTopicType;
use App\Form\TopicResponseType;
use App\Repository\CategoriesRepository;
use App\Repository\SubCategoriesRepository;
use App\Repository\TopicResponsesRepository;
use App\Repository\TopicsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TopicController extends AbstractController
{

    #[Route('/forum', name:'app_forum_list')]
    public function listForum(CategoriesRepository $repository): Response {
        return $this->render('pages/topic/forumList.html.twig', [
            'categories' => $repository->findAll()
         ]);
    }

    #[Route('/forum/{catId}/{subId}/{id}', name:'app_topic_show')]
    public function showTopic(
        $catId, 
        $subId, 
        Topics $topic, 
        TopicsRepository $topicRepository, 
        TopicResponsesRepository $responseRepository, 
        CategoriesRepository $catRepository,
        SubCategoriesRepository $subRepository,
        PaginatorInterface $paginator,
        Request $request,
        EntityManagerInterface $manager,
        ): Response 
    {

        $cat = $catRepository->findOneBy(['id' => $catId]);
        $sub = $subRepository->findOneBy(['id' => $subId]);

        $form = $this->createForm(TopicResponseType::class);
        $form->handleRequest($request);

        $messages = $paginator->paginate(
            $responseRepository->findBy(['topic' => $topic->getId()]),
            $request->query->getInt('page', 1),
            10
        );

        if ($form->isSubmitted() && $form->isValid()) {
            
            $message = $form->getData();
            $message->setTopic($topic)
                ->setUser($this->getUser());
            $topic->setUpdatedAt(new \DateTimeImmutable());
                
            $manager->persist($message);
            $manager->persist($topic);
            $manager->flush();

            return $this->redirectToRoute('app_topic_show', ['catId' => $catId, 'subId' => $subId, 'id' => $topic->getId()]);
        }

        return $this->render('pages/topic/show.html.twig',[
            'responses' => $messages,
            'subject' => $topicRepository->findOneBy(['id' => $topic->getId()]),
            'catId' => $cat->getId(),
            'catTitle' => $cat->getTitle(),
            'subId' => $sub->getId(),
            'subTitle' => $sub->getTitle(),
            'form' => $form->createView()
        ]);
    }

    #[Route('/new/forum/{catId}/{subId}', name:'app_newTopic')]
    public function new(Categories $catId,SubCategories $subId, Request $request, EntityManagerInterface $manager): Response {

        $form = $this->createForm(NewTopicType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $topic = $form->getData();
            $topic->setIsActive(0)
                ->setSubCategory($subId)
                ->setUser($this->getUser())
            ;
            
            $manager->persist($topic);
            $manager->flush();

            return $this->redirectToRoute('app_sub_show', ['catId' => $catId->getId(), 'id' => $subId->getId() ]);
        }

        return $this->render("pages/topic/new.html.twig", [
            'form' => $form->createView()
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
    public function showSub($catId , SubCategories $sub, TopicsRepository $repository, CategoriesRepository $catRepository, PaginatorInterface $paginator, Request $request): Response {

        $cat = $catRepository->findOneBy(['id' => $catId]);

        $topics = $paginator->paginate(
            $repository->findBy(
                ['subCategory' => $sub->getId()], 
                ['updatedAt' => 'DESC']
            ),
            $request->query->getInt('page', 1),
            10
        );
        
        return $this->render('pages/topic/sub.html.twig', [
            'topics' => $topics,
            'subCatTitle' => $sub->getTitle(),
            'subCatId' => $sub->getId(),
            'catId' => $cat->getId(),
            'catTitle' => $cat->getTitle()
        ]);
    }
}
