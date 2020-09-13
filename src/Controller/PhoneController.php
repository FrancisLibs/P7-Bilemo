<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PhoneController extends AbstractController
{
    /**
     * @Route("/api/phone", name="phone")
     */
    public function index()
    {
        $data = [
            'name' => 'iPhone X',
            'price' => 1000
        ];

        return new JsonResponse($data);
    }
}
