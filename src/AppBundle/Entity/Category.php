<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 24.05.17
 * Time: 11:40
 */

namespace AppBundle\Entity;

use AppBundle\Model\BaseModel;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Model\CategoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class Category
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="category")
 */
class Category extends BaseModel implements CategoryInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(name="category_id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="name",type="string",length=255)
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Tag",mappedBy="category")
     */
    protected $tags;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\TagList",mappedBy="category")
     */
    protected $tags_list;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
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
     * @return Category
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
     * Add tag
     *
     * @param Tag $tag
     *
     * @return Category
     */
    public function addTag(Tag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tag
     *
     * @param Tag $tag
     */
    public function removeTag(Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Add tagsList
     *
     * @param TagList $tagsList
     *
     * @return Category
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
