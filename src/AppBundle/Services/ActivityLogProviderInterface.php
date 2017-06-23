<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 26.05.17
 * Time: 13:52
 */

namespace AppBundle\Services;


use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;

interface ActivityLogProviderInterface
{
    public function __construct(EntityManagerInterface $entityManager,Logger $logger);

    public function getLogsForToday();

    public function getLogsForDaysRange(\DateTime $from,\DateTime $to);
}