<?php
namespace BO\Zmsdb;

use \BO\Zmsentities\Availability as Entity;
use \BO\Zmsentities\Collection\AvailabilityList as Collection;

class Availability extends Base
{
    public static $cache = [];

    public function readEntity($availabilityId, $resolveReferences = 0, $disableCache = false)
    {
        if (!$disableCache && !array_key_exists($availabilityId, self::$cache)) {
            $query = new Query\Availability(Query\Base::SELECT);
            $query
                ->addEntityMapping()
                ->addResolvedReferences($resolveReferences)
                ->addConditionAvailabilityId($availabilityId);
            $availability = $this->fetchOne($query, new Entity());
            $availability['scope'] = (new Scope())->readEntity($availability['scope']['id'], $resolveReferences);
            if (!isset($availability['department'])) {
                $availability['department'] = (new Department())
                    ->readEntity($availability['scope']['department']['id'], $resolveReferences);
            }
            self::$cache[$availabilityId] = $availability;
        }
        return self::$cache[$availabilityId];
    }

    public function readList($scopeId, $resolveReferences = 0)
    {
        $collection = new Collection();
        $query = new Query\Availability(Query\Base::SELECT);
        $query
            ->addEntityMapping()
            ->addResolvedReferences($resolveReferences)
            ->addConditionScopeId($scopeId);
        $result = $this->fetchList($query, new Entity());
        if (count($result)) {
            foreach ($result as $entity) {
                if ($entity instanceof Entity) {
                    $collection->addEntity($entity);
                }
            }
        }
        return $collection;
    }

    /**
     * write an availability
     *
     * @param
     * entityId
     *
     * @return lastInsertId()
     */
    public function writeEntity(\BO\Zmsentities\Availability $entity)
    {
        $query = new Query\Availability(Query\Base::INSERT);
        $values = $query->reverseEntityMapping($entity);
        $query->addValues($values);
        $this->writeItem($query);
        return $this->getWriter()->lastInsertId();
    }

    /**
     * update an availability
     *
     * @param
     * entityId
     *
     * @return Entity
     */
    public function updateEntity($entityId, \BO\Zmsentities\Availability $entity)
    {
        $query = new Query\Availability(Query\Base::UPDATE);
        $query->addConditionAvailabilityId($entityId);
        $values = $query->reverseEntityMapping($entity);
        $query->addValues($values);
        $this->writeItem($query);
        return $this->readEntity($entityId);
    }

    /**
     * remove an availability
     *
     * @param
     * availabilityId
     *
     * @return Resource Status
     */
    public function deleteEntity($availabilityId)
    {
        $query =  new Query\Availability(Query\Base::DELETE);
        $query->addConditionAvailabilityId($availabilityId);
        return $this->deleteItem($query);
    }
}
