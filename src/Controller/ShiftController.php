<?php

namespace App\Controller;

use App\Dto\ShiftCalendarEventDto;
use App\Entity\Shift;
use App\Entity\User;
use App\Repository\AssignmentRepository;
use App\Repository\ShiftRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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


    #[Route('/', name: 'shift_calendar', methods: ['GET'])]
    public function calendar(): Response
    {
        return $this->render('shift/calendar.html.twig', []);
    }


    #[Route('/api/calendar-data', name: 'api_calendar_shifts_data', methods: ['GET'])]
    public function getCalendarData(
        ShiftRepository $shiftRepository
    ): JsonResponse {

        $today = new \DateTimeImmutable();
        $oneWeekAgo = $today->modify('-1 week');
        $oneMonthAhead = $today->modify('+1 month');

        $shifts = $shiftRepository->findInDateRange($oneWeekAgo, $oneMonthAhead);

        $events = array_map(
            fn(Shift $shift) => ShiftCalendarEventDto::fromShift($shift)->toArray(),
            $shifts
        );

        return new JsonResponse($events);
    }


    #[Route('shifts/{id?}', name: "shift_details")]
    public function shiftDetails(
        Shift $shift,
        AssignmentRepository $assignmentRepository,
        #[CurrentUser()] User $user
    ): Response {

        $existingAssignments = [];

        if ($user && $user->getStaffProfile()) {
            $existingAssignments = $assignmentRepository->findBy([
                'shift' => $shift,
                'staffProfile' => $user->getStaffProfile()
            ]);

            $existingAssignments = array_reduce($existingAssignments, function ($result, $assignment) {
                $result[$assignment->getShiftPosition()->getId()] = $assignment;
                return $result;
            }, []);
        }

        return $this->render('shift/details.html.twig', [
            'shift' => $shift,
            'existingAssignments' => $existingAssignments
        ]);
    }
}
