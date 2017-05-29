<?php
/**
 * @package Zmsadmin
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsadmin;

use BO\Zmsentities\Collection\DayoffList as Collection;
use BO\Mellon\Validator;

/**
 * Handle requests concerning services
 *
 */
class DayoffByYear extends BaseController
{

    /**
     *
     * @return String
     */
    public function readResponse(
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response,
        array $args
    ) {
        $workstation = \App::$http->readGetResult('/workstation/', ['resolveReferences' => 1])->getEntity();
        $confirm_success = $request->getAttribute('validator')->getParameter('confirm_success')->isString()->getValue();
        $year = Validator::value($args['year'])->isNumber()->getValue();
        $collection = \App::$http->readGetResult('/dayoff/'. $year .'/')->getCollection();
        $dayOffList = ($collection) ? array_values($collection->sortByCustomKey('date')->getArrayCopy()) : null;

        $updated = false;
        $input = $request->getParsedBody();
        if (array_key_exists('save', (array) $input)) {
            $data = (array_key_exists('dayoff', $input)) ? $input['dayoff'] : [];
            $collection = new Collection($data);
            $collection = \App::$http->readPostResult(
                '/dayoff/'. $year .'/',
                $collection->withTimestampFromDateformat()
            )->getCollection();
            $updated = true;
            return \BO\Slim\Render::redirect('dayoffByYear', ['year' => $year], [
                'confirm_success' => \App::$now->getTimeStamp()
            ]);
        }

        $response = \BO\Slim\Render::withLastModified($response, time(), '0');
        return \BO\Slim\Render::withHtml(
            $response,
            'page/dayoffByYear.twig',
            array(
                'title' => 'Allgemein gültige Feiertage',
                'year' => $year,
                'updated' => $updated,
                'menuActive' => 'dayoff',
                'workstation' => $workstation,
                'confirm_success' => $confirm_success,
                'dayoffList' => $dayOffList
            )
        );
    }
}
