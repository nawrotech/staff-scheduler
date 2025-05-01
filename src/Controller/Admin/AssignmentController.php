<?php

namespace App\Controller\Admin;

use App\Entity\Assignment;
use App\Enum\AssignmentStatus;
use App\Repository\AssignmentRepository;
use App\Validator\AssignmentApprovalLimit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AssignmentController extends AbstractController
{
    #[Route('/assignment/check/fucking/{id}', name: 'app_assignment')]
    public function index(
        Assignment $assignment,
        EntityManagerInterface $em
    ): Response {

        // dd($assignment);

        $assignment->setStatus(AssignmentStatus::PENDING);

        $uow = $em->getUnitOfWork();
        $originalData = $uow->getOriginalEntityData($assignment);

        dd($assignment, $originalData['status']);


        return $this->render('assignment/index.html.twig', [
            'controller_name' => 'AssignmentController',
        ]);
    }

    #[Route('/assignments/update-status/{id}', name: 'admin_assignment_update_status', methods: ['POST'])]
    #[IsCsrfTokenValid(new Expression('"update-assignment-" ~ args["assignment"].getId()'), tokenKey: 'token')]
    public function update(
        Assignment $assignment,
        EntityManagerInterface $em,
        Request $request,
        ValidatorInterface $validator
    ): Response {

        $submittedStatusValue = $request->getPayload()->get('status');

        $redirectRoute = 'admin_shift_manage';
        $redirectParams = ['id' => $assignment->getShift()?->getId() ?? 0];


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

        $violations = $validator->validate($assignment, [
            new AssignmentApprovalLimit()
        ]);

        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $this->addFlash('danger', $violation->getMessage());
            }
            return $this->redirectToRoute($redirectRoute, $redirectParams);
        }

        $em->flush();

        $this->addFlash('success', 'Assignment status updated successfully.');
        return $this->redirectToRoute('admin_shift_manage', [
            'id' => $assignment->getShift()->getId()
        ]);
    }

    #[Route('/assignments/bulk-update-status/{shiftId}', name: 'admin_assignment_bulk_update_status', methods: ['POST'])]
    #[IsCsrfTokenValid('bulk-update-status', tokenKey: 'token')]
    public function bulkUpdateStatus(
        Request $request,
        EntityManagerInterface $em,
        AssignmentRepository $assignmentRepository,
        ValidatorInterface $validator,
        int $shiftId
    ): Response {
        $submittedData = $request->getPayload();
        $assignmentIds = $submittedData->all('assignment_ids');

        $bulkStatusValue = $submittedData->get('bulk_status');

        if (empty($assignmentIds)) {
            $this->addFlash('warning', 'No assignments selected for bulk update.');
            return $this->redirectToRoute('admin_shift_manage', ['id' => $shiftId]);
        }

        if (null === $bulkStatusValue) {
            $this->addFlash('error', 'No bulk status selected.');
            return $this->redirectToRoute('admin_shift_manage', ['id' => $shiftId]);
        }

        $newStatus = AssignmentStatus::tryFrom($bulkStatusValue);

        if (null === $newStatus) {
            $this->addFlash('error', sprintf('Invalid bulk status value "%s" provided.', $bulkStatusValue));
            return $this->redirectToRoute('admin_shift_manage', ['id' => $shiftId]);
        }

        $assignmentsToUpdate = $assignmentRepository->findBy(['id' => $assignmentIds]);

        $updatedCount = 0;
        foreach ($assignmentsToUpdate as $assignment) {
            if ($assignment->getShift()?->getId() === $shiftId) {
                $assignment->setStatus($newStatus);

                $violations = $validator->validate($assignment, [
                    new AssignmentApprovalLimit()
                ]);


                if (count($violations) > 0) {
                    foreach ($violations as $violation) {
                        $this->addFlash('danger', $violation->getMessage());
                    }
                    return $this->redirectToRoute('admin_shift_manage', ['id' => $shiftId]);
                }

                $updatedCount++;
            } else {
                $this->addFlash('warning', sprintf('Assignment ID %d does not belong to this shift and was skipped.', $assignment->getId()));
            }
        }

        if ($updatedCount > 0) {
            $em->flush();
            $this->addFlash('success', sprintf('%d assignment statuses updated successfully to "%s".', $updatedCount, $newStatus->value));
        } else {
            $this->addFlash('warning', 'No assignments were updated. They might not belong to this shift or were already in the target state.');
        }

        return $this->redirectToRoute('admin_shift_manage', ['id' => $shiftId]);
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
