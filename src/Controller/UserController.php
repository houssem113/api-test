<?php

namespace App\Controller;

use App\Entity\User;
use App\Serializer\DataSerializer;
use App\Validator\DataValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{

    #[Route('/api/signup', name: 'user_signup', methods: ["POST"])]
    public function signUp(
        Request $request,
        DataSerializer $serializer,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        DataValidator $validator
    ): JsonResponse {

        $data = $request->toArray();

        $user = $serializer->deseialize($data, User::class);

        $isValid = $validator->validate($user);

        if(!$isValid) {

            $errors = $validator->getErrors();

            return $this->json($errors, 400);
        }

        $plainPassword = $user->getPassword();

        $hashedPassword = $hasher->hashPassword($user, $plainPassword);

        $user->setPassword($hashedPassword);

        $em->persist($user);

        $em->flush();

        return new JsonResponse($serializer->serialize($user, [
             "groups" => "user:read"
        ]), 201,  [], true);
    }
}
