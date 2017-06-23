<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 24.05.17
 * Time: 10:55
 */

namespace AppBundle\Repository;


interface RepositoryInterface
{
    /**
     * @param $id
     * @return \stdClass
     */
    public function findById($id);

}