<?php
/**
 * @package ZMS API
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsapi;

use \BO\Slim\Render;
use \BO\Mellon\Validator;
use \BO\Zmsdb\Mail as Query;
use \BO\Zmsdb\Config;
use \BO\Zmsdb\Process;

/**
 * @SuppressWarnings(Coupling)
 */
class ProcessDeleteMail extends BaseController
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
        $input = Validator::input()->isJson()->assertValid()->getValue();
        $process = new \BO\Zmsentities\Process($input);
        
        $process->testValid();
        $this->testProcessData($process);

        \BO\Zmsdb\Connection\Select::getWriteConnection();
        $config = (new Config())->readEntity();

        $collection = ProcessConfirmationMail::getProcessListOverview($process);
        $mail = (new \BO\Zmsentities\Mail())->toResolvedEntity($collection, $config, 'deleted');
        $mail = (new \BO\Zmsdb\Mail)->writeInQueue($mail, \App::$now, false);

        \App::$log->debug("Send mail", [$mail]);

        $message = Response\Message::create($request);
        $message->data = $mail;

        $response = Render::withLastModified($response, time(), '0');
        $response = Render::withJson($response, $message, 200);
        return $response;
    }

    protected function testProcessData($process)
    {
        $authCheck = (new Process())->readAuthKeyByProcessId($process->getId());
        if (! $authCheck) {
            throw new Exception\Process\ProcessNotFound();
        } elseif ($process->toProperty()->scope->preferences->client->emailRequired->get() &&
            ! $process->getFirstClient()->hasEmail()
        ) {
            throw new Exception\Process\EmailRequired();
        }
    }
}
