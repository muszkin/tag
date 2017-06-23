<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 26.05.17
 * Time: 13:15
 */

namespace AppBundle\Entity;


use AppBundle\Model\ActivityLogInterface;
use AppBundle\Model\BaseModel;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ActivityLog
 * @package AppBundle\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ActivityLogRepository")
 * @ORM\Table(name="activity_log")
 */
class ActivityLog extends BaseModel implements ActivityLogInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",name="activity_log_id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="date",type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(name="description",type="text")
     */
    private $description;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}