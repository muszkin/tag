<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 25.05.17
 * Time: 12:48
 */

namespace AppBundle\Services\Filter;

use AppBundle\Entity\Agent;
use AppBundle\Entity\Category;
use AppBundle\Entity\Source;
use AppBundle\Entity\Tag;
use AppBundle\Entity\TagList;
use AppBundle\Entity\Team;
use AppBundle\Model\AgentInterface;
use AppBundle\Model\CategoryInterface;
use AppBundle\Model\SourceInterface;
use AppBundle\Model\TagInterface;
use AppBundle\Model\TeamInterface;
use AppBundle\Repository\TagListRepository;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;

class FilterProvider
{
    const TEAM = "team";
    const AGENT = "agent";
    const SOURCE = "source";
    const CATEGORY = "category";
    const TAG = "tag";
    const KEYS = [
        self::TEAM,
        self::AGENT,
        self::SOURCE,
        self::CATEGORY,
        self::TAG
    ];

    /** @var \DateTime */
    private $from;

    /** @var \DateTime */
    private $to;

    /** @var  ArrayCollection|null */
    private $team;

    /** @var  ArrayCollection|null */
    private $agent;

    /** @var  ArrayCollection|null */
    private $source;

    /** @var  ArrayCollection|null */
    private $category;

    /** @var  ArrayCollection|null */
    private $tag;

    /** @var string */
    private $group;

    /** @var EntityManagerInterface */
    private $em;

    /** @var Cache $cache */
    private $cache;

    /** @var TranslatorInterface $translator */
    private $translator;

    public function __construct(EntityManagerInterface $entityManager,Cache $cache,TranslatorInterface $translator)
    {
        $this->em = $entityManager;
        $this->cache = $cache;
        $this->translator = $translator;
    }

    /**
     * @return \DateTime
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param \DateTime $from
     * @return FilterProvider
     */
    public function setFrom($from)
    {
        $this->from = $from;

        $this->from->setTime(0,0,0);

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param \DateTime $to
     * @return FilterProvider
     */
    public function setTo($to)
    {
        $this->to = $to;

        $this->to->setTime(23,59,59);
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param ArrayCollection $team
     * @return FilterProvider
     */
    public function setTeam($team)
    {
        $this->team = $team;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @param ArrayCollection $agent
     * @return FilterProvider
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;
        return $this;
    }

    /**
     * @return ArrayCollection
     * @return FilterProvider
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param ArrayCollection $source
     * @return FilterProvider
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param ArrayCollection $category
     * @return FilterProvider
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param ArrayCollection $tag
     * @return FilterProvider
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param string $group
     * @return FilterProvider
     */
    public function setGroup($group)
    {
        $this->group = $group;
        return $this;
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param ArrayCollection $team
     * @param ArrayCollection $agent
     * @param ArrayCollection $source
     * @param ArrayCollection $category
     * @param ArrayCollection $tag
     * @param $group
     * @return FilterProvider
     */
    public function init(
        \DateTime $from,
        \DateTime $to,
        $team = null,
        $agent = null,
        $source = null,
        $category = null,
        $tag = null,
        $group
    )
    {
        $this->setFrom($from);
        $this->setTo($to);
        $this->setTeam($team);
        $this->setAgent($agent);
        $this->setSource($source);
        $this->setCategory($category);
        $this->setTag($tag);
        $this->setGroup($group);

        return $this;
    }

    public function getResultsFromFilters()
    {
        $tagsList = $this->em->getRepository("AppBundle:TagList")->findByFilters($this);

        return $tagsList;
    }

    public function getChartsData($tagsList = array())
    {
        $result =[
            "team" => $this->getChartsDataTrans($tagsList,$this,self::TEAM),
            "agent" => $this->getChartsDataTrans($tagsList,$this,self::AGENT),
            "source" => $this->getChartsDataTrans($tagsList,$this,self::SOURCE),
            "category" => $this->getChartsDataTrans($tagsList,$this,self::CATEGORY),
            "tag" => $this->getChartsDataTrans($tagsList,$this,self::TAG),
        ];


        return $result;
    }

    /**
     * @param array $tagsList
     * @param FilterProvider $filter
     * @param $key
     * @return array
     */
    public function getChartsDataTrans($tagsList = array(),FilterProvider $filter,$key)
    {
        if (!in_array($key,self::KEYS)){
            return [
                "pie" => null,
                "line" => null,
            ];
        }
        $getFunction = "get".ucfirst($key);
        $pieChart = [];
        /** @var TagList $tagList */
        foreach ($tagsList as $tagList){
            /** @var Team|Agent|Source|Category|Tag $object */
            $object = call_user_func([$tagList,$getFunction]);
            $pieChart[$object->getId()] = [
                "category" => $this->translator->trans($object->getStringForTranslation(),[],'tag'),
                "value" => ((isset($pieChart[$object->getId()]['value']))?$pieChart[$object->getId()]['value']:0) + 1,
            ];
        }

        switch ($filter->getGroup()){
            case 'days':
                $format = "Y-m-d";
                $period = "DD";
                break;
            case 'weeks';
                $format = "Y W";
                $period = "WW";
                break;
            case 'months':
                $format = "Y-m";
                $period = "MM";
                break;
            case 'years':
                $format = "Y";
                $period = "YYYY";
                break;
            default:
                $format = "Y-m-d";
                $period = "DD";
        }

        $lineChart = [];
        $graphs = [];
        /** @var TagList $tagList */
        foreach ($tagsList as $tagList){
            /** @var Team|Agent|Source|Category|Tag $object */
            $object = call_user_func([$tagList,$getFunction]);
            $date = $tagList->getDate();
            $lineChart[$date->format($format)][$this->translator->trans($object->getStringForTranslation(),[],'tag')] += 1;
            if (!in_array($this->translator->trans($object->getStringForTranslation(),[],'tag'),$graphs)){
                array_push($graphs,$this->translator->trans($object->getStringForTranslation(),[],'tag'));
            }
        }

        $lineChart['period'] = $period;

        return [
            "pie" => $pieChart,
            "line" => $lineChart,
            "graphs" => $graphs,
            "name" => $key
        ];
    }

}