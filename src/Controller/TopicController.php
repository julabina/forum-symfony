<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\SubCategories;
use App\Entity\TopicResponses;
use App\Entity\Topics;
use App\Form\NewTopicType;
use App\Form\TopicResponseType;
use App\Repository\CategoriesRepository;
use App\Repository\SubCategoriesRepository;
use App\Repository\TopicResponsesRepository;
use App\Repository\TopicsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TopicController extends AbstractController
{

    /**
     * display the forum categories page
     *
     * @param CategoriesRepository $repository
     * @return Response
     */
    #[Route('/forum', name:'app_forum_list')]
    public function listForum(CategoriesRepository $repository): Response
    {
        return $this->render('pages/topic/forumList.html.twig', [
            'categories' => $repository->findAll()
         ]);
    }

    /**
     * display one topic page manage response.
     * 
     * @param $catId 
     * @param $subId
     * @param Topics $topic
     * @param TopicsRepository $topicRepository
     * @param TopicResponsesRepository $responseRepository
     * @param CategoriesRepository $catRepository
     * @param SubCategoriesRepository $subRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
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

        $nbrOfTopics = $responseRepository->findBy(['topic' => $topic->getId()]);

        if (count($nbrOfTopics) !== 0) {
            $lastPage = ceil(count($nbrOfTopics)/10);
        } else {
            $lastPage = 1;
        }       
            
        $messages = $paginator->paginate(
            $responseRepository->findBy(['topic' => $topic->getId()]),
            $request->query->getInt('page', $lastPage),
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
        return $this->render('pages/topic/show.html.twig', [
            'responses' => $messages,
            'subject' => $topicRepository->findOneBy(['id' => $topic->getId()]),
            'catId' => $cat->getId(),
            'catTitle' => $cat->getTitle(),
            'subId' => $sub->getId(),
            'subTitle' => $sub->getTitle(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * display adding topic page and manage topics adding
     * 
     * @param Categories $catId
     * @param SubCategories $subId 
     * @param Request $request,
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/new/forum/{catId}/{subId}', name:'app_newTopic')]
    public function new(Categories $catId,SubCategories $subId, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(NewTopicType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $topic = $form->getData();
            $topic->setIsActive(0)
                ->setSubCategory($subId)
                ->setUser($this->getUser())
                ->setIsPinned(0)
                ->setIsLock(0)

            ;
             
            $manager->persist($topic);
            $manager->flush();

            return $this->redirectToRoute('app_sub_show', ['catId' => $catId->getId(), 'id' => $subId->getId() ]);
        }

        return $this->render("pages/topic/new.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * display subCategories on categrory page
     * 
     * @param Categories $category 
     * @param SubCategoriesRepository $repository
     * @return Response
     */
    #[Route('/forum/{id}', name: 'app_cat_show')]
    public function showCat(Categories $category, SubCategoriesRepository $repository): Response
    {
        return $this->render('pages/topic/cat.html.twig', [
            'catTitle' => $category->getTitle(),
            'catId' => $category->getId(),
            'subCategories' => $repository->findBy(['category' => $category->getId() ])
        ]);
    }

    /**
     * display all topics from one sub category
     * 
     * @param $catId  
     * @param SubCategories $sub 
     * @param TopicsRepository $repository 
     * @param CategoriesRepository $catRepository 
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/forum/{catId}/{id}', name: 'app_sub_show')]
    public function showSub(
        $catId, 
        SubCategories $sub, 
        TopicsRepository $repository, 
        CategoriesRepository $catRepository, 
        PaginatorInterface $paginator, 
        Request $request
    ): Response 
    {
        $cat = $catRepository->findOneBy(['id' => $catId]);

        $topics = $paginator->paginate(
            $repository->findBy(
                ['subCategory' => $sub->getId(),
                'isPinned' => false], 
                ['updatedAt' => 'DESC']
            ),
            $request->query->getInt('page', 1),
            10
        );
        
        return $this->render('pages/topic/sub.html.twig', [
            'topics' => $topics,
            'pinned' => $repository->findBy(['subCategory' => $sub->getId(), 'isPinned' => true], ['updatedAt' => 'DESC']),
            'subCatTitle' => $sub->getTitle(),
            'subCatId' => $sub->getId(),
            'catId' => $cat->getId(),
            'catTitle' => $cat->getTitle()
        ]);
    }

    /**
     * lock one topic
     * 
     * @param Topics $topic, 
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/lock/{id}', name:'admin_lock')]
    public function lock(Topics $topic, EntityManagerInterface $manager): Response {

        if ($topic->isIsLock() === true) {
            $topic->setIsLock(0);
        } else {
            $topic->setIsLock(1);
        }

        $manager->persist($topic);
        $manager->flush();

        return $this->redirectToRoute('app_topic_show', ['catId' => $topic->getSubCategory()->getCategory()->getId(), 'subId' => $topic->getSubCategory()->getId(), 'id' => $topic->getId()]);
    }
}
