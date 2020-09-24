<?php

namespace App\Controller;

use App\Entity\Phone;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use App\Repository\PhoneRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api", name="phones")
 * 
 */
class PhoneController extends AbstractController
{
    private $repository;

    public function __construct(PhoneRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/phones/{id}", name="show_phone", methods={"GET"})
     * @SWG\Tag(name="Phone")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the informations of a phone",
     *     @SWG\Schema(
     *         type="array",
     *         example={},
     *         @SWG\Items(ref=@Model(type=Phone::class, groups={"phone:show"}))
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Resource not found"
     * )
     * @param PhoneRepository $repository
     * @return Response
     */
    public function show(Phone $phone)
    {
        $phone = $this->repository->find($phone->getId());

        if(!$phone)
        {
            return $this->json([
                "status" => 404,
                "message" => "Resource not found"
            ],404, [], []);
        }
        return $this->json($phone, 200, [], ['groups' => 'phone:show']);
    }

    /**
     * @Route("/phones", name="list_phone", methods={"GET"})
     * @Route("/phones/{page<\d+>?1}", name="list_phones_paginated", methods={"GET"})
     * @SWG\Tag(name="Phone")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of phones"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Resource not found"
     * )
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $page = $request->query->get('page');

        if (is_null($page) || $page < 1) {
            $page = 1;
        }
        
        $phones = $this->repository->findAllPhones($page, $_ENV['LIMIT']);

        if(count($phones) > 0)
        {
            return $this->json($phones, 200, [], ['groups' => ['phone:list']]);
        }

        return $this->json([
            "status" => 404,
            "message" => "Resource not found"
        ], 404, [], []);
    }
}
