<?php
/**
 * @package ZMS API
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsapi;

use BO\Slim\Render;
use BO\Mellon\Validator;

use BO\Zmsentities\Availability as Entity;
use BO\Zmsentities\Collection\AvailabilityList as Collection;

use BO\Zmsdb\Availability as AvailabilityRepository;
use BO\Zmsdb\Slot as SlotRepository;
use BO\Zmsdb\Helper\CalculateSlots as CalculateSlotsHelper;
use BO\Zmsdb\Connection\Select as DbConnection;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use BO\Zmsapi\Exception\BadRequest as BadRequestException;
use BO\Zmsapi\Exception\Availability\AvailabilityUpdateFailed as UpdateFailedException;

/**
 * @SuppressWarnings(Coupling)
 */
class AvailabilityAdd extends BaseController
{
    /**
     * @SuppressWarnings(Param)
     * @return String
     */
    public function readResponse(
        RequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        (new Helper\User($request))->checkRights();
        $input = Validator::input()->isJson()->assertValid()->getValue();
        if (! $input || count($input) === 0) {
            throw new BadRequestException();
        }
        $collection = new Collection();
        DbConnection::getWriteConnection();
        foreach ($input as $availability) {
            $entity = new Entity($availability);
            $updatedEntity = $this->writeEntityUpdate($entity);
            $this->writeCalculatedSlots($updatedEntity);
            $collection[] = $updatedEntity;
        }

        $message = Response\Message::create($request);
        $message->data = $collection->getArrayCopy();

        $response = Render::withLastModified($response, time(), '0');
        $response = Render::withJson($response, $message->setUpdatedMetaData(), $message->getStatuscode());
        return $response;
    }

    protected function writeCalculatedSlots($updatedEntity)
    {
        (new SlotRepository)->writeByAvailability($updatedEntity, \App::$now);
        (new CalculateSlotsHelper(\App::DEBUG))
            ->writePostProcessingByScope($updatedEntity->scope, \App::$now);
        DbConnection::writeCommit();
    }

    protected function writeEntityUpdate($entity): Entity
    {
        $repository = new AvailabilityRepository();
        $entity->testValid();
        $oldentity = $repository->readEntity($entity->id);
        if ($oldentity && $oldentity->hasId()) {
            $this->writeSpontaneousEntity($oldentity);
            $updatedEntity = $repository->updateEntity($entity->id, $entity, 2);
        } else {
            $updatedEntity = $repository->writeEntity($entity, 2);
        }
        if (! $updatedEntity) {
            throw new UpdateFailedException();
        }
        return $updatedEntity;
    }

    protected function writeSpontaneousEntity(Entity $entity): void
    {
        $doubleTypesEntity = (new AvailabilityRepository())->readEntityDoubleTypes($entity->id);
        if ($doubleTypesEntity) {
            $doubleTypesEntity->workstationCount['intern'] = 0;
            $doubleTypesEntity->workstationCount['callcenter'] = 0;
            $doubleTypesEntity->workstationCount['public'] = 0;
            $doubleTypesEntity['description'] = '';
            $doubleTypesEntity['type'] = 'openinghours';
            (new AvailabilityRepository())->writeEntity($doubleTypesEntity);
        }
    }
}
