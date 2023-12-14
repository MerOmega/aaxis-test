<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateDummyUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';


    public function __construct(private readonly EntityManagerInterface      $entityManager,
                                private readonly UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates a dummy user.')
            ->setHelp('This command allows you to create a user with dummy or custom data for testing purposes.')
            ->addArgument('email', InputArgument::OPTIONAL, 'The email of the user')
            ->addArgument('password', InputArgument::OPTIONAL, 'The password for the user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();

        $email = $input->getArgument('email') ?? 'aaxis@test.com';
        $password = $input->getArgument('password') ?? 'aaxis_test';

        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $password
        );
        $user->setPassword($hashedPassword);

        $user->setApiKey(bin2hex(random_bytes(30)) . '_' . time());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('User created successfully.');

        return Command::SUCCESS;
    }
}