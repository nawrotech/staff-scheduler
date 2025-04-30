<?php

namespace App\Controller\Admin;

use App\Entity\Assignment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
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

    #[Route('/assignment/{id}', name: 'admin_assignment_delete', methods: ['DELETE'])]
    #[IsCsrfTokenValid(new Expression('"delete-assignment-" ~ args["assignment"].getId()'), tokenKey: 'token')]
    public function delete(
        Assignment $assignment,
        EntityManagerInterface $em,
    ): Response {

        $shiftId = $assignment->getShift()->getId();

        $em->remove($assignment);
        $em->flush();

        $this->addFlash('success', 'Assignment removed successfully.');

        return $this->redirectToRoute('admin_shift_manage', [
            'id' => $shiftId
        ]);
    }
}
