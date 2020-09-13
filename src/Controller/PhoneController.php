<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api/phones")
 */
class PhoneController extends AbstractController
{
    /**
     * @Route("/{page<\d+>?1}", name="list_phones", methods={"GET"})
     */
    public function index(Request $request, PhoneRepository $phoneRepository, SerializerInterface $serializer)
    {
        $page = $request->query->get('page');

        if (is_null($page) || $page < 1) {
            $page = 1;
        }
       
        $phones = $phoneRepository->findAllPhones($page, $_ENV['LIMIT']);

        return $this->json($phones, 200, [], []);
    }
}
