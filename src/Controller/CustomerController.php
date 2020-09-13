<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api/customers")
 */
class CustomerController extends AbstractController
{
    /**
     * @Route("/{id}", name="show_customer", methods={"GET"})
     */
    public function show(Customer $customer, CustomerRepository $customerRepository)
    {
        return $this->json($customerRepository->find($customer->getId()), 200, [], ['groups' => 'customer:show']);
    }

    /**
     * @Route("/{page<\d+>?1}", name="list_customer", methods={"GET"})
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
}
