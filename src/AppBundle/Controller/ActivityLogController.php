<?php
/**
 * Created by PhpStorm.
 * User: muszkin
 * Date: 26.05.17
 * Time: 13:49
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ActivityLogController extends Controller
{

    /**
     * @param Request $request
     * @return array
     * @Template()
     */
    public function listAction(Request $request)
    {
        $activityLogProvider = $this->get('tag.activity.log');

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $activityLogProvider->getLogsForToday(),
            $request->query->getInt('page',1),
            50
        );

        return [
            "logs" => $pagination
        ];
    }
}