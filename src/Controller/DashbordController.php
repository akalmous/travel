<?php

namespace App\Controller;

use App\Repository\BookingRepository;
use App\Repository\UserRepository;
use App\Repository\GuideRepository;
use App\Repository\TripRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Role\Role;

class DashbordController extends AbstractController
{
    #[Route('/dashbord', name: 'app_dashbord')]
    public function index(TripRepository $tripRepository, GuideRepository $guideRepository, UserRepository $userRepository, BookingRepository $bookingRepository): Response
    {
        
        
        $guides = count($userRepository->findByDateGuides('ROLE_GUIDE'));
        //echo($guides);
        $trips = count($tripRepository->findByDateTrips());
        //echo($trips);
        $users =count($userRepository->findByDateUsers('ROLE_USER'));
        //echo($users);
        $bookings = count($bookingRepository->findByDateBookings());
        //echo($bookings);
        return $this->render('dashbord/index.html.twig', ['guides'=> $guides, 'trips'=> $trips, 'users'=> $users, 'bookings' => $bookings]);
    }
}
