<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 24.05.17
 * Time: 10:43
 */

namespace AppBundle\Services;

use AppBundle\Model\AgentInterface;
use AppBundle\Model\TagInterface;

interface ProviderInterface
{

    /**
     * @param \DateTime $date
     * @return TagInterface
     * Get tags from specific day
     */
    public function getTagsFromDay(\DateTime $date);

    /**
     * @param AgentInterface $agent
     * @return TagInterface
     * Get tags for specific agent
     */
    public function getTagsForAgent(AgentInterface $agent);

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @return TagInterface
     * Get tags from date range
     */
    public function getTagsFromRange(\DateTime $from,\DateTime $to);

    /**
     * @param $condition
     * @return AgentInterface
     */
    public function findAgent($condition);

    /**
     * @param string $description
     */
    public function storeErrorIntoDb($description);
}