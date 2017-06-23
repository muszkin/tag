<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 24.05.17
 * Time: 10:56
 */

namespace AppBundle\Entity;

use AppBundle\Model\AgentInterface;
use AppBundle\Model\BaseModel;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class Agent
 * @package AppBundle\Model
 * @ORM\Entity
 * @ORM\Table(name="staff")
 */
class Agent extends BaseModel implements AgentInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(name="agent_id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="firstname",type="string")
     */
    protected $firstname;

    /**
     * @ORM\Column(name="lastname",type="string")
     */
    protected $lastname;

    /**
     * @ORM\Column(name="sid",type="integer",options={"comment":"User id from Kayako"},nullable=true)
     */
    protected $sid;

    /**
     * @ORM\Column(name="sip",type="integer",options={"comment":"Number from thulium (local)"},nullable=true)
     */
    protected $sip;

    /**
     * @ORM\Column(name="thulium_login",type="string",length=255,options={"comment":"Thulium login with dot in middle"},nullable=true)
     */
    protected $thulium_login;

    /**
     * @ORM\Column(name="whmcs_admin_id",type="integer",length=255,options={"comment":"Id of user from whmcs"},nullable=true)
     */
    protected $whmcs_admin_id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Team",inversedBy="agents")
     * @ORM\JoinColumn(name="team_id",referencedColumnName="team_id")
     */
    protected $team;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\TagList",mappedBy="agent")
     */
    protected $tags_list;
    /**
     * Constructor
     */
    public function __construct()
    {
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
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Agent
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Agent
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set sid
     *
     * @param integer $sid
     *
     * @return Agent
     */
    public function setSid($sid)
    {
        $this->sid = $sid;

        return $this;
    }

    /**
     * Get sid
     *
     * @return integer
     */
    public function getSid()
    {
        return $this->sid;
    }

    /**
     * Set sip
     *
     * @param integer $sip
     *
     * @return Agent
     */
    public function setSip($sip)
    {
        $this->sip = $sip;

        return $this;
    }

    /**
     * Get sip
     *
     * @return integer
     */
    public function getSip()
    {
        return $this->sip;
    }

    /**
     * Set thuliumLogin
     *
     * @param string $thuliumLogin
     *
     * @return Agent
     */
    public function setThuliumLogin($thuliumLogin)
    {
        $this->thulium_login = $thuliumLogin;

        return $this;
    }

    /**
     * Get thuliumLogin
     *
     * @return string
     */
    public function getThuliumLogin()
    {
        return $this->thulium_login;
    }

    /**
     * Set whmcsAdminId
     *
     * @param integer $whmcsAdminId
     *
     * @return Agent
     */
    public function setWhmcsAdminId($whmcsAdminId)
    {
        $this->whmcs_admin_id = $whmcsAdminId;

        return $this;
    }

    /**
     * Get whmcsAdminId
     *
     * @return integer
     */
    public function getWhmcsAdminId()
    {
        return $this->whmcs_admin_id;
    }

    /**
     * Set team
     *
     * @param Team $team
     *
     * @return Agent
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

    /**
     * Add tagsList
     *
     * @param TagList $tagsList
     *
     * @return Agent
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

    public function __toString()
    {
        return "{$this->getFirstname()} {$this->getLastname()}";
    }

    public function getName()
    {
        return (string)$this;
    }

    public function getStringForTranslation()
    {
        return $this->getName();
    }


}
