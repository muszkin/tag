<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 24.05.17
 * Time: 15:02
 */

namespace AppBundle\Controller;


use AppBundle\Form\MenuType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use function var_dump;

class IndexController extends Controller
{
    /**
     * @param Request $request
     * @Template("@App/Main/index.html.twig")
     * @return mixed
     */
    public function indexAction(Request $request)
    {
        $menu = $this->createForm(MenuType::class,null,["method"=>"GET"]);

        $menu->handleRequest($request);
        $tags = [];
        $charts = null;
        if ($menu->isSubmitted()){
            $formData = $menu->getData();
            $query = $request->query->all();
            try {
                $from = new \DateTime($query['menu']['from']);
            }catch (\Exception $exception){
                $from = new \DateTime('first day of this month');
            }
            try {
                $to = new \DateTime($query['menu']['to']);
            }catch (\Exception $exception){
                $to = new \DateTime('last day of this month');
            }

            $team = (isset($formData['team']))?$formData['team']:null;
            $agent = (isset($formData['agent']))?$formData['agent']:null;
            $source = (isset($formData['source']))?$formData['source']:null;
            $category = (isset($formData['category']))?$formData['category']:null;
            $tag = (isset($formData['tag']))?$formData['tag']:null;
            $group = $formData['group'];

            $tagProvider = $this->get('tag.filter.provider');

            $tagProvider->init($from,$to,$team,$agent,$source,$category,$tag,$group);
            $tags = $tagProvider->getResultsFromFilters();
            $charts = $tagProvider->getChartsData($tags);

        }

        return ["menu" => $menu->createView(),"tags" => $tags, "charts" => $charts];
    }

    /**
     * @param Request $request
     * @return mixed
     * @Template("@App/Main/tags.twig")
     */
    public function getTagsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $category_id = $request->query->get('category');

        $category = $em->getRepository('AppBundle:Category')->find($category_id);

        return ["tags" => $em->getRepository('AppBundle:Tag')->findBy(["category" => $category])];
    }

    /**
     * @param Request $request
     * @return mixed
     * @Template("@App/Main/agents.twig")
     */
    public function getAgentsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $team_id = $request->query->get('team');

        $team = $em->getRepository('AppBundle:Team')->find($team_id);

        return ["agents" => $em->getRepository('AppBundle:Agent')->findBy(["team" => $team])];
    }

}