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
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TagProviderCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:tag:import:daily')
            ->addOption("from","f",InputArgument::OPTIONAL,"From date")
            ->addOption("to","t",InputArgument::OPTIONAL,"To date")
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

        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();

	    $today = new \DateTime('today -1day');

        $teams = $em->getRepository("AppBundle:Team")->findAll();

        if (empty($teams)){
            $output->writeln("No teams added");
            die();
        }
        /** @var Team $team */
        foreach ($teams as $team){
            $sources = $team->getTeamSources();
            if (empty($sources)){
                $output->writeln("No sources configured for team {$team}");
                $output->writeln("skipping.");
                break;
            }else {
                /** @var TeamSource $source */
                foreach ($sources as $source) {
                    try {
                        /** @var KayakoProvider|ThuliumProvider|WhmcsProvider $tagProvider */
                        if ("tag.provider.thulium" == $source->getSource()->getSlug()){
                            continue;
                        }
                        $tagProvider = $container->get($source->getSource()->getSlug());
                    }catch (\Exception $exception){
                        $output->writeln("Wrong service (slug) in source");
                    }
                    if (!$from && !$to){
                        $tags = $tagProvider->getTagsFromDay($today);
                    }else if ($from && !$to){
                        $tags = $tagProvider->getTagsFromRange(new \DateTime($from),new \DateTime("today"));
                    }else if (!$from && $to){
                        $tags = $tagProvider->getTagsFromRange(new \DateTime($to),new \DateTime("today"));
                    }else {
                        $tags = $tagProvider->getTagsFromRange(new \DateTime($from),new \DateTime($to));
                    }
                    foreach ($tags as $tag){
                        $tagDuplicate = $em->getRepository('AppBundle:TagList')->findOneBy([
                            "date" => new \DateTime($tag['date']),
                            "team" => $team,
                            "source" => $source->getSource(),
                            "agent" => $tag['agent'],
                            "category" => $tag['tag']->getCategory(),
                            "tag" => $tag['tag'],
                            "ticket_id" => ((isset($tag['ticket_id']))?$tag['ticket_id']:null),
                            "connection_id" => ((isset($tag['connection_id']))?$tag['connection_id']:null),
                        ]);
                        if ($tagDuplicate){
                            continue;
                        }

                        $tagList = new TagList();
                        $tagList->setDate(new \Datetime($tag['date']));
                        $tagList->setTeam($team);
                        $tagList->setSource($source->getSource());
                        $tagList->setAgent($tag['agent']);
                        $tagList->setCategory($tag['tag']->getCategory());
                        $tagList->setTag($tag['tag']);
                        if (isset($tag['ticket_id'])) $tagList->setTicketId($tag['ticket_id']);
                        if (isset($tag['connection_id'])) $tagList->setConnectionId($tag['connection_id']);

                        $em->persist($tagList);
                        $em->flush();
                        $output->writeln("Added {$tagList}");
                    }
                }

            }
        }
        try {
            /** @var ThuliumProvider $tagProvider */
            $tagProvider = $container->get("tag.provider.thulium");
            $tagProvider->setOutput($output);
        }catch (\Exception $exception){
            $output->writeln("Wrong service (slug) in source");
        }
        if (!$from && !$to){
            $tags = $tagProvider->getTagsFromDay($today);
        }else if ($from && !$to){
            $tags = $tagProvider->getTagsFromRange(new \DateTime($from),new \DateTime("today"));
        }else if (!$from && $to){
            $tags = $tagProvider->getTagsFromRange(new \DateTime($to),new \DateTime("today"));
        }else {
            $tags = $tagProvider->getTagsFromRange(new \DateTime($from),new \DateTime($to));
        }
        $source = $em->getRepository('AppBundle:Source')->findOneBy([
            "slug" => "tag.provider.thulium"
        ]);

        foreach ($tags as $tag){
            $tagDuplicate = $em->getRepository('AppBundle:TagList')->findOneBy([
                "date" => new \DateTime($tag['date']),
                "team" => $tag['agent']->getTeam(),
                "source" => $source,
                "agent" => $tag['agent'],
                "category" => $tag['tag']->getCategory(),
                "tag" => $tag['tag'],
                "ticket_id" => ((isset($tag['ticket_id']))?$tag['ticket_id']:null),
                "connection_id" => ((isset($tag['connection_id']))?$tag['connection_id']:null),
            ]);
            if ($tagDuplicate){
                continue;
            }

            $tagList = new TagList();
            $tagList->setDate(new \Datetime($tag['date']));
            $tagList->setTeam($tag['agent']->getTeam());
            $tagList->setSource($source);
            $tagList->setAgent($tag['agent']);
            $tagList->setCategory($tag['tag']->getCategory());
            $tagList->setTag($tag['tag']);
            if (isset($tag['ticket_id'])) $tagList->setTicketId($tag['ticket_id']);
            if (isset($tag['connection_id'])) $tagList->setConnectionId($tag['connection_id']);

            $em->persist($tagList);
            $em->flush();
            $output->writeln("Added {$tagList}");
        }
    }
}
