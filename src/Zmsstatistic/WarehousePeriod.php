<?php
/**
 * @package Zmsadmin
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsstatistic;

use BO\Zmsstatistic\Download\Base;

class WarehousePeriod extends BaseController
{
    /**
     * @SuppressWarnings(Param)
     * @return String
     */
    public function readResponse(
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response,
        array $args
    ) {
        $workstation = \App::$http->readGetResult('/workstation/', ['resolveReferences' => 2])->getEntity();
        if (!$workstation->hasId()) {
            return \BO\Slim\Render::redirect(
                'index',
                array(
                    'error' => 'login_failed'
                )
            );
        }
        $periodList = \App::$http
          ->readGetResult('/warehouse/'. $args['subject'] .'/'. $args['subjectid'] .'/')
          ->getEntity();

        return \BO\Slim\Render::withHtml(
            $response,
            'page/warehousePeriod.twig',
            array(
                'title' => 'Kategorien',
                'menuActive' => 'warehouse',
                'periodList' => $periodList,
                'category' => $args['subject'],
                'categoryName' => Base::$subjectTranslations[$args['subject']],
                'subjectId' => $args['subjectid'],
                'workstation' => $workstation->getArrayCopy()
            )
        );
    }
}
