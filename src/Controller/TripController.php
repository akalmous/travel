<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\Trip;
use App\Form\TripFormType;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/trip', name: 'app_trip')]
class TripController extends AbstractController
{
    #[Route('/', name: 'all_trip')]
    public function index(TripRepository $tripRepository): Response
    {
        return $this->render('trip/index.html.twig', [
            'trips' => $tripRepository->findAll(),
            
        ]);
    }


    #[Route('/new', name: 'new_trip')]
    public function addTrip(Request $request, EntityManagerInterface $entityManager): Response
    {

        $trip = new Trip();
        $form = $this->createForm(TripFormType::class, $trip);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $pictures = $form->get('pictures')->getData();
    
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

            $entityManager->clear();
            $entityManager->persist($trip);
            $entityManager->flush();
            // ... perform some action, such as saving the task to the database

            return $this->redirectToRoute('trip');
            }
             return $this->render('trip/add.html.twig', [
            'controller_name' => 'TripController', 'tripForm' => $form->createView(),
        ]);
    }

    



}
