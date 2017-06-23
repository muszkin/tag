<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 24.05.17
 * Time: 11:10
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

abstract class RepositoryAbstract extends EntityRepository implements RepositoryInterface
{

    /**
     * @inheritdoc
     */
    public function findById($id)
    {
        return $this->findOneBy([
            'id'=>$id
        ]);
    }

}