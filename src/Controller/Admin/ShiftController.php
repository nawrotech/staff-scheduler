<?php

namespace App\Controller\Admin;

use App\Entity\Assignment;
use App\Entity\Shift;
use App\Repository\AssignmentRepository;
use App\Service\NotifierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/shifts')]
class ShiftController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        // private AssignmentService $assignmentService,
        private NotifierService $notifierService
    ) {}

    #[Route('/{id}', name: 'admin_shift_manage')]
    public function manage(Shift $shift, AssignmentRepository $assignmentRepository): Response
    {

        $shiftAssignments = $assignmentRepository->findBy(['shift' => $shift], ['assignedAt' => 'ASC']);

        $assignmentsByPosition = [];
        foreach ($shiftAssignments as $assignment) {
            $positionName = $assignment->getShiftPosition()?->getName() ?? 'Unassigned';
            $assignmentsByPosition[$positionName->value][] = $assignment;
        }

        // $assignmentsByPosition = array_reduce($shiftAssignments, function ($carry, Assignment $assignment) {
        //     $positionName = $assignment->getShiftPosition()?->getName() ?? 'Unassigned';
        //     $assignmentsByPosition[$positionName->value][] = $assignment;
        // }, []);


        return $this->render('admin/shift/manage.html.twig', [
            'shift' => $shift,
            'assignmentsByPosition' => $assignmentsByPosition
        ]);
    }
}
