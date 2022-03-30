<?php
/**
 * @package ZMS API
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsapi;

use \BO\Slim\Render;
use \BO\Mellon\Validator;
use \BO\Zmsdb\Process;

/**
 * @SuppressWarnings(CouplingBetweenObjects)
 */
class ProcessConfirm extends BaseController
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
        \BO\Zmsdb\Connection\Select::setCriticalReadSession();
        
        $resolveReferences = Validator::param('resolveReferences')->isNumber()->setDefault(3)->getValue();
        $input = Validator::input()->isJson()->assertValid()->getValue();
        $userId = Validator::param('userId')->isString()->getValue();
        $entity = new \BO\Zmsentities\Process($input);
        $entity->testValid();
        $this->testProcessData($entity);

        $process = (new Process())->readEntity($entity->id, $entity->authKey);
        if ('reserved' != $process->status) {
            throw new Exception\Process\ProcessNotReservedAnymore();
        }
        $process = (new Process())->updateProcessStatus($process, 'confirmed', \App::$now, $resolveReferences, $userId);
        $message = Response\Message::create($request);
        $message->data = $process;

        $response = Render::withLastModified($response, time(), '0');
        $response = Render::withJson($response, $message->setUpdatedMetaData(), $message->getStatuscode());
        return $response;
    }

    protected function testProcessData($entity)
    {
        $authCheck = (new Process())->readAuthKeyByProcessId($entity->id);
        if (! $authCheck) {
            throw new Exception\Process\ProcessNotFound();
        } elseif ($authCheck['authKey'] != $entity->authKey && $authCheck['authName'] != $entity->authKey) {
            throw new Exception\Process\AuthKeyMatchFailed();
        }
    }
}
