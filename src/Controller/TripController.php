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
use Symfony\Component\HttpFoundation\File\File;
use DateTimeInterface;
use DateTime ;


// fonction pour récuperer tout les trips
#[Route('/voyages', name: '')]
class TripController extends AbstractController
{
    
    #[Route('/', name: 'all_trip')]
    
    public function index(Request $request, TripRepository $tripRepository, GuideRepository $guideRepository): Response
    {
        
           if (isset($_POST['btnfiltrer'])){
           
            $tripsfilter = $tripRepository->findByFilter(
                $_POST['searchBar'],
                $_POST['price'],
                $_POST['dateDate'],
                $_POST['duration'],
                $_POST['guide'],
                
            );
           }

           else if(isset($_POST['SearchBar'])) {

            $tripsfilter = $tripRepository->findByName($_POST['searchBar']);
           }
          else {
           $tripsfilter = $tripRepository->findTripGuide();
           //echo("et");
        
        } 
            
        //$searchBar=$request->query->get('searchBar');
        //$tripsfilter = $tripRepository->findByName($searchBar);

        
        // on renvoie que les trips avec un guide et toute la liste des  guides 
        return $this->render('trip/index.html.twig', [
            'trips1' => $tripRepository->findTripGuide(), 'guides'=>$guideRepository->findAll(),'trips' => $tripsfilter,
            
        ]);

        

        
    }

    #[Route('/test', name: 'filter_trip')]
    public function actionFiltrer(Request $request,TripRepository $tripRepository,GuideRepository $guideRepository): Response
    {
        
        // la barre de recherche
        echo($_POST['searchBar']);
        echo($_POST['price']);
        echo($_POST['dateDate']);
        echo($_POST['duration']);
        var_dump($_POST['guide']);
        //echo($dateDate);
        
        
        
        //var_dump($tripsfilter);
        return $this->render('trip/index.html.twig', [
             'guides'=>$guideRepository->findAll(),
            
        ]);
    }
    

    #[Route('/', name: 'search_trip')]
    public function searchBar(TripRepository $tripRepository, Request $request) : Response
    {
        $searchBar='0';
        $searchBar=$request->query->get('searchBar');
        //var_dump($searchBar);
        //dump('tesr ' + $searchBar);
        //$answers = $tripRepository->findBy($request->query->get('q'));
        return $this->render('trip/index.html.twig',[$searchBar]);
    }

    

    // fonction pour ajouter un voyage
    #[Route('/new', name: 'new_trip')]
    public function addTrip(Request $request, EntityManagerInterface $entityManager, TripRepository $tripRepository, GuideRepository $guideRepository): Response
    {
       
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $trip = new Trip();
        $form = $this->createForm(TripFormType::class, $trip);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            
            
            $pictures = $form->get('pictures')->getData();
            //var_dump($pictures);
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
                $trip->addPicture($img);
            }
            
            
            $datedepart = $form->get('departureDate')->getData();
            $dateretour= $form->get('returnDate')->getData();
            $interval = $datedepart->diff($dateretour);
            
            
            $trip->setDuration($interval->format('%a'));
            $trip->setDescription($_POST['description']);
            $trip->setPriceDesc($_POST['priceDesc']);
            $trip->setArchived(FALSE);
            $trip->setFormalitie($_POST['formalitie']);
            $entityManager->clear();
            $entityManager->persist($trip);
            $entityManager->flush();
            // ... perform some action, such as saving the task to the database

            $this->addFlash('success', 'Le voyage a bien été ajouté !');
            return $this->redirectToRoute('all_trip');
            }
            
            return $this->render('trip/add.html.twig', [
                'controller_name' => 'TripController', 'tripForm' => $form->createView(),'trips1' => $tripRepository->findTripGuide(),'guides'=>$guideRepository->findAll(),
            ]);
    }


    // fonction pour afficher les détails d'un voyage
    #[Route('/show/{id}', name: 'show_trip')]

    public function showTrip($id, TripRepository $tripRepository){
    
        return $this->render('trip/show.html.twig', [
            'trip' => $tripRepository->findOneBy(['id' => $id]), 
            
        ]);
    }


    //fonction pour modifier un voyage 
    #[Route('/edit/{id}', name: 'edit_trip')]
    public function editTrip($id ,EntityManagerInterface $em, TripRepository $tripRepository, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $trip = $tripRepository->findOneBy(['id'=>$id]);
       
        $form = $this->createForm(TripFormType::class, $trip)->handleRequest($request);
        if ($form->isSubmitted()){
           ;

            // Persister l'entité principale (dans ce cas, $trip) dans la base de données
            $em->persist($trip);
            $em->flush();
            
            
           

            

            
        if ($form->get('pictures')->getData() != null) {
            $picturesTrip = $trip->getPictures();

            foreach ($picturesTrip as $picture) {
                //echo('test');
                $em->remove($picture);
                $em->flush();
             
            }

            $pictures = $form->get('pictures')->getData();
            foreach ($pictures as $picture ) {
                # code...
          
            $fichier = md5(uniqid()).'.'.$picture->guessExtension();
                
            // On copie le fichier dans le dossier uploads
            $picture->move(
                $this->getParameter('images_directory'),
                $fichier
            );
            
            // On crée l'image dans la base de données
            $img = new Picture();
            $img->setName($fichier);
            $trip->addPicture($img);
            
        }
        
        }

            
            
            $datedepart = $form->get('departureDate')->getData();
            $dateretour= $form->get('returnDate')->getData();
            $interval = $datedepart->diff($dateretour);
            
            
            $trip->setDuration($interval->format('%a'));
            $trip->setDescription($_POST['description']);
            $trip->setPriceDesc($_POST['priceDesc']);
            $trip->setFormalitie($_POST['formalitie']);
            $em->persist($trip);
            $em->flush();
            $this->addFlash('success','le voyage a bie été modifié');
        }
        return $this->render('trip/edit.html.twig',['tripForm'=>$form->createView(), 'trip'=>$trip]);
    }

    // fonction pour archiver un voyage (et non supprimer)
    #[Route('/delete/{id}', name: 'delete_trip')]
    public function action($id, Request $request, TripRepository $tripRepository, EntityManagerInterface $em): Response
    {   
        //
        $trip = $tripRepository->findOneBy(['id'=>$id]);
        $trip->setArchived(TRUE);
        $em->flush();
        return $this->redirectToRoute('all_trip');
    }


                                        
}
