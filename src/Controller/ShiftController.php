<?php

namespace App\Controller;

use App\Entity\Shift;
use App\Form\ShiftType;
use App\Repository\ShiftRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ShiftController extends AbstractController
{

    public function __construct(
        private ShiftRepository $shiftRepository,
        private EntityManagerInterface $em
        )
    {
    }


    #[Route('/', name: 'shift_index')]
    public function index(): Response
    {
        // $shifts = $this->isGranted('ROLE_ADMIN') 
        //     ? $this->shiftRepository->findAll() 
        //     : $this->shiftRepository->findBy(['staff' => $this->getUser()]);

        return $this->render('shift/index.html.twig', [
            'controller_name' => 'ShiftController',
        ]);
    }


    #[Route('/api/shifts', name: 'api_shifts', methods: ['GET'])]
    public function getShiftsForCalendar(): JsonResponse
    {
        $shifts = $this->shiftRepository->findAll(); 
        
        $events = [];
        foreach ($shifts as $shift) {
            $date = $shift->getDate()->format('Y-m-d');
            $startDateTime = new \DateTimeImmutable($date . ' ' . $shift->getStartTime()->format('H:i:s'));
            $endDateTime = new \DateTimeImmutable($date . ' ' . $shift->getEndTime()->format('H:i:s'));
            
            $events[] = [
                'id' => $shift->getId(),
                'title' => 'Shift',
                'start' => $startDateTime->format('Y-m-d\TH:i:s'),
                'end' => $endDateTime->format('Y-m-d\TH:i:s'),
                'extendedProps' => [
                    'notes' => $shift->getNotes()
                ]
            ];
        }
        return new JsonResponse($events);
    }


    #[Route('shifts/calendar', name: 'shift_calendar', methods: ['GET'])]
    public function calendar(ShiftRepository $shiftRepository): Response
    {
        // $shifts = $this->isGranted('ROLE_ADMIN') 
        //     ? $shiftRepository->findAll() 
        //     : $shiftRepository->findBy(['staff' => $this->getUser()]);
            
        return $this->render('shift/calendar.html.twig', [
            // 'shifts' => $shifts,
        ]);
    }

        // ADMIN/MANAGER ONLY
        #[Route('shifts/create/{id?}', name: 'shift_create', methods: ['GET', 'POST'])]
        public function create(
            Request $request, 
            ?Shift $shift = null
        ): Response
        {
            if (!$shift) {
                $shift = new Shift();
            }
           
            $form = $this->createForm(ShiftType::class, $shift);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) { 
                $this->em->persist($shift);
                $this->em->flush();
    
                $this->addFlash('success', 'Shift created successfully!');
    
                return $this->redirectToRoute('shift_calendar');
    
            }
    
            return $this->render('shift/create.html.twig', [
                'shift' => $shift,
                'form' => $form,
            ]);
        }

    #[Route('shifts/{id?}', name: "shift_show")]
    public function showShift(Shift $shift): Response
    {
        // dd($shift);

        // $shifts = $this->isGranted('ROLE_ADMIN') 
        //     ? $this->shiftRepository->findAll() 
        //     : $this->shiftRepository->findBy(['staff' => $this->getUser()]);

        return $this->render('shift/details.html.twig', [
           'shift' => $shift
        ]);
    }




    // #[Route('/export', name: 'shift_export', methods: ['GET'])]
    // public function export(ShiftRepository $shiftRepository): Response
    // {
    //     // Logic for exporting shifts as CSV or PDF
    // }

    
}
