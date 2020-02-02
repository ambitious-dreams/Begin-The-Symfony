<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class MarkInactiveUsersCommand extends Command
{
    protected static $defaultName = 'app:mark-inactive-users';

    private $userRepository;
    private $em;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Marks user as inactive after 1 month of inactivity.')
            ->setHelp('Marks user as inactive after 1 month of inactivity.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $users = $this->userRepository->findInactiveForMonth();

        foreach ($users as $user) {
            $user->setInactive(true);
        }

        $this->em->flush();
        $output->writeln('Done!');

        return 0;
    }
}