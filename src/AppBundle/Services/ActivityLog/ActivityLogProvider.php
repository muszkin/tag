<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 26.05.17
 * Time: 13:52
 */

namespace AppBundle\Services\ActivityLog;


use AppBundle\Services\ActivityLogProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;

class ActivityLogProvider implements ActivityLogProviderInterface
{
    /** @var  EntityManagerInterface */
    private $em;

    /** @var  Logger */
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, Logger $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger;
    }

    public function getLogsForToday()
    {
        $today = new \DateTime();
        $from = clone $today;
        $from->setTime(0,0,0);
        $to = clone $today;
        $to->setTime(23,59,59);

        $activityLogs = $this->em->getRepository('AppBundle:ActivityLog')->getActivityLog($from,$to);

        return $activityLogs;
    }

    public function getLogsForDaysRange(\DateTime $from,\DateTime $to)
    {
        $from->setTime(0,0,0);
        $to->setTime(23,59,59);

        $activityLogs = $this->em->getRepository('AppBundle:ActivityLog')->getActivityLog($from,$to);

        return $activityLogs;
    }
}