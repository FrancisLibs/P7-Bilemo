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

/**
 * @Route("/api/")
 */
class CustomerController extends AbstractController
{
    private $security;
    private $repository;

    public function __construct(Security $security, CustomerRepository $repository)
    {
        $this->security = $security;
        $this->repository = $repository;
    }

    /**
     * @Route("customers/{id}", name="show_customer", methods={"GET"})
     */
    public function show(Customer $customer)
    {
        $user = $this->security->getUser();

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
     * @Route("customers/{page<\d+>?1}", name="list_customer", methods={"GET"})
     */
    public function index(Request $request)
    {
        $user = $this->security->getUser();

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
     * @Route("customers", name="add_customer", methods={"POST"})
     */
    public function new(Request $request, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $user = $this->security->getUser();
    
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
     * @Route("customers/{id}", name="delete_customer", methods={"DELETE"})
     */
    public function delete(Customer $customer, EntityManagerInterface $manager)
    {
        $user = $this->security->getUser();

        $customer = $this->repository->findCustomer($customer->getId(), $user);

        $manager->remove($customer);
        $manager->flush();

        return $this->json([
            "status"    => 200,
            "message"   => "The client has been deleted"
        ], 200);
    }
}
