<?php
/**
 * @package 115Mandant
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsadmin;

use BO\Zmsentities\Organisation as Entity;
use BO\Mellon\Validator;

/**
  * Handle requests concerning services
  *
  */
class OrganisationAdd extends BaseController
{
    /**
     * @return String
     */
    public function __invoke(
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response,
        array $args
    ) {

        $input = $request->getParsedBody();
        if (is_array($input) && array_key_exists('save', $input)) {
            try {
                $entity = new Entity($input);
                $entity = \App::$http->readPostResult('/organisation/add/', $entity)
                    ->getEntity();
                return Helper\Render::redirect(
                    'organisation',
                    array(
                        'id' => $entity->id
                    ),
                    array(
                        'success' => 'organisation_created'
                    )
                );
            } catch (\Exception $exception) {
                return Helper\Render::error($exception);
            }
        }

        return Helper\Render::checkedHtml(self::$errorHandler, $response, 'page/organisation.twig', array(
            'title' => 'Kunde',
            'action' => 'add',
            'menuActive' => 'organisation'
        ));
    }
}
