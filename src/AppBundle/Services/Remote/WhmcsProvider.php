<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 24.05.17
 * Time: 11:43
 */

namespace AppBundle\Services\Remote;


use AppBundle\Model\AgentInterface;
use AppBundle\Model\TagInterface;
use AppBundle\Services\ProviderInterface;

class WhmcsProvider extends AbstractProvider implements ProviderInterface
{

    /**
     * @param \DateTime $date
     * @return TagInterface
     * Get tags from specific day
     */
    public function getTagsFromDay(\DateTime $date)
    {
        // TODO: Implement getTagsFromDay() method.
    }

    /**
     * @param AgentInterface $agent
     * @return TagInterface
     * Get tags for specific agent
     */
    public function getTagsForAgent(AgentInterface $agent)
    {
        // TODO: Implement getTagsForAgent() method.
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @return TagInterface
     * Get tags from date range
     */
    public function getTagsFromRange(\DateTime $from, \DateTime $to)
    {
        // TODO: Implement getTagsFromRange() method.
    }

    /**
     * @param $condition
     * @return AgentInterface
     */
    public function findAgent($condition)
    {
        // TODO: Implement findAgent() method.
    }
}