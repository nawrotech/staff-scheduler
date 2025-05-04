<?php

namespace App\Controller;

use App\Entity\Assignment;
use App\Enum\AssignmentStatus;
use App\Service\AssignmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
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
