<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Phone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PhoneFixtures extends Fixture
{
    private $brands = ['Apple', 'Samsung', 'nokia', 'Sony', 'Motorola', 'Huawei'];
    private $colors = ['black', 'white', 'red', 'white', 'blue'];

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= 20; $i++) {
            $phone = new Phone();
            $phone->setBrand($this->brands[rand(0, 1)] . ' ' . rand(5, 8));
            $phone->setColor($this->colors[rand(0, 1)]);
            $phone->setPrice(rand(500, 1000));
            $phone->setDescription('A wonderful phone with ' . rand(10, 50) . ' tricks');

            $manager->persist($phone);
        }

        for($i = 1; $i <= 20; $i++)
        {
            $customer = new Customer();
            $customer
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName)
                ->setEmail($faker->email)
                ->setCompany($faker->company)
            ;

            $manager->persist($customer);
        }
        $manager->flush();
    }
}
