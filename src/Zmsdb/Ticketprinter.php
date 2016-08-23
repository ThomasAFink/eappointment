<?php

namespace BO\Zmsdb;

use \BO\Zmsentities\Ticketprinter as Entity;
use \BO\Zmsentities\Collection\TicketprinterList as Collection;

class Ticketprinter extends Base
{
    /**
    * read entity
    *
    * @param
    * itemId
    * resolveReferences
    *
    * @return Resource Entity
    */
    public function readEntity($itemId, $resolveReferences = 0)
    {
        $query = new Query\Ticketprinter(Query\Base::SELECT);
        $query
            ->addEntityMapping()
            ->addResolvedReferences($resolveReferences)
            ->addConditionTicketprinterId($itemId);
        $ticketprinter = $this->fetchOne($query, new Entity());
        return $ticketprinter;
    }

     /**
     * read list of owners
     *
     * @param
     * resolveReferences
     *
     * @return Resource Collection
     */
    public function readList($resolveReferences = 0)
    {
        $ticketprinterList = new Collection();
        $query = new Query\Ticketprinter(Query\Base::SELECT);
        $query
            ->addEntityMapping()
            ->addResolvedReferences($resolveReferences - 1);
        $result = $this->fetchList($query, new Entity());
        if (count($result)) {
            foreach ($result as $ticketprinter) {
                if ($ticketprinter instanceof Entity) {
                    $ticketprinterList->addEntity($ticketprinter);
                }
            }
        }
        return $ticketprinterList;
    }

    /**
     * write a ticketprinter
     *
     * @param
     * entity
     *
     * @return Entity
     */
    public function writeEntity(Entity $entity, $parentId)
    {
        $query = new Query\Ticketprinter(Query\Base::INSERT);
        $values = $query->reverseEntityMapping($entity, $parentId);

        //get owner by organisation
        $owner = (new Owner())->readByOrganisationId($parentId);
        $values['kundenid'] = $owner->id;

        $query->addValues($values);
        $this->writeItem($query);
        $lastInsertId = $this->getWriter()->lastInsertId();
        return $this->readEntity($lastInsertId);
    }

    /**
     * read list of owners by organisation
     *
     * @param
     * resolveReferences
     *
     * @return Resource Collection
     */
    public function readByOrganisationId($organisationId, $resolveReferences = 0)
    {
        $ticketprinterList = new Collection();
        $query = new Query\Ticketprinter(Query\Base::SELECT);
        $query
            ->addEntityMapping()
            ->addResolvedReferences($resolveReferences - 1)
            ->addConditionOrganisationId($organisationId);
        $result = $this->fetchList($query, new Entity());
        if (count($result)) {
            foreach ($result as $ticketprinter) {
                if ($ticketprinter instanceof Entity) {
                    $ticketprinterList->addEntity($ticketprinter);
                }
            }
            return $ticketprinterList;
        }
    }

     /**
     * remove an owner
     *
     * @param
     * itemId
     *
     * @return Resource Status
     */
    public function deleteEntity($itemId)
    {
        $query =  new Query\Ticketprinter(Query\Base::DELETE);
        $query->addConditionTicketprinterId($itemId);
        return $this->deleteItem($query);
    }
}
