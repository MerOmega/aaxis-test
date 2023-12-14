<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

#[Route('/api/user')]
class UserController extends AbstractController
{

    /**
     * Act as a login endpoint for the user to get the api_key and use it in the other endpoints
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @param UserPasswordHasherInterface $passwordEncoder
     * @return Response
     */
    #[Route('', name: 'app_user', methods: ['POST'])]
    public function login(Request $request, UserRepository $userRepository,
                          UserPasswordHasherInterface $passwordEncoder): Response
    {
        $data = json_decode($request->getContent(), true);

        $email    = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $user = $userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found.'], 401);
        }

        if (!$passwordEncoder->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Invalid password.'], 401);
        }

        return new JsonResponse([
                                    'id'      => $user->getId(),
                                    'email'   => $user->getEmail(),
                                    'roles'   => $user->getRoles(),
                                    'api_key' => $user->getApiKey(),
                                ]);
    }

    /**
     * This endpoint is used to create a new user
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     * @return JsonResponse
     * @throws \Exception
     */
    #[Route('/create', name: 'app_create_user', methods: ['POST'])]
    public function createUser(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);


        $user = new User();
        $user->setEmail($data['email']);
        $user->setApiKey(bin2hex(random_bytes(30)) . '_' . time());
        $user->setRoles(['ROLE_USER']);

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $data['password']
        );
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();


        return $this->json([
                               'message' => 'User created successfully',
                               'user' => [
                                   'id' => $user->getId(),
                                   'email' => $user->getEmail(),
                                   'roles' => $user->getRoles(),
                                   'api_key' => $user->getApiKey(),
                               ],
                           ], 201);
    }
}
