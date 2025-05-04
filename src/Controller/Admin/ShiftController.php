<?php

namespace App\Controller\Admin;

use App\Entity\Assignment;
use App\Entity\Shift;
use App\Form\ShiftType;
use App\Repository\AssignmentRepository;
use App\Service\NotifierService;
use App\Service\ShiftService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/shifts')]
class ShiftController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private NotifierService $notifierService
    ) {}


    #[Route('/{id}/delete', name: 'shift_delete', methods: ['POST'])]
    #[IsCsrfTokenValid(new Expression('"delete-shift-" ~ args["shift"].getId()'), tokenKey: 'token')]
    public function delete(
        Shift $shift,
        EntityManagerInterface $em,
    ): Response {
        $shiftDate = $shift->getDate()->format('Y-m-d');
        $startTime = $shift->getStartTime()->format('H:i');
        $endTime = $shift->getEndTime()->format('H:i');

        $em->remove($shift);
        $em->flush();

        $this->addFlash(
            'success',
            sprintf(
                'Shift on %s from %s to %s has been deleted successfully.',
                $shiftDate,
                $startTime,
                $endTime
            )
        );

        return $this->redirectToRoute('shift_calendar');
    }

    #[Route('/{id}/manage', name: 'shift_manage', methods: ['GET'])]
    public function manage(Shift $shift, AssignmentRepository $assignmentRepository): Response
    {
        $shiftAssignments = $assignmentRepository->findBy(['shift' => $shift], ['assignedAt' => 'ASC']);

        $assignmentsByPosition = array_reduce($shiftAssignments, function ($carry, Assignment $assignment) {
            $positionName = $assignment->getShiftPosition()?->getName() ?? 'Unassigned';
            $carry[$positionName->value][] = $assignment;
            return $carry;
        }, []);

        return $this->render('admin/shift/manage.html.twig', [
            'shift' => $shift,
            'assignmentsByPosition' => $assignmentsByPosition
        ]);
    }


    #[Route('/create/{id?}', name: 'shift_create', methods: ['GET', 'POST'])]
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
}
