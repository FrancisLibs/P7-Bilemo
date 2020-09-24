<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Security as SWGSecurity;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @Route("/api", name ="customers")
 */
class CustomerController extends AbstractController
{
    private $repository;

    public function __construct(CustomerRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/customers/{id}", name="show_customer", methods={"GET"})
     * @SWG\Tag(name="Customers")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the informations of a customer",
     *     @SWG\Schema(
     *         type="array",
     *         example={},
     *         @SWG\Items(ref=@Model(type=Customer::class, groups={"customer:show"}))
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Resource not found"
     * )
     * 
     * @param Customer $customer
     * @return Response
     */
    public function show(Customer $customer)
    {
        $user = $this->getUser();
        $customer = $this->repository->findCustomer($customer->getId(), $user);

        if (!$customer) {
            return $this->json([
                "status" => 404,
                "message" => "No customer found"
            ], 404, [], []);
        }

        return $this->json($customer, 200, [], ['groups' => 'customer:show']);
    }

    /**
     * @Route("/customers", name="list_customer", methods={"GET"})
     * @Route("/customers/{page<\d+>?1}", name="list_customer_paginated", methods={"GET"})
     * @SWG\Tag(name="Customers")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of customers",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Resource not found"
     * )
     * 
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $user = $this->getUser();

        $page = $request->query->get('page');

        if (is_null($page) || $page < 1) {
            $page = 1;
        }
        
        $customers = $this->repository->findAllCustomers($page, $_ENV['LIMIT'], $user);

        if (count($customers) == null)
        {
            return $this->json([
                "status" => 404,
                "message" => "No customers found"
            ], 404, [], []);
        }

        return $this->json($customers, 200, [], ['groups' => ['customer:list']]);
    }

    /**
     * @Route("/customers", name="add_customer", methods={"POST"})
     * @SWG\Tag(name="Customers")
     * @SWG\Response(
     *     response=201,
     *     description="Add a new customer",
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Bad request"
     * )
     * 
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $user = $this->getUser();
    
        $json= $request->getContent();

        try{
            $customer = $serializer->deserialize($json, Customer::class, 'json');
            $customer->setUser($user);

            $errors = $validator->validate($customer);

            if(count($errors) > 0){
                return $this->json($errors, 400);
            }

            $manager->persist($customer);
            $manager->flush();

            return $this->json($customer, 201, [], ['groups' => 'customer:show']);

        } catch (NotEncodableValueException $e) {
            return $this->json([
                "status" => 400,
                "message" => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @Route(":customers/{id}", name="delete_customer", methods={"DELETE"})
     * @SWG\Tag(name="Customers")
     * @SWG\Response(
     *     response=204,
     *     description="Delete an existing customer",
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Access denied"
     * )
     * 
     * @param Customer $customer
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Customer $customer, EntityManagerInterface $manager)
    {
        $user = $this->getUser();

        $customer = $this->repository->findCustomer($customer->getId(), $user);

        if (!$customer) {
            return $this->json([
                "status" => 500,
                "message" => "Access denied"
            ], 404, [], []);
        }

        $manager->remove($customer);
        $manager->flush();

        return $this->json([
            "status"    => 204,
            "message"   => "Customer succesfully deleted"
        ], 200);
    }
}
