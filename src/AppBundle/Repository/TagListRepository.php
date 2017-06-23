<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 24.05.17
 * Time: 12:16
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Agent;
use AppBundle\Entity\Category;
use AppBundle\Entity\Source;
use AppBundle\Entity\Tag;
use AppBundle\Entity\TagList;
use AppBundle\Entity\Team;
use AppBundle\Services\Filter\FilterProvider;
use function method_exists;
use function property_exists;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;

class TagListRepository extends RepositoryAbstract implements TagListRepositoryInterface
{

    public function findByFilters(FilterProvider $filter)
    {

        $qb = $this->getEntityManager()->createQueryBuilder();

        $query = $qb
            ->select('tagList')
            ->from('AppBundle:TagList','tagList')
            ->where("tagList.date >= :from")
            ->andWhere(" tagList.date <= :to")
            ->setParameters([
                "from" => $filter->getFrom(),
                "to" => $filter->getTo()
            ])
        ;

        $teams = [];
        foreach ($filter->getTeam() as $team){
            $teams[] = $team->getId();
        }
        if (!empty($teams)){
            $query
                ->andWhere($qb->expr()->in('tagList.team',$teams))
            ;
        }
        $agents = [];
        foreach($filter->getAgent() as $agent){
            $agents[] = $agent->getId();
        }
        if (!empty($agents)){
            $query
                ->andWhere($qb->expr()->in('tagList.agent',$agents))
            ;
        }
        $sources = [];
        foreach($filter->getSource() as $source){
            $sources[] = $source->getId();
        }
        if (!empty($sources)){
            $query
                ->andWhere($qb->expr()->in('tagList.source',$sources))
            ;
        }
        $categories = [];
        foreach($filter->getCategory() as $category){
            $categories[] = $category->getId();
        }
        if (!empty($categories)){
            $query
                ->andWhere($qb->expr()->in('tagList.category',$categories))
            ;
        }
        $tags = [];
        foreach($filter->getTag() as $tag){
            $tags[] = $tag->getId();
        }
        if (!empty($tags)){
            $query
                ->andWhere($qb->expr()->in('tagList.tag',$tags))
            ;
        }

        $query->orderBy('tagList.date','ASC');
        $result = $query->getQuery()->getResult();

        return $result;
    }



}