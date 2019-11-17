<?php

namespace App\Command;

use App\Entity\User;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListUsersCommand extends Command
{
    protected static $defaultName = 'list:users';

    /**
     * @var $ownersList
     */
    private $usersList;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->usersList = $container->get('doctrine')->getManager()->getRepository(User::class);
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->usersList->findAll() as $user){
            $output->writeln($user->getUsername());
        }
    }
}
