<?php

namespace App\Controller\Admin;

use App\Entity\Assignment;
use App\Enum\AssignmentStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;

final class AssignmentController extends AbstractController
{
    #[Route('/assignment', name: 'app_assignment')]
    public function index(): Response
    {
        return $this->render('assignment/index.html.twig', [
            'controller_name' => 'AssignmentController',
        ]);
    }

    #[Route('/assignments/update-status/{id}', name: 'admin_assignment_update_status', methods: ['POST'])]
    #[IsCsrfTokenValid(new Expression('"update-assignment-" ~ args["assignment"].getId()'), tokenKey: 'token')]
    public function update(
        Assignment $assignment,
        EntityManagerInterface $em,
        Request $request
    ): Response {

        $submittedStatusValue = $request->getPayload()->get('status');

        if (null === $submittedStatusValue) {
            $this->addFlash('error', 'Status value not provided.');
            return $this->redirectToRoute('admin_shift_manage', ['id' => $assignment->getShift()?->getId()]);
        }

        $newStatus = AssignmentStatus::tryFrom($submittedStatusValue);

        if (null === $newStatus) {
            $this->addFlash('error', sprintf('Invalid status value "%s" provided.', $submittedStatusValue));
            return $this->redirectToRoute('admin_shift_manage', ['id' => $assignment->getShift()?->getId()]);
        }
        $assignment->setStatus($newStatus);
        $em->flush();

        $this->addFlash('success', 'Assignment status updated successfully.');

        return $this->redirectToRoute('admin_shift_manage', [
            'id' => $assignment->getShift()->getId()
        ]);
    }

    #[Route('/assignment/{id}', name: 'admin_assignment_delete', methods: ['DELETE'])]
    #[IsCsrfTokenValid(new Expression('"delete-assignment-" ~ args["assignment"].getId()'), tokenKey: 'token')]
    public function delete(
        Assignment $assignment,
        EntityManagerInterface $em,
    ): Response {

        $em->remove($assignment);
        $em->flush();

        $this->addFlash('success', 'Assignment removed successfully.');

        return $this->redirectToRoute('admin_shift_manage', [
            'id' => $assignment->getShift()->getId()
        ]);
    }
}
