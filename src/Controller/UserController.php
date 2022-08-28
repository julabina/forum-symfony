<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\EditProfilType;
use App\Form\PasswordEditType;
use App\Repository\TopicsRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;

class UserController extends AbstractController
{
    /**
     * display user profil page
     *
     * @param UsersRepository $userRepository
     * @param TopicsRepository $topicRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/profil', name: 'app_profil')]
    public function showProfil(UsersRepository $userRepository, TopicsRepository $topicRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $topics = $paginator->paginate(
            $topicRepository->findBy(['user' => $this->getUser()], ['updatedAt' => 'DESC']),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/user/profil.html.twig', [
           'user' => $userRepository->find(['id' => $this->getUser()]),
           'topics' => $topics
        ]);
    }

    /**
     * display update user profil page and manage it
     * 
     * @param Users $userChoosen 
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === userChoosen")]
    #[Route('/profil/modifier/{id}', name:'app_profil_edit')]
    public function editProfil(Users $userChoosen, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response 
    {
        $form = $this->createForm(EditProfilType::class, $userChoosen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($hasher->isPasswordValid($userChoosen, $form->getData()->getPlainPassword())) {
            
                $user = $form->getData();
                $user->setUpdatedAt(new \DateTimeImmutable());

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Pseudo bien modifié.'
                );
                
            } else {
                
                $this->addFlash(
                    'error',
                    'Le mot de passe ne correspond pas.'
                );

            }
        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * display update password page and manage it
     * 
     * @param Users $userChoosen
     * @param Request $request
     * @param UserPasswordHasherInterface $hasher
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === userChoosen")]
    #[Route('/profil/password/{id}', name:'app_profil_editPassword')]
    public function editPassword(Users $userChoosen, Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $manager): Response 
    {
        $form = $this->createForm(PasswordEditType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            if ($hasher->isPasswordValid($userChoosen, $form->getData()['plainPassword'])) {
                $userChoosen->setUpdatedAt(new \DateTimeImmutable())
                    ->setPlainPassword(
                        $form->getData()['newPassword']
                    );
                
                $manager->persist($userChoosen);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Mot de passe bien modifié.'
                );
            } else {
                $this->addFlash(
                    'error',
                    'Le mot de passe est incorrecte.'
                );
            }

        }

        return $this->render('pages/user/password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * delete the current user account
     * 
     * @param Users $userChoosen 
     * @param EntityManagerInterface $manager 
     * @param Request $request
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === userChoosen")]
    #[Route('/profil/delete/{id}', name:'app_profil_delete')]
    public function deleteUser(Users $userChoosen, EntityManagerInterface $manager, Request $request): Response 
    {
        //delete session before delete in db

        $currentUserId = $this->getUser();
        if ($currentUserId === $userChoosen) {
            $session = $request->getSession();
            $session = new Session();
            $session->invalidate();

            $manager->remove($userChoosen);
            $manager->flush();
        }


        return $this->redirectToRoute('app_home');
    }
}
