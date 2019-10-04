<?php

namespace App\Command;

use App\Entity\Room;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ListRoomsCommand extends Command
{
    protected static $defaultName = 'list:rooms';

    /**
     * @var $roomsList
     */
    private $roomsList;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->roomsList = $container->get('doctrine')->getManager()->getRepository(Room::class);
    }

    protected function configure()
    {
        $this
            ->setDescription('Liste toutes les chambres enregistrées sur le site')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->roomsList->findAll() as $room){
            $output->writeln($room);
        }
    }
}
