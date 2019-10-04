<?php

namespace App\Command;

use App\Entity\Owner;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListOwnersCommand extends Command
{
    protected static $defaultName = 'list:owners';

    /**
     * @var $ownersList
     */
    private $ownersList;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->ownersList = $container->get('doctrine')->getManager()->getRepository(Owner::class);
    }

    protected function configure()
    {
        $this
            ->setDescription('Liste tous les propriétaires enregistrés sur le site')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->ownersList->findAll() as $owner){
            $output->writeln($owner);
        }
    }
}
