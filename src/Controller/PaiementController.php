<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Trip;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaiementController extends AbstractController
{

    // récupere la clé de stripe 
    #[Route('/paiement', name: 'app_paiement')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('paiement/index.html.twig', [
            'stripe_key' => $_ENV["STRIPE_KEY"],
        ]);
    }
    

    // la fonction pour payer avec stripe 
    #[Route('/paiement/stripe/{id}', name: 'app_stripe_charge')]
    public function stripe($id, TripRepository $tripRepository, Request $request,  EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // le formualire d'informations personnelles est soumis
        if (isset($_POST['submit'])){
            
            // on récupere la clé
            Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
            // on récupere les infos d trip 
            $trip = $tripRepository->findOneBy(['id' => $id]);
            // et on crée le paiement
            $checkout_session = Stripe\Checkout\Session::create ([
                'line_items' => [[
                    # On récupere le prix, quantité, nom infos du trip .....
                    
                    'amount' => $trip->getPrice()*100,
                    'quantity' => $_POST['quantity'],

                    'currency'=> 'eur',
                    'name'=>$trip->getName(),
                    
                    
                    
                ]
                ],
                
                'mode' => 'payment',
                // si la paiement aboutit il se redirige vers cette page
                'success_url' => $this->generateUrl('all_trip', [], UrlGeneratorInterface::ABSOLUTE_URL),
                // le cas contraire ici
                'cancel_url' => 'https://www.binaryboxtuts.com/',
        ]);

            // ajout d'une nouvelle réservation dans la bdd
            $booking = new Booking();
            $price = $trip->getPrice()*$_POST['quantity'];
            $user = $this->getUser();
            $guide = $trip->getGuide();
            $booking->setPrice($price);
            $booking->setReservationDate(new \DateTime());
            $booking->setUser($user);
            $booking->setTrip($trip);
            $booking->setGuide($guide);
            $entityManager->persist($booking);
            $entityManager->flush();

            //on lance le paiement, le lien est généré automatiquement
            header("Location: " . $checkout_session->url);
            return $this->redirect($checkout_session->url, 303);
        
        }
        
    }
   

   
    
    

        
    


    
}
