<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 24.05.17
 * Time: 12:31
 */

namespace AppBundle\Entity;

use AppBundle\Model\BaseModel;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Model\TeamSourceInterface;

/**
 * Class TeamSource
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="team_source")
 */
class TeamSource extends BaseModel implements TeamSourceInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(name="team_source_id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Source",inversedBy="team_sources")
     * @ORM\JoinColumn(name="source_id",referencedColumnName="source_id")
     */
    protected $source;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Team",inversedBy="team_sources")
     * @ORM\JoinColumn(name="team_id",referencedColumnName="team_id")
     */
    protected $team;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set source
     *
     * @param Source $source
     *
     * @return TeamSource
     */
    public function setSource(Source $source = null)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return Source
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set team
     *
     * @param Team $team
     *
     * @return TeamSource
     */
    public function setTeam(Team $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    public function __toString()
    {
        return "Team {$this->getTeam()->getName()} have source {$this->getSource()->getName()}";
    }
}
