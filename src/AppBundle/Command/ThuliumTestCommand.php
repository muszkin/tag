<?php

namespace AppBundle\Command;

use AppBundle\Entity\Source;
use AppBundle\Entity\Tag;
use AppBundle\Entity\TagList;
use AppBundle\Entity\Team;
use AppBundle\Entity\TeamSource;
use AppBundle\Services\ProviderInterface;
use AppBundle\Services\Remote\KayakoProvider;
use AppBundle\Services\Remote\ThuliumProvider;
use AppBundle\Services\Remote\WhmcsProvider;
use const PEAR_INSTALL_DIR;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ThuliumTestCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:tag:thulium')
            ->addOption("from","f",InputArgument::OPTIONAL,"From date")
            ->addOption("to","t",InputArgument::OPTIONAL,"To date")
            ->addOption("connection","c",InputArgument::OPTIONAL,"Connection id")
            ->setDescription("Command to import tags for teams.\n
             If you pass from date it will import data from this date to today.\n
             You can also provider to date.")
            ->addUsage("php bin/console app:tag:import:daily [(-f|--from) '2017-05-01'] [(-t|--to) '2017-05-31']")
        ;

    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $from = $input->getOption('from');
        $to = $input->getOption('to');
        $connection_id= $input->getOption('connection');

        $container = $this->getContainer();
        $yesterday = new \DateTime('today -1day');
	    $today = new \DateTime('today');

        $thuliumProvider = $container->get('tag.provider.thulium');
        $thuliumProvider->setOutput($output);
        $thuliumProvider->getConnections(((!$from)?$yesterday:new \DateTime($from)),((!$to)?$today:new \DateTime($to)),$connection_id);

        //var_dump($thuliumProvider->getConnectionComment("1497880489.14990"));
        //var_dump($thuliumProvider->getConnectionComment("1497334734.55"));
        //var_dump($thuliumProvider->getConnectionComment("1497334132.9"));

        $list = $thuliumProvider->getListOfConnections();

//        foreach ($list as $connection){
//            $output->writeln($thuliumProvider->getConnectionComment($connection['connection_id']));
//        }
        $x = 1;
        foreach ($list as $key => $value){
            $list[$key]['Lp.'] = $x;
            $x++;
        }
        $table = new Table($output);
        $table
            ->setHeaders(array_keys($list[0]))
            ->setRows($list)
        ;

        $table->render();
    }
}
