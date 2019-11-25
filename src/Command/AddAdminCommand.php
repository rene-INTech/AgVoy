<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddAdminCommand extends Command
{
    protected static $defaultName = 'add:admin';

    /**
     * @var $ownersList
     */
    private $usersList;
    private $manager;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->manager = $container->get('doctrine')->getManager();
        $this->usersList = $this->manager->getRepository(User::class);
    }

    protected function configure()
    {
        $this
            ->setDescription('Rend un utilisateur administrateur')
            ->addArgument('username', InputArgument::REQUIRED, "Le nom de l'utilisateur à promouvoir")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');

        $user = $this->usersList->findOneBy(['username'=>$username]);
        $user->addRole('ROLE_ADMIN');
        $this->manager->persist($user);
        $this->manager->flush();
        $io->success($username.' a bien été promu admin!');
    }
}
