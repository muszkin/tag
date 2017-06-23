<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 24.05.17
 * Time: 12:15
 */

namespace AppBundle\Repository;


use AppBundle\Services\Filter\FilterProvider;

interface TagListRepositoryInterface extends RepositoryInterface
{
    public function findByFilters(FilterProvider $filter);
}