<?php
/**
 * @package ZMS API
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsapi;

use \BO\Slim\Render;
use \BO\Mellon\Validator;
use \BO\Zmsdb\Useraccount;
use \BO\Zmsentities\Collection\UseraccountList as Collection;

class UseraccountList extends BaseController
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
        $validator = $request->getAttribute('validator');
        (new Helper\User($request, 2))->checkRights('useraccount');
        $resolveReferences = $validator->getParameter('resolveReferences')->isNumber()->setDefault(1)->getValue();
        $useraccountList = (new Useraccount)->readList($resolveReferences);
        $useraccountList = $this->getAccessedUseraccountList($useraccountList);
        $message = Response\Message::create($request);
        $message->data = $useraccountList;

        $response = Render::withLastModified($response, time(), '0');
        $response = Render::withJson($response, $message->setUpdatedMetaData(), 200);
        return $response;
    }

    protected function getAccessedUseraccountList($useraccountList)
    {
        $collection = new Collection();
        foreach ($useraccountList as $useraccount) {
            $assignedDepartments = (new Useraccount())->readAssignedDepartmentList($useraccount);
            if ($useraccount->isSuperUser() || 0 === $assignedDepartments->count()) {
                $collection->addEntity(clone $useraccount);
            }
        }
        return $collection;
    }
}
