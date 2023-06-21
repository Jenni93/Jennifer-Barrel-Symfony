<?php

namespace App\Controller;

use App\Form\BookingType;
use App\Entity\Booking;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;



class BookingController extends AbstractController
{
    #[Route('/', name: 'app_booking')]
    public function index(): Response
    {
        return $this->render('booking/index.html.twig', [
            'controller_name' => 'BookingController',
        ]);
    }


    #[Route('/create', name: 'app_create', methods: ['GET','POST'])]
    public function create(Request $request, PersistenceManagerRegistry $doctrine): Response
    {   
        $em = $doctrine->getManager();

        $data = $request->getContent();
        $data = json_decode($data);

        $booking = new Booking();
        $booking->setCreatedAt(new \DateTime());
        $booking->setDesciption($data->description);
        $booking->setStatus($data->status);
        

        $em->persist($booking);
        $em->flush();

        return new Response($data->status);
    }

    #[Route('/showItems', name: 'app_show_items')]
    public function showItems(PersistenceManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        $repository = $em->getRepository(Booking::class);
        $bookings = $repository->findAllArray();

        dump($bookings);
        
        return  new JsonResponse ($bookings);

    }

    #[Route('/show', name: 'app_show')]
    public function show(Request $request, PersistenceManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        $data = $request->getContent();
        $data = json_decode($data);

        $repository = $em->getRepository(Booking::class);
        $booking = $repository->findByIdArray($data);
                
        return  new JsonResponse ($booking);

    }

    #[Route('/update', name: 'app_update')]
    public function update(Request $request, PersistenceManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();

        $data = $request->getContent();
        $data = json_decode($data);

        $booking = $em->getRepository(Booking::class)->find($data->idBooking);
        $booking->setStatus($data->BookingV->status);
        $booking->setDesciption($data->BookingV->description);
        $em->flush();

       return  new JsonResponse ($data);
    }

    #[Route('/delete', name: 'app_delete')]
    public function delete(Request $request, PersistenceManagerRegistry $doctrine): Response
    {

        $em = $doctrine->getManager();

        $data = $request->getContent();
        $data = json_decode($data);

        $booking = $em->getRepository(Booking::class)->find($data);
        $booking->setDeletedAt(new \DateTime());
        $booking->setStatus(false);
        $em->flush();
        
                
        return new Response($data);
    }

    #[Route('/remove', name: 'app_remove')]
    public function remove(Request $request, PersistenceManagerRegistry $doctrine): Response
    {

        $em = $doctrine->getManager();

        $data = $request->getContent();
        $data = json_decode($data);

        $repository = $em->getRepository(Booking::class);
        $booking = $repository->findBy(['id' => $data]);
        $em->remove($booking[0]);
        $em->flush();
                
        return new Response($data);
    }




}
