<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class AssignmentController extends AbstractController
{
    #[Route('/assignment', name: 'app_assignmsfsfsdsfent')]
    public function index(): Response
    {
        return $this->render('assignment/index.html.twig', [
            'controller_name' => 'AssignmentController',
        ]);
    }

    // #[Route('/assignments/{id}')]



    // #[IsGranted(['ROLE_USER'])
}
