<?php

namespace App\Controller;

use App\Entity\Assignment;
use App\Entity\Shift;
use App\Entity\User;
use App\Enum\AssignmentStatus;
use App\Repository\AssignmentRepository;
use App\Repository\ShiftPositionRepository;
use App\Service\AssignmentService;
use App\Service\ShiftService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;

#[IsGranted('ROLE_USER')]
final class AssignmentController extends AbstractController
{
    #[Route('/assignment', name: 'app_assignment')]
    public function index(): Response
    {
        return $this->render('assignment/index.html.twig', [
            'controller_name' => 'AssignmentController',
        ]);
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


    #[Route('/assignments/{id}/cancel', name: 'assignment_shift_cancel', methods: ['POST'])]
    #[IsCsrfTokenValid(new Expression('"cancel-assignment-" ~ args["assignment"].getId()'), tokenKey: 'token')]
    public function cancelAssignment(
        Assignment $assignment,
        AssignmentService $assignmentService
    ): Response {
        if ($this->getUser() !== $assignment->getStaffProfile()->getUser()) {
            throw new AccessDeniedException('You can only cancel your own assignments.');
        }

        $shiftId = $assignment->getShift()->getId();
        $wasApproved = $assignment->getStatus() === AssignmentStatus::APPROVED;

        $assignmentService->cancelAssignment($assignment, $wasApproved);

        if ($wasApproved) {
            $this->addFlash('warning', 'Your approved assignment has been canceled. Management has been notified.');
        } else {
            $this->addFlash('success', 'Your assignment has been canceled.');
        }

        return $this->redirectToRoute('shift_show', ['id' => $shiftId]);
    }
}
