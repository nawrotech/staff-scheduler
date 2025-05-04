<?php

namespace App\Controller;

use App\Dto\ShiftCalendarEventDto;
use App\Entity\Assignment;
use App\Entity\Shift;
use App\Entity\User;
use App\Enum\AssignmentStatus;
use App\Form\ShiftType;
use App\Repository\AssignmentRepository;
use App\Repository\ShiftPositionRepository;
use App\Repository\ShiftRepository;
use App\Repository\UserRepository;
use App\Service\ShiftService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class ShiftController extends AbstractController
{

    public function __construct(
        private ShiftRepository $shiftRepository,
        private EntityManagerInterface $em
    ) {}


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

        $events = array_map(
            fn(Shift $shift) => ShiftCalendarEventDto::fromShift($shift)->toArray(),
            $shifts
        );

        return new JsonResponse($events);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/shifts/{id}/apply', name: 'assignment_shift_apply', methods: ['POST'])]
    public function apply(
        Shift $shift,
        ShiftService $shiftService,
        #[CurrentUser()] User $user,
        EntityManagerInterface $em,
        ShiftPositionRepository $shiftPositionRepository,
        AssignmentRepository $assignmentRepository,
        Request $request
    ): Response {

        if (!$user->getStaffProfile()) {
            $this->addFlash('error', 'You need a staff profile to apply for shifts');
            return $this->redirectToRoute('shift_details', ['id' => $shift->getId()]);
        }

        $positionId = $request->getPayload()->get('position_id');
        $shiftPosition = $shiftPositionRepository->find($positionId);

        if (!$shiftPosition || $shiftPosition->getShift()->getId() !== $shift->getId()) {
            $this->addFlash('danger', 'Invalid position selected.');
            return $this->redirectToRoute('shift_show', ['id' => $shift->getId()]);
        }

        if ($shiftPosition->getName() !== $user->getStaffProfile()->getPosition()) {
            $this->addFlash('danger', 'You don\'t have required position for that shift!');
            return $this->redirectToRoute('shift_show', ['id' => $shift->getId()]);
        }

        $existingAssignment = $assignmentRepository->findOneBy([
            'shift' => $shift,
            'staffProfile' => $user->getStaffProfile()
        ]);

        if ($existingAssignment) {
            $this->addFlash('warning', 'You have already applied for this shift');
            return $this->redirectToRoute('shift_show', ['id' => $shift->getId()]);
        }

        $assignment = new Assignment();
        $assignment->setShift($shift);
        $assignment->setShiftPosition($shiftPosition);
        $assignment->setStaffProfile($user->getStaffProfile());
        $assignment->setAssignedAt(new \DateTimeImmutable());
        $assignment->setStatus(AssignmentStatus::PENDING);

        $em->persist($assignment);
        $em->flush();

        $shiftService->checkAndDispatchShiftFulfilled($shift);

        $this->addFlash('success', 'Successfully applied for the shift!');
        return $this->redirectToRoute('shift_show', ['id' => $shift->getId()]);
    }



    #[Route('shifts/calendar', name: 'shift_calendar', methods: ['GET'])]
    public function calendar(ShiftRepository $shiftRepository): Response
    {
        return $this->render('shift/calendar.html.twig', []);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('shifts/create/{id?}', name: 'shift_create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        ShiftService $shiftService,
        ?Shift $shift = null
    ): Response {
        if (!$shift) {
            $shift = new Shift();
        }

        $form = $this->createForm(ShiftType::class, $shift);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $shiftService->save($shift);

            $this->addFlash('success', 'Shift created successfully!');

            return $this->redirectToRoute('shift_calendar');
        }

        return $this->render('shift/create.html.twig', [
            'shift' => $shift,
            'form' => $form,
        ]);
    }

    #[Route('shifts/{id?}', name: "shift_show")]
    public function showShift(
        Shift $shift,
        UserRepository $userRepository
    ): Response {

        // dd($userRepository->findByRole('ROLE_USER'));

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
