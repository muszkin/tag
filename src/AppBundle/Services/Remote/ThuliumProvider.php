<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 24.05.17
 * Time: 11:43
 */

namespace AppBundle\Services\Remote;


use AppBundle\Entity\Agent;
use AppBundle\Model\AgentInterface;
use AppBundle\Model\TagInterface;
use AppBundle\Services\ProviderInterface;
use function array_merge;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Monolog\Logger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class ThuliumProvider extends AbstractProvider implements ProviderInterface
{
    const DISPOSITION = "ANSWERED";
    const INBOUND = "INBOUND";

    private $url;
    private $login;
    private $password;
    private $tags;

    /** @var array */
    private $connections;

    /** @var OutputInterface */
    private $output;

    /** @var Stopwatch */
    private $stopwatch;

    public function __construct(EntityManagerInterface $entityManager, Logger $logger,$url,$login,$password)
    {
        parent::__construct($entityManager, $logger);
        $this->url = $url;
        $this->login = $login;
        $this->password = $password;
        $this->getTagsList();
        $this->stopwatch = new Stopwatch();
    }

    public function getListOfConnections()
    {
        return $this->connections;
    }

    /**
     * @param \DateTime $date
     * @return TagInterface[]
     * Get tags from specific day
     */
    public function getTagsFromDay(\DateTime $date)
    {
        $tagList = [];
        $from = clone $date;
        $from->setTime(0,0,0);
        $to = clone $date;
        $to->setTime(23,59,59);
        $this->getConnections($from,$to);
        foreach ($this->connections as $connection){
            if (self::DISPOSITION != $connection['disposition']){
                continue;
            }

            $comment = $this->getConnectionComment($connection['connection_id']);
            if (!empty($comment)){

                try {
                    $agent = $this->findAgent($comment['agent_login']);
                }catch (\Exception $exception){
                    $currentData = json_encode($connection);
                    $commentData = json_encode($comment);
                    $this->storeErrorIntoDb("{$exception->getMessage()}\n$currentData\n$commentData");

                    $this->logger->err("{$exception->getMessage()}\n{$currentData}\n".json_encode($tagList));
                    continue;
                }

                foreach ($this->tags as $tag){
                    if (strpos($comment['comment'],$tag) !== false){
                        try {
                            $tag = $this->findTag($tag);
                        }catch (\Exception $exception){
                            $this->storeErrorIntoDb("{$exception->getMessage()}\n".json_encode($connection));
                            $this->logger->err($exception->getMessage());
                            continue;
                        }

                        $tagList[] = [
                            "date" => $connection['date'],
                            "tag" => $tag,
                            "connection_id" => $connection['connection_id'],
                            "agent" => $agent
                        ];
                    }
                }
            }
        }

        return $tagList;
    }

    /**
     * @param AgentInterface $agent
     * @return TagInterface
     * Get tags for specific agent
     */
    public function getTagsForAgent(AgentInterface $agent)
    {
        // TODO: Implement getTagsForAgent() method.
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @return TagInterface[]
     * Get tags from date range
     */
    public function getTagsFromRange(\DateTime $from, \DateTime $to)
    {
        $tagList = [];
        $from->setTime(0,0,0);
        $to->setTime(23,59,59);
        $this->getConnections($from,$to);
        foreach ($this->connections as $connection){

            if (self::DISPOSITION != $connection['disposition']){
                continue;
            }
            $this->stopwatch->start(md5($connection['date']));
            $comment = $this->getConnectionComment($connection['connection_id']);
            if (!empty($comment)){

                try {
                    /** @var Agent $agent */
                    $agent = $this->findAgent($comment['agent_login']);
                }catch (\Exception $exception){
                    $currentData = json_encode($connection);
                    $commentData = json_encode($comment);
                    $this->storeErrorIntoDb("{$exception->getMessage()}\n$currentData\n$commentData");

                    $this->logger->err("{$exception->getMessage()}\n{$currentData}\n".json_encode($tagList));
                    continue;
                }
                $connectionTime = $this->stopwatch->stop(md5($connection['date']));
                foreach ($this->tags as $tag){
                    $this->stopwatch->start(md5($comment['comment'].$tag));
                    $timer = null;
                    if (strpos($comment['comment'],$tag) !== false){
                        try {
                            $tag = $this->findTag($tag);
                        }catch (\Exception $exception){
                            $this->storeErrorIntoDb("{$exception->getMessage()}\n".json_encode($connection));
                            $this->logger->err($exception->getMessage());
                            continue;
                        }

                        $tagList[] = [
                            "date" => $connection['date'],
                            "tag" => $tag,
                            "connection_id" => $connection['connection_id'],
                            "agent" => $agent
                        ];
                        $timer = $this->stopwatch->stop(md5($comment['comment'].$tag));
                        $this->output->writeln("Added {$tag->getSlug()} from {$connection['date']} by agent {$agent->getName()} - thulium {$connectionTime->getDuration()} - parsing {$timer->getDuration()}");
                    }
                    if (!$timer){
                        $this->stopwatch->stop(md5($comment['comment'].$tag));
                    }
                }
            }
        }

        return $tagList;
    }

    public function filterConnections()
    {
        foreach ($this->connections as $key => $connection){
            if ($connection['disposition'] != self::DISPOSITION){
                unset($this->connections[$key]);
                continue;
            }
            if ($connection['type'] != self::INBOUND){
                unset($this->connections[$key]);
                continue;
            }
        }
    }

    /**
     * @param $condition
     * @return AgentInterface
     * @throws \Exception
     */
    public function findAgent($condition)
    {
        $agent = $this->em->getRepository('AppBundle:Agent')->findOneBy([
            "thulium_login" => $condition,
        ]);

        if (!$agent) {
            throw new \Exception("Agent $condition not found");
        }

        return $agent;
    }

    public function getConnections(\DateTime $from,\DateTime $to,$connection_id = null)
    {
        $query_params = [
            "limit" => 100,
            "offset" => 0,
            "date_from" => $from->format('Y-m-d H:i:s'),
            "date_to" => $to->format('Y-m-d H:i:s'),
        ];
        if ($connection_id){
            $query_params['connection_id'] = $connection_id;
        }
        $this->stopwatch->start('connection');
        $result = $this->doRequest("GET","/connections",$query_params);

        if (isset($result['result'])) {
            $this->connections = $result['result'];
            $timer = $this->stopwatch->stop("connection");
            $this->output->writeln("Total {$result['count']}, already get first 100 - {$timer->getDuration()}ms");
            if ($result['count'] > $query_params['limit']) {
                $pages = ceil($result['count'] / $query_params['limit']);
                for ($x = 1; $x <= $pages; $x++) {
                    $query_params['offset'] = $query_params['limit'] * $x;
                    $this->stopwatch->start($x);
                    $next_results = $this->doRequest("GET", "/connections", $query_params);
                    $timer = $this->stopwatch->stop($x);
                    $limit = 100 + $query_params['offset'];
                    $this->output->writeln("Total {$result['count']}, already get first {$limit} - {$timer->getDuration()}ms");
                    $this->connections = array_merge($this->connections, $next_results['result']);
                }
            }
        }
        $this->stopwatch->start('filtering');
        $this->filterConnections();
        $total = count($this->connections);
        $timer = $this->stopwatch->stop('filtering');
        $this->output->writeln("All connections are taken,total to check {$total} - {$timer->getDuration()}ms");
    }

    public function getConnectionComment($connection_id)
    {
        $url = "/connections/{$connection_id}/topic";

        $result = $this->doRequest("GET",$url);

        $return = [];

        if (isset($result['comment']) && !empty($result['comment'])){
            $return = [
                "comment" => $result['comment'],
                "agent_login" => $result['agent_login']
            ];
        }

        return $return;
    }

    public function getTagsList()
    {
        $tags = $this->em->getRepository('AppBundle:Tag')->findAll();

        foreach ($tags as $tag){
            $this->tags[] = $tag->getSlug();
        }
    }

    public function doRequest($method,$url,$query = null,$body = null)
    {
        $client = new Client();

        try {
            if (!$query && !$body) {
                $response = $client->request($method,$this->url . $url, [
                    "auth" => [$this->login, $this->password]
                ]);
            } else if($query && !$body) {
                $response = $client->request($method,$this->url . $url, [
                    "query" => $query,
                    "auth" => [$this->login, $this->password]
                ]);
            } else if (!$query && $body) {
                $response = $client->request($method,$this->url . $url, [
                    "form_params" => $body,
                    "auth" => [$this->login, $this->password]
                ]);
            } else {
                $response = $client->request($method,$this->url . $url, [
                    "query" => $query,
                    "form_params" => $body,
                    "auth" => [$this->login, $this->password]
                ]);
            }

        }catch (ClientException $exception){
            return json_decode($exception->getResponse()->getBody()->getContents(),true);
        }
        return json_decode($response->getBody()->getContents(),true);
    }

    /**
     * @param mixed $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

}