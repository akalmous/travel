<?php

namespace App\Controller;

use App\Repository\BookingRepository;
use App\Repository\TripRepository;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Options;

class SummaryController extends AbstractController
{
    #[Route('/summary/{id}', name: 'app_summary')]
    public function index($id , BookingRepository $bookingRepository): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser()->getId();
        header('Content-type: application/pdf');
        
        
        $booking = $bookingRepository->findOneBy([
            'id' => $id
        ]);
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', TRUE);
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->set('isPhpEnabled', true);
        $pdfOptions->set('isHtml5ParserEnabled', true);
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('summary/index.html.twig', [
            'title' => "Welcome to our PDF Test", 'booking' => $booking
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
        exit();
        
    }

    #[Route('/summary/guide/{id}', name: 'guide_summary')]
    public function indexGuide($id , BookingRepository $bookingRepository, TripRepository $tripRepository): Response
    {

        
        header('Content-type: application/pdf');
        
        
        $trip = $tripRepository->findOneBy([
            'id' => $id
        ]);

        $nbPlaces = $trip->getReservedPlaces();
        $price = $trip->getPrice();

        $salaryGuide = 0.2 * ($nbPlaces * $price)   ;
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', TRUE);
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->set('isPhpEnabled', true);
        $pdfOptions->set('isHtml5ParserEnabled', true);
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('summary/guide.html.twig', [
            'title' => "Welcome to our PDF Test", 'trip' => $trip, 'salaryGuide'=>$salaryGuide
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
        exit();
        
    }
}
