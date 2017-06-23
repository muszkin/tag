<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 24.05.17
 * Time: 11:29
 */

namespace AppBundle\Entity;

use AppBundle\Model\BaseModel;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Model\SourceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class Source
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="source")
 */
class Source extends BaseModel implements SourceInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(name="source_id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="name",type="string",length=255)
     */
    protected $name;

    /**
     * @ORM\Column(name="slug",type="string",length=255)
     */
    protected $slug;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\TeamSource",mappedBy="source")
     */
    protected $team_sources;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\TagList",mappedBy="source")
     */
    protected $tags_list;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->team_sources = new ArrayCollection();
        $this->tags_list = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Source
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Source
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add teamSource
     *
     * @param TeamSource $teamSource
     *
     * @return Source
     */
    public function addTeamSource(TeamSource $teamSource)
    {
        $this->team_sources[] = $teamSource;

        return $this;
    }

    /**
     * Remove teamSource
     *
     * @param TeamSource $teamSource
     */
    public function removeTeamSource(TeamSource $teamSource)
    {
        $this->team_sources->removeElement($teamSource);
    }

    /**
     * Get teamSources
     *
     * @return Collection
     */
    public function getTeamSources()
    {
        return $this->team_sources;
    }

    /**
     * Add tagsList
     *
     * @param TagList $tagsList
     *
     * @return Source
     */
    public function addTagsList(TagList $tagsList)
    {
        $this->tags_list[] = $tagsList;

        return $this;
    }

    /**
     * Remove tagsList
     *
     * @param TagList $tagsList
     */
    public function removeTagsList(TagList $tagsList)
    {
        $this->tags_list->removeElement($tagsList);
    }

    /**
     * Get tagsList
     *
     * @return Collection
     */
    public function getTagsList()
    {
        return $this->tags_list;
    }

    public function getStringForTranslation()
    {
        return $this->getName();
    }
}
