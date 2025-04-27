<?php

namespace App\Controller;

use App\Entity\Assignment;
use App\Entity\Shift;
use App\Entity\User;
use App\Enum\AssignmentStatus;
use App\Repository\ShiftPositionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AssignmentController extends AbstractController
{
    #[Route('/assignment', name: 'app_assignment')]
    public function index(): Response
    {
        return $this->render('assignment/index.html.twig', [
            'controller_name' => 'AssignmentController',
        ]);
    }

    // #[IsGranted(['ROLE_USER'])]
    #[Route('/shifts/{id}/apply', name: 'assignment_shift_apply', methods: ['POST'])]
    public function apply(
        Shift $shift,
        #[CurrentUser()] User $user,
        EntityManagerInterface $em,
        ShiftPositionRepository $shiftPositionRepository,
        Request $request
    ): Response
    {
        $positionId = $request->getPayload()->get('position_id');
        $shiftPosition = $shiftPositionRepository->find($positionId);

        if ($shiftPosition->getName() !== $user->getStaffProfile()->getPosition()) {
            $this->addFlash('danger', 'You don\'t have required position for that shift!');
            return $this->redirectToRoute('shift_show', ['id' => $shift->getId()]);
        }

        if (!$shiftPosition || $shiftPosition->getShift()->getId() !== $shift->getId()) {
            $this->addFlash('danger', 'Invalid position selected.');
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

        $this->addFlash('success', 'Successfully applied for the shift!');
        return $this->redirectToRoute('shift_show', ['id' => $shift->getId()]);

    }

}
