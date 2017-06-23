<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 24.05.17
 * Time: 11:43
 */

namespace AppBundle\Services\Remote;


use AppBundle\Model\AgentInterface;
use AppBundle\Model\TagInterface;
use AppBundle\Services\ProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use function json_encode;
use Monolog\Logger;
use stdClass;

class KayakoProvider extends AbstractProvider implements ProviderInterface
{
    const URL = "https://pomoc.shoper.pl/dashboard-api/index.php";

    private $method;

    private $params;

    /**
     * @param \DateTime $date
     * @throws \Exception
     * @return TagInterface[]
     * Get tags from specific day
     */
    public function getTagsFromDay(\DateTime $date)
    {
        $from = $date->setTime(0,0,0)->format("Y-m-d H:i:s");
        $to = new \DateTime('today');
        $to = $to->setTime(23,59,59)->format("Y-m-d H:i:s");

        $this->method = "getTagsFromDay";
        $this->params = [
            "from" => $from,
            "to" => $to,
        ];

        try {
            $tags = $this->doRequest();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
        $tagList = [];
        foreach ($tags as $tagRow){
            try {
                $tag = $this->findTag($tagRow->tag_name);
            }catch (\Exception $exception){
                $this->storeErrorIntoDb("{$exception->getMessage()}\n".json_encode($tagRow));
                $this->logger->err($exception->getMessage());
                continue;
            }

            try {
                $agent = $this->findAgent($tagRow->sid);
            }catch (\Exception $exception){
                $this->storeErrorIntoDb("{$exception->getMessage()}\n".json_encode($tagRow));
                $this->logger->err($exception->getMessage());
                continue;
            }
            $tagList[] = [
                "date" => $tagRow->date,
                "tag" => $tag,
                "ticket_id" => $tagRow->ticket_id,
                "agent" => $agent
            ];

        }
        return $tagList;
    }

    /**
     * @inheritdoc
     */
    public function findAgent($condition)
    {
        $agent = $this->em->getRepository('AppBundle:Agent')->findOneBy([
            "sid" => $condition
        ]);

        if (!$agent){
            throw new \Exception("Agent not found");
        }
        return $agent;
    }
    /**
     * @param AgentInterface $agent
     * @return TagInterface[]
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
     * @throws \Exception
     * Get tags from date range
     */
    public function getTagsFromRange(\DateTime $from, \DateTime $to)
    {
        $from = $from->setTime(0,0,0)->format("Y-m-d H:i:s");
        $to = $to->setTime(23,59,59)->format("Y-m-d H:i:s");

        $this->method = "getTagsFromDay";
        $this->params = [
            "from" => $from,
            "to" => $to,
        ];

        try {
            $tags = $this->doRequest();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
        $tagList = [];
        foreach ($tags as $tagRow){
            try {
                $tag = $this->findTag($tagRow->tag_name);
            }catch (\Exception $exception){
                $this->storeErrorIntoDb("{$exception->getMessage()}\n".json_encode($tagRow));
                $this->logger->err($exception->getMessage());
                continue;
            }

            try {
                $agent = $this->findAgent($tagRow->sid);
            }catch (\Exception $exception){
                $this->storeErrorIntoDb("{$exception->getMessage()}\n".json_encode($tagRow));
                $this->logger->err($exception->getMessage());
                continue;
            }
            $tagList[] = [
                "date" => $tagRow->date,
                "tag" => $tag,
                "ticket_id" => $tagRow->ticket_id,
                "agent" => $agent
            ];

        }
        return $tagList;
    }

    /**
     * @return stdClass
     * @throws \Exception
     */
    private function doRequest()
    {
        $client = new Client();

        try {
            $result = $client->request("post", self::URL, [
                "form_params" => [
                    "method" => $this->method,
                    "params" => json_encode($this->params)
                ]
            ]);
            return json_decode($result->getBody()->getContents());
        } catch (ClientException $exception){
            $request = $exception->getRequest()->getBody();
            $respone = $exception->getResponse()->getBody();
            $message = $exception->getMessage().PHP_EOL.(string)$request.PHP_EOL.(string)$respone;
            $this->logger->err($message);
            $this->storeErrorIntoDb($message);
            throw new \Exception($message);
        }

    }
}