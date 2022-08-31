<?php

namespace App\Controller;

use App\Entity\TopicResponses;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResponseController extends AbstractController
{
    /**
     * delete one topic response
     * 
     * @param TopicResponses $resp 
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/deleteResp/{id}', name:'app_resp_delete')]
    public function delete(TopicResponses $resp, EntityManagerInterface $manager): Response 
    {

        $manager->remove($resp);
        $manager->flush();

        return $this->redirectToRoute('app_topic_show', [
            'catId' => $resp->getTopic()->getSubCategory()->getCategory()->getId(),
            'subId' => $resp->getTopic()->getSubCategory()->getId(),
            'id' => $resp->getTopic()->getId()
        ]);

    }
}
