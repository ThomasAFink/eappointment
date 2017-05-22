<?php
/**
 * @package ZMS API
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsapi;

use \BO\Slim\Render;
use \BO\Mellon\Validator;
use \BO\Zmsdb\ProcessStatusArchived as Query;
use \BO\Zmsdb\Process;

/**
 * @SuppressWarnings(Coupling)
 */
class ProcessFinished extends BaseController
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
        $workstation = (new Helper\User($request))->checkRights();
        $input = Validator::input()->isJson()->assertValid()->getValue();
        $process = new \BO\Zmsentities\Process($input);
        $process->testValid();
        $this->testProcessData($process);
        $hasValidCredentials = (
            $process->hasProcessCredentials() &&
            ('pending' == $process['status'] || 'finished' == $process['status'])
        );

        if (! $hasValidCredentials) {
            throw new Exception\Process\ProcessInvalid();
        }
        $cluster = (new \BO\Zmsdb\Cluster)->readByScopeId($workstation->scope['id'], 1);
        $workstation->process = $process;
        $workstation->testMatchingProcessScope($cluster);

        $query = new Query();
        if ('pending' == $process['status']) {
            $process = $query->updateEntity($process);
        } else {
            $query->writeEntityFinished($process, \App::$now);
            foreach ($process->getClients() as $client) {
                if ($client->hasSurveyAccepted()) {
                    $config = (new \BO\Zmsdb\Config())->readEntity();
                    $mail = (new \BO\Zmsentities\Mail())->toResolvedEntity($process, $config);
                    (new \BO\Zmsdb\Mail())->writeInQueue($mail);
                }
            }
        }

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
