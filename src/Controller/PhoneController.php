<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api/")
 */
class PhoneController extends AbstractController
{
    /**
     * @Route("phones/{id}", name="show_phone", methods={"GET"})
     */
    public function show(Phone $phone, PhoneRepository $repository)
    {
        return $this->json($repository->find($phone->getId()), 200, [], ['groups' => 'phone:show']);
    }

    /**
     * @Route("phones/{page<\d+>?1}", name="list_phones", methods={"GET"})
     */
    public function index(Request $request, PhoneRepository $repository)
    {
        $page = $request->query->get('page');

        if (is_null($page) || $page < 1) {
            $page = 1;
        }
       
        $phones = $repository->findAllPhones($page, $_ENV['LIMIT']);

        return $this->json($phones, 200, [], ['groups' => ['phone:list']]);
    }
}
