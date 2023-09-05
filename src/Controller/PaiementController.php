<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Trip;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\AST\QuantifiedExpression;
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
        $quantity =$_POST['quantity'];
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
                ]],
                'mode' => 'payment',

                // si la paiement aboutit il se redirige vers cette page  avec la session comme ca je recypere les infos de cet chekout aprés et j'envoie l'id du trip
                'success_url'=> urldecode($this->generateUrl('success', ['session_id' => '{CHECKOUT_SESSION_ID}', 'id'=> $id], UrlGeneratorInterface::ABSOLUTE_URL)),
                // le cas contraire ici
                'cancel_url' => 'https://www.binaryboxtuts.com/',      
        ]);
        
            //on lance le paiement, le lien est généré automatiquement
            return $this->redirect($checkout_session->url, 303);
        
        }
        
    }


    // la fonction qui sert une fois le paiement est en statut : success 
    #[Route('/success', name: 'success')]
    public function action(Request $request, TripRepository $tripRepository, EntityManagerInterface $entityManager): Response
    {

        // on recupere la session stripe et la session du checkout ainis que l'id du trip
        Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        $id=$request->query->get('id');
        $session = \Stripe\Checkout\Session::retrieve($request->get('session_id'));
        $data = \Stripe\Checkout\Session :: allLineItems($request->get('session_id'));
       
        // je recupere le trip grace à l'id
        $trip = $tripRepository->findOneBy(['id' => $id]);
        $quantity = $data->data[0]->quantity; // je recupere la quantitué du panier 

        // je créee une nouvelle reservation (le trip, guide et user)
        $booking = new Booking();
            $price = $trip->getPrice()*$quantity;
            $user = $this->getUser();
            $guide = $trip->getGuide();
            $booking->setPrice($price);
            $booking->setReservationDate(new \DateTime());
            $booking->setUser($user);
            $booking->setTrip($trip);
            $booking->setGuide($guide);
            $trip->setReservedPlaces($trip->getReservedPlaces() + $quantity); // je modifie l'entité quantité dans la table trip 'les places reserves et les places restantes '
            $trip->setNumberPlaces($trip->getNumberPlaces() - $quantity);
            $entityManager->persist($booking);
            $entityManager->persist($trip);
            $entityManager->flush();


        // je redirige vers la template 
        return $this->render('paiement/success.html.twig',['session'=> $session, 'quantity' => $quantity , 'id'=>$id, 'trip'=>$trip] );
        
    }

  


    

   
    
    

        
    


    
}
