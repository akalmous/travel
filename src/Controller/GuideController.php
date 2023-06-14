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
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


#[Route('/guide', name: '')]
class GuideController extends AbstractController
{

    // Inscription d'un new guide
    #[Route('/inscription', name: 'register_guide')]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, 
    TripRepository $tripRepositor, AuthenticationUtils $authenticationUtils): Response
    {

        $guide = new Guide();
        $form=$this->createForm(GuideType::class, $guide);
         $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
             // gestion des images
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


            // récuperation des données à partir du formulaire
            $plainPassword = $form["user"]["plainPassword"]->getData();
            $user = $guide->getUser();
            $user->setRoles(["ROLE_GUIDE"]);
            $guide->getUser()->setPassword($userPasswordHasher->hashPassword(
                $user,$plainPassword
            ));

            //insertion dans la bdd
            $entityManager->clear();
            $entityManager->persist($guide);
            $entityManager->flush();
            
            // get the login error if there is one
            $error = $authenticationUtils->getLastAuthenticationError();
            // last username entered by the user
            $lastUsername = $authenticationUtils->getLastUsername();

            // message success
            $this->addFlash('success', 'Votre compte a été bien créé');
            //redirection à la page de connexion
            return $this->redirectToRoute('app_login', ['last_username' => $lastUsername, 'error' => $error]);
        }
        

        // je l'envoie vers le formulaire d'iscription d'un guide
        return $this->render('guide/register.html.twig', ['guideForm' => $form->createView()]);
    }

    
    // la fonction  qui va permmettre d'afficher tout les trips sans guide
    #[Route('/voyages', name: 'choose_trip')]
    public function choose(GuideRepository $guideRepository, TripRepository $tripRepository){
        return $this->render('guide/choose.html.twig', [ 'trips'=> $tripRepository->findBy(
            ['guide'=> null]
        )
         
           
        ]);

    }

    // la fonction qui permet d'avoir des détails du trip choisi par le guide
    #[Route('/voyage/{id}', name: 'guide_show_trip')]

    
    public function showTrip($id, TripRepository $tripRepository, GuideRepository $guideRepository, EntityManagerInterface $entityManager,){

        $this->denyAccessUnlessGranted('ROLE_GUIDE');
        // récupération de l'id du guide connecté
        $userId = $this->getUser()->getId();
        
        $guide = $guideRepository->findOneBy([
            'user'=> $userId
        ]);
        if (isset($_POST['participate'])){
            $trip = $tripRepository->findOneBy(['id' => $id]);
            $trip->setGuide($guide);
            $entityManager->flush();
        }
        
        // je renvoie :  le guide trouvé ainsi que le trip grace à l'id
        return $this->render('guide/show.html.twig', [
            'trip' => $tripRepository->findOneBy(['id' => $id]),'guides'=>$guide 
            
        ]);
    }

    
    // la fonction qui permet au guide de participer à un trip
    #[Route('/participer/voyages/{id}', name: 'participate_trip')]
    public function participateTrip( $id, TripRepository $tripRepository, EntityManagerInterface $entityManager, 
    GuideRepository $guideRepository, UserRepository $userRepository){
       
        $this->denyAccessUnlessGranted('ROLE_GUIDE');
        //on récipere le guide
        $userId = $this->getUser()->getId();
        
        $guide = $guideRepository->findOneBy([
            'user'=> $userId
        ]);
        
        //on récupere le trip grace à l'id
        $trip = $tripRepository->findOneBy(['id' => $id]);
        
        // et on modifie la valeur de guide
        $trip->setGuide($guide);
        $entityManager->flush(); // BDD
        return $this->render('guide/show.html.twig', [
            'trip' => $tripRepository->findOneBy(['id' => $id]), 'guides'=>$guide
            
        ]);
    }

    //la fonction qui permet de trouver tous les trips choisi par le guide connecté
   
    
    #[Route('/profil', name: 'tripGuide')]

    public function tripGuide( TripRepository $tripRepository, GuideRepository $guideRepository){
        $this->denyAccessUnlessGranted('ROLE_GUIDE');
        
        //on récupere le guide
        $userId = $this->getUser()->getId();
        $guide = $guideRepository->findOneBy([
            'user'=> $userId
        ]);
        

        $guideId  = $guide -> getId();

        //je récupere les trips à partir du guide connecté
        $tripGuide = $tripRepository->findBy([
            'guide' => $guideId
        ]);
        
        return $this->render('guide/index.html.twig', [
            'trips' => $tripGuide
            
        ]);
    }
}
