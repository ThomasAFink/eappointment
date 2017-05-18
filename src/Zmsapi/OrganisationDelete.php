<?php
/**
 * @package ZMS API
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 */

namespace BO\Zmsapi;

use \BO\Slim\Render;
use \BO\Zmsdb\Organisation as Query;

/**
 * Delete an organisation by Id
 */
class OrganisationDelete extends BaseController
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
        (new Helper\User($request))->checkRights('superuser');
        $query = new Query();
        $organisation = $query->readEntity($args['id']);
        if (! $organisation) {
            throw new Exception\Organisation\OrganisationNotFound();
        }
        $query->deleteEntity($organisation->id);

        $message = Response\Message::create($request);
        $message->data = $organisation;

        $response = Render::withLastModified($response, time(), '0');
        $response = Render::withJson($response, $message->setUpdatedMetaData(), $message->getStatuscode());
        return $response;
    }
}
