<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

/**
 * @Route("/api/")
 */
class CustomerController extends AbstractController
{
    /**
     * @Route("customers/{id}", name="show_customer", methods={"GET"})
     */
    public function show(Customer $customer, CustomerRepository $customerRepository)
    {
        return $this->json($customerRepository->find($customer->getId()), 200, [], ['groups' => 'customer:show']);
    }

    /**
     * @Route("customers/{page<\d+>?1}", name="list_customer", methods={"GET"})
     */
    public function index(Request $request, CustomerRepository $customerRepository)
    {
        $page = $request->query->get('page');

        if (is_null($page) || $page < 1) {
            $page = 1;
        }
       
        $customers = $customerRepository->findAllCustomers($page, $_ENV['LIMIT']);

        return $this->json($customers, 200, [], ['groups' => ['customer:list']]);
    }

    /**
     * @Route("customers/", name="add_customer", methods={"POST"})
     */
    public function new(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $json= $request->getContent();

        try{
            $customer = $serializer->deserialize($json, Customer::class, 'json');

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
}
