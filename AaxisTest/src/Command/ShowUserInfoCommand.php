<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowUserInfoCommand extends Command
{
    protected static $defaultName = 'app:get-user';

    public function __construct(private readonly UserRepository $userRepository)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Gets a user\'s data by email or ID.')
            ->setHelp('This command allows you to retrieve user data.')
            ->addArgument('identifier', InputArgument::REQUIRED, 'The email or ID of the user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $identifier = $input->getArgument('identifier');

        $user = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? $this->userRepository->findOneBy(['email' => $identifier])
            : $this->userRepository->find($identifier);

        if (!$user) {
            $output->writeln('User not found.');
            return Command::FAILURE;
        }

        $output->writeln('User ID: ' . $user->getId());
        $output->writeln('Email: ' . $user->getEmail());
        $output->writeln('Roles: ' . implode(', ', $user->getRoles()));
        $output->writeln('API Key: ' . $user->getApiKey());

        return Command::SUCCESS;
    }
}
