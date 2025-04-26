<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ShiftController extends AbstractController
{
    #[Route('/', name: 'app_shift')]
    public function index(): Response
    {
        return $this->render('shift/index.html.twig', [
            'controller_name' => 'ShiftController',
        ]);
    }
}
