<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 25.05.17
 * Time: 14:36
 */

namespace AppBundle\Model;


abstract class BaseModel
{
    public function __toString()
    {
        return $this->name;
    }
}