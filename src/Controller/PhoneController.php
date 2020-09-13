<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api/phones")
 */
class PhoneController extends AbstractController
{
    /**
     * @Route("/", name="list_phones", methods={"GET"})
     */
    public function index(PhoneRepository $phoneRepository, SerializerInterface $serializer)
    {
        return $this->json($phoneRepository->findAll(), 200, [], []);
    }
}
