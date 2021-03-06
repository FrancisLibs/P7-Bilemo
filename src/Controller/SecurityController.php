<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder,
        ValidatorInterface $validator)
    {
        $data = json_decode($request->getContent());
        //dd($data);
        if(isset($data->email) && isset($data->password) && isset($data->username))
        {
            $user = new User();
            $user->setEmail($data->email)
                ->setPassword($encoder->encodePassword($user, $data->password))
                ->setRoles($user->getRoles())
                ->setUsername($data->username);

            $errors = $validator->validate($user);

            if (count($errors) > 0) {
                return $this->json($errors, 400);
            }

            $manager->persist($user);
            $manager->flush();

            return $this->json([
                "Status" => 201,
                "message" => "The user was created"
            ], 201);
        }
        return $this->json([
            "Status" => 500,
            "message" => "The email, password and username are required"
        ], 500);
    }
}
