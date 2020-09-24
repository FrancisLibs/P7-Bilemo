<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @Route("/api")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="register", methods={"POST"})
     * @SWG\Tag(name="Users")
     * @SWG\Response(
     *     response=201,
     *     description="Register a user",
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Validation errors"
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Email, password or username missing"
     * )
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder,
        ValidatorInterface $validator)
    {
        $data = json_decode($request->getContent());
        if(isset($data->email, $data->password, $data->username))
        {
            $user = new User();
            $user->setEmail($data->email);
            $user->setPassword($encoder->encodePassword($user, $data->password));
            $user->setRoles($user->getRoles());
            $user->setUsername($data->username);

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

    /**
     * @Route("/login_check", name="login_check", methods={"POST"})
     * @SWG\Tag(name="Register and Log in")
     * @SWG\Response(
     *     response=200,
     *     description="Get authentication token",
     *     @SWG\Schema(
     *         type="array",
     *         example={"username": "user1", "password": "pass"},
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     */
    public function login_check()
    {
    }
}
