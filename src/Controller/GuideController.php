<?php

namespace App\Controller;

use App\Entity\Guide;
use App\Entity\PictureGuide;
use App\Entity\Trip;
use App\Entity\User;
use App\Form\GuideType;
use App\Repository\GuideRepository;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/guide', name: '')]
class GuideController extends AbstractController
{
    #[Route('/register', name: 'register_guide')]
    
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, TripRepository $tripRepository): Response
    {
        $guide = new Guide();
        $form=$this->createForm(GuideType::class, $guide);
         $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            
            $pictureguides = $form->get('pictureguides')->getData();
            
            foreach ($pictureguides as $pictureguide ) {
                $fichier = md5(uniqid()).'.'.$pictureguide->guessExtension();
                $pictureguide->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                $img = new PictureGuide();
                $img->setName($fichier);
                $guide->addPictureGuide($img);
            };
            $plainPassword = $form["user"]["plainPassword"]->getData();
            $user = $guide->getUser();
            $user->setRoles(["ROLE_GUIDE"]);
            $guide->getUser()->setPassword($userPasswordHasher->hashPassword(
                $user,$plainPassword
            ));

            
            $entityManager->clear();
            $entityManager->persist($guide);
            $entityManager->flush();
            
            
            

            
    
        }
        return $this->render('guide/register.html.twig', [ 'trips'=> $tripRepository->findBy(
            ['guide'=> null]),
            'controller_name' => 'GuideController','guideForm' => $form->createView(),
        ]);
    }

    #[Route('/choose', name: 'choose_trip')]
    public function choose(GuideRepository $guideRepository, TripRepository $tripRepository){
        return $this->render('guide/choose.html.twig', [ 'trips'=> $tripRepository->findBy(
            ['guide'=> null]
        )
         
           
        ]);

    }

    #[Route('/show/{id}', name: 'guide_show_trip')]

    public function showTrip($id, TripRepository $tripRepository, GuideRepository $guideRepository){
        $userId = $this->getUser()->getId();
        
        $guide = $guideRepository->findOneBy([
            'user'=> $userId
        ]);
    
        return $this->render('guide/show.html.twig', [
            'trip' => $tripRepository->findOneBy(['id' => $id]),'guides'=>$guide 
            
        ]);
    }

    #[Route('/test/show/{id}', name: 'participate_trip')]

    public function participateTrip( $id, TripRepository $tripRepository, EntityManagerInterface $entityManager, GuideRepository $guideRepository, UserRepository $userRepository){
       
        $userId = $this->getUser()->getId();
        
        $guide = $guideRepository->findOneBy([
            'user'=> $userId
        ]);
        
        $trip = $tripRepository->findOneBy(['id' => $id]);
        
        $trip->setGuide($guide);
        $entityManager->flush();
        return $this->render('guide/show.html.twig', [
            'trip' => $tripRepository->findOneBy(['id' => $id]), 'guides'=>$guide
            
        ]);
    }

    #[Route('/', name: 'tripGuide')]

    public function tripGuide( TripRepository $tripRepository, GuideRepository $guideRepository){
        $userId = $this->getUser()->getId();
        
        $guide = $guideRepository->findOneBy([
            'user'=> $userId
        ]);
        
        $guideId  = $guide -> getId();
        $tripGuide = $tripRepository->findBy([
            'guide' => $guideId
        ]);
        
        return $this->render('guide/index.html.twig', [
            'trips' => $tripGuide
            
        ]);
    }
}
