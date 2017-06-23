<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 26.05.17
 * Time: 13:14
 */

namespace AppBundle\Repository;


use Doctrine\Common\Collections\Criteria;

class ActivityLogRepository extends RepositoryAbstract implements ActivityLogRepositoryInterface
{

    public function getActivityLog(\DateTime $from,\DateTime $to)
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create();

        $criteria
            ->andWhere(
                $expr->gte('date',$from)
            )
            ->andWhere(
                $expr->lte('date',$to)
            )
        ;

        $query = $this->createQueryBuilder('s')->addCriteria($criteria);
        return $query->getQuery();
    }

}