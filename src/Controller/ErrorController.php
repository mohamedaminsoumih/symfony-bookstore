<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    /**
     * @Route("/error/{errorMessage}", name="generic_error", methods={"GET"})
     */
    public function errorMessage(string $errorMessage): Response
    {
        return $this->render('error/generic.html.twig', [
            'errorMessage' => $errorMessage,
        ]);
    }
}