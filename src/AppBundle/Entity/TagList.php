<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 24.05.17
 * Time: 12:16
 */

namespace AppBundle\Entity;

use AppBundle\Model\BaseModel;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Model\TagListInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class TagList
 * @package AppBundle\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TagListRepository")
 * @ORM\Table(name="tag_list")
 */
class TagList extends BaseModel implements TagListInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(name="tag_list_id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="date",type="datetime")
     */
    protected $date;

    /**
     * @ORM\Column(name="ticket_id",type="integer",nullable=true)
     */
    protected $ticket_id;

    /**
     * @ORM\Column(name="connection_id",type="string",nullable=true)
     */
    protected $connection_id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tag",inversedBy="tag_list")
     * @ORM\JoinColumn(name="tag_id",referencedColumnName="tag_id")
     */
    protected $tag;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Agent",inversedBy="tags_list")
     * @ORM\JoinColumn(name="agent_id",referencedColumnName="agent_id")
     */
    protected $agent;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Team",inversedBy="tags_list")
     * @ORM\JoinColumn(name="team_id",referencedColumnName="team_id")
     */
    protected $team;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Source",inversedBy="tags_list")
     * @ORM\JoinColumn(name="source_id",referencedColumnName="source_id")
     */
    protected $source;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Category",inversedBy="tags_list")
     * @ORM\JoinColumn(name="category_id",referencedColumnName="category_id")
     */
    protected $category;

    protected $count;

    public function __toString()
    {
        $date = $this->getDate();
        return json_encode([
            "id" => $this->getId(),
            "date" => $date->format('Y-m-d H:i:s'),
            "agent" => $this->getAgent()->getName(),
            "source" => $this->getSource()->getName(),
            "category" => $this->getCategory()->getName(),
            "tag" => $this->getTag()->getName(),
            "team" => $this->getTeam()->getName()
        ]);
    }

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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return TagList
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set tag
     *
     * @param \AppBundle\Entity\Tag $tag
     *
     * @return TagList
     */
    public function setTag(\AppBundle\Entity\Tag $tag = null)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return \AppBundle\Entity\Tag
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set agent
     *
     * @param \AppBundle\Entity\Agent $agent
     *
     * @return TagList
     */
    public function setAgent(\AppBundle\Entity\Agent $agent = null)
    {
        $this->agent = $agent;

        return $this;
    }

    /**
     * Get agent
     *
     * @return \AppBundle\Entity\Agent
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * Set team
     *
     * @param \AppBundle\Entity\Team $team
     *
     * @return TagList
     */
    public function setTeam(\AppBundle\Entity\Team $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return \AppBundle\Entity\Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set source
     *
     * @param \AppBundle\Entity\Source $source
     *
     * @return TagList
     */
    public function setSource(\AppBundle\Entity\Source $source = null)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return \AppBundle\Entity\Source
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set category
     *
     * @param \AppBundle\Entity\Category $category
     *
     * @return TagList
     */
    public function setCategory(\AppBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \AppBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return mixed
     */
    public function getTicketId()
    {
        return $this->ticket_id;
    }

    /**
     * @param mixed $ticket_id
     */
    public function setTicketId($ticket_id)
    {
        $this->ticket_id = $ticket_id;
    }

    /**
     * @return mixed
     */
    public function getConnectionId()
    {
        return $this->connection_id;
    }

    /**
     * @param mixed $connection_id
     */
    public function setConnectionId($connection_id)
    {
        $this->connection_id = $connection_id;
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param mixed $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }
}
