<?php

namespace App\Controller;

use App\Form\GuideType;
use App\Form\RegistrationFormType;
use App\Repository\BookingRepository;
use App\Repository\GuideRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;


class UserController extends AbstractController
{

    // foncion pour pouvoir modifier les infos d'un user 
    #[Route('/profil', name: 'edit_user')]
    public function action(Request $request, EntityManagerInterface $em, GuideRepository $guideRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        // on recup l'user et les infos du formualire 
        $user = $this->getUser();
        $userRole = $user->getRoles();
        
        if ($userRole == ["ROLE_USER"] || $userRole == ["ROLE_ADMIN","ROLE_USER"] ) {
            //var_dump($userRole);
            //var_dump('test');
            $form = $this->createForm(RegistrationFormType::class, $user)->handleRequest($request);
        
            if ($form->isSubmitted()) {
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'Vos informations personnelles ont bien été modifiés ');
            }
            //var_dump($userRole);
            
            return $this->render('user/index.html.twig', [
                'registrationForm' => $form->createView(),
            ]);
        }

        elseif ($userRole == ["ROLE_GUIDE","ROLE_USER"] )  {
            
            
            $userId = $this->getUser()->getId();
        
            $guide = $guideRepository->findOneBy(['user'=> $userId]);
            
            $form = $this->createForm(GuideType::class, $guide)->handleRequest($request);
            if ($form->isSubmitted()) {
                $guide->setDescription($_POST['guideDesc']);
                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Vos informations personnelles ont bien été modifiés ');
                
            }
    
            return $this->render('user/index.html.twig', [
                'registrationForm' => $form->createView(), 'guide'=>$guide
            ]);
        }

        return $this->render('base.html.twig',[]);

        
        
    }


    // fonction pour la liste des reservations du login connecté 
    #[Route('/reservations', name: 'user_reservation')]
    public function booking(BookingRepository $bookingRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser()->getId();
        
        
        
        $bookings = $bookingRepository->findBy([
            'user' => $user
        ]);
        
        return $this->render('user/reservations.html.twig',['user'=>$user,  'bookings' => $bookings]);
    }
}
