<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 25.05.17
 * Time: 16:17
 */

namespace AppBundle\Services\Remote;


use AppBundle\Entity\ActivityLog;
use AppBundle\Entity\Tag;
use AppBundle\Services\ProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;

abstract class AbstractProvider
{

    /** @var  EntityManagerInterface */
    protected $em;

    /** @var  Logger */
    protected $logger;

    /**
     * AbstractProvider constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager,Logger $logger)
    {
        $this->em = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @param $tag_name
     * @return Tag
     * @throws \Exception
     */
    public function findTag($tag_name)
    {
        $tag = $this->em->getRepository('AppBundle:Tag')->findOneBy(
            [
                "slug" => $tag_name
            ]
        );
        if (!$tag){
            throw new \Exception("Tag $tag_name not found in database");
        }

        return $tag;
    }

    public function storeErrorIntoDb($description)
    {
        $activityLog = new ActivityLog();
        $activityLog->setDate(new \DateTime());
        $activityLog->setDescription($description);

        $this->em->persist($activityLog);
        $this->em->flush();
    }
}