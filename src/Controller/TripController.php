<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\Trip;
use App\Form\TripFormType;
use App\Repository\GuideRepository;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use DateTimeInterface;
use DateTime ;


// fonction pour récuperer tout les trips
#[Route('/voyages', name: '')]
class TripController extends AbstractController
{
    
    #[Route('/', name: 'all_trip')]
    
    public function index(Request $request, TripRepository $tripRepository, GuideRepository $guideRepository): Response
    {
            
            // la barre de recherche
            $tripsfilter = $tripRepository->findByName(
                $request->query->get('searchBar'),
                 $request->query->get('price'),
                 $request->query->get('dateDate'),
                 $request->query->get('duration'),
                 $request->query->get('guide'),
            );
            
            
        //$searchBar=$request->query->get('searchBar');
        //$tripsfilter = $tripRepository->findByName($searchBar);

        
        // on renvoir que les trips avec un guide et toute la liste des  guides 
        return $this->render('trip/index.html.twig', [
            'trips1' => $tripRepository->findTripGuide(),  'trips' => $tripsfilter, 'request'=>$request,'guides'=>$guideRepository->findAll(),
            
        ]);

        

        
    }

    #[Route('/', name: 'search_trip')]
    public function searchBar(TripRepository $tripRepository, Request $request) : Response
    {
        $searchBar='0';
        $searchBar=$request->query->get('searchBar');
        var_dump($searchBar);
        dump('tesr ' + $searchBar);
        //$answers = $tripRepository->findBy($request->query->get('q'));
        return $this->render('trip/index.html.twig',[$searchBar]);
    }

    

    
    #[Route('/new', name: 'new_trip')]
    public function addTrip(Request $request, EntityManagerInterface $entityManager, TripRepository $tripRepository, GuideRepository $guideRepository): Response
    {
       
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $trip = new Trip();
        $form = $this->createForm(TripFormType::class, $trip);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            
            $pictures = $form->get('pictures')->getData();
            var_dump($pictures);
            // On boucle sur les images
            foreach($pictures as $picture){
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()).'.'.$picture->guessExtension();
                
                // On copie le fichier dans le dossier uploads
                $picture->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                
                // On crée l'image dans la base de données
                $img = new Picture();
                $img->setName($fichier);
                echo('test');
                echo($fichier);
                $trip->addPicture($img);
            }
            
            
            $datedepart = $form->get('departureDate')->getData();
            $dateretour= $form->get('returnDate')->getData();
            $interval = $datedepart->diff($dateretour);
            
            
            $trip->setDuration($interval->format('%a'));
            $trip->setDescription($_POST['description']);
            $trip->setPriceDesc($_POST['priceDesc']);
            $entityManager->clear();
            $entityManager->persist($trip);
            $entityManager->flush();
            // ... perform some action, such as saving the task to the database

            
            }
             return $this->render('trip/add.html.twig', [
            'controller_name' => 'TripController', 'tripForm' => $form->createView(),'trips1' => $tripRepository->findTripGuide(),'guides'=>$guideRepository->findAll(), 
        ]);
    }



    #[Route('/show/{id}', name: 'show_trip')]

    public function showTrip($id, TripRepository $tripRepository){
    
        return $this->render('trip/show.html.twig', [
            'trip' => $tripRepository->findOneBy(['id' => $id]), 
            
        ]);
    }

    #[Route('/', name: 'edit_trip')]
    public function action(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('template.html.twig');
    }


                                        
}
