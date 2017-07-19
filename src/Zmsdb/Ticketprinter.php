<?php

namespace BO\Zmsdb;

use \BO\Zmsentities\Ticketprinter as Entity;
use \BO\Zmsentities\Collection\TicketprinterList as Collection;

/**
 *
 * @SuppressWarnings(CouplingBetweenObjects)
 */
class Ticketprinter extends Base
{
    /**
     * read entity
     *
     * @param
     * itemId
     *
     * @return Resource Entity
     */
    public function readEntity($itemId)
    {
        $query = new Query\Ticketprinter(Query\Base::SELECT);
        $query
            ->addEntityMapping()
            ->addConditionTicketprinterId($itemId);
        $ticketprinter = $this->fetchOne($query, new Entity());
        $ticketprinter = $this->readWithContactData($ticketprinter);
        return $ticketprinter;
    }

    /**
     * read list of ticketprinters
     *
     * @return Resource Collection
     */
    public function readList()
    {
        $ticketprinterList = new Collection();
        $query = new Query\Ticketprinter(Query\Base::SELECT);
        $query->addEntityMapping();
        $result = $this->fetchList($query, new Entity());
        if (count($result)) {
            foreach ($result as $ticketprinter) {
                if ($ticketprinter instanceof Entity) {
                    $ticketprinter = $this->readWithContactData($ticketprinter);
                    $ticketprinterList->addEntity($ticketprinter);
                }
            }
        }
        return $ticketprinterList;
    }

    /**
     * read Ticketprinter by Hash
     *
     * @param
     * hash
     *
     * @return Resource Entity
     */
    public function readByHash($hash)
    {
        $query = new Query\Ticketprinter(Query\Base::SELECT);
        $query
            ->addEntityMapping()
            ->addConditionHash($hash);
        $ticketprinter = $this->fetchOne($query, new Entity());
        $ticketprinter->enabled = (1 == $ticketprinter->enabled);
        $ticketprinter = $this->readWithContactData($ticketprinter);
        return $ticketprinter;
    }

    /**
     * read Ticketprinter by comma separated buttonlist
     *
     * @param
     * ticketprinter Entity
     * now DateTime
     *
     * @return Resource Entity
     */
    public function readByButtonList(Entity $ticketprinter, \DateTimeImmutable $now)
    {
        if (count($ticketprinter->buttons) > 6) {
            throw new Exception\Ticketprinter\TooManyButtons();
        }
        foreach ($ticketprinter->buttons as $key => $button) {
            if ('scope' == $button['type']) {
                $query = new Scope();
                $scope = $query->readWithWorkstationCount($button['scope']['id'], $now);
                if (! $scope->hasId()) {
                    throw new Exception\Ticketprinter\UnvalidButtonList();
                }
                $ticketprinter->buttons[$key]['scope'] = $scope;
                $ticketprinter->buttons[$key]['enabled'] = $query->readIsEnabled($scope->id, $now);
                $ticketprinter->buttons[$key]['name'] = $scope->getPreference('ticketprinter', 'buttonName');
            } elseif ('cluster' == $button['type']) {
                $query = new Cluster();
                $cluster = $query->readEntity($button['cluster']['id'], 1);
                if (! $cluster) {
                    throw new Exception\Ticketprinter\UnvalidButtonList();
                }
                $scopeList = $query->readEnabledScopeList($cluster->id, $now);
                $ticketprinter->buttons[$key]['cluster'] = $cluster;
                $ticketprinter->buttons[$key]['enabled'] = (1 <= $scopeList->count()) ? true : false;
                $ticketprinter->buttons[$key]['name'] = $cluster->getName();
            }
        }
        $this->readExceptions($ticketprinter);
        $ticketprinter = $this->readWithContactData($ticketprinter);
        return $ticketprinter;
    }

    protected function readExceptions(Entity $ticketprinter)
    {
        $query = new Scope();
        $scope = $this->readSingleScopeFromButtonList($ticketprinter);
        if ($scope && ! $query->readIsGivenNumberInContingent($scope['id'])) {
            throw new Exception\Scope\GivenNumberCountExceeded();
        }
        if ($scope && $scope->getStatus('ticketprinter', 'deactivated')) {
            throw new Exception\Ticketprinter\DisabledByScope(
                $scope->getPreference('ticketprinter', 'deactivatedText')
            );
        }
    }

    protected function readWithContactData(Entity $entity)
    {
        $contact = new \BO\Zmsentities\Contact();
        if (1 == $entity->getClusterList()->count() && 0 == $entity->getScopeList()->count()) {
            $contact->name = $entity->getClusterList()->getFirst()->name;
        } elseif (0 == $entity->getClusterList()->count() && 1 == $entity->getScopeList()->count()) {
            $department = (new Department())->readByScopeId($entity->getScopeList()->getFirst()->id);
            $contact->name = $department->name;
        }
        $entity->contact = $contact;
        return $entity;
    }

    public function readSingleScopeFromButtonList(Entity $ticketprinter)
    {
        $scope = null;
        if (1 == $ticketprinter->getScopeList()->count()) {
            $scope = $ticketprinter->getScopeList()->getFirst();
            $scope = (new Scope())->readEntity($scope['id']);
        } elseif (1 == $ticketprinter->getClusterList()->count()) {
            $scopeList = $ticketprinter->getClusterList()->getFirst()->scopes;
            $scopeList = new \BO\Zmsentities\Collection\ScopeList($scopeList);
            if (1 == $scopeList->count()) {
                $scope = (new Scope())->readEntity($scopeList->getFirst()['id']);
            }
        }
        return $scope;
    }

    /**
     * write a cookie for ticketprinter
     *
     * @param
     * organisationId
     *
     * @return Entity
     */
    public function writeEntityWithHash($organisationId)
    {
        $query = new Query\Ticketprinter(Query\Base::INSERT);
        $ticketprinter = (new Entity())->getHashWith($organisationId);

        $organisation = (new Organisation())->readEntity($organisationId);
        $owner = (new Owner())->readByOrganisationId($organisationId);
        $ticketprinter->enabled = (! $organisation->getPreference('ticketPrinterProtectionEnabled'));

        $values = $query->reverseEntityMapping($ticketprinter, $organisation->id);
        //get owner by organisation
        $owner = (new Owner())->readByOrganisationId($organisationId);
        $values['kundenid'] = $owner->id;
        $query->addValues($values);
        $this->writeItem($query);
        return $ticketprinter;
    }

    /**
     * write a ticketprinter
     *
     * @param
     * entity,
     * organisationId
     *
     * @return Entity
     */
    public function writeEntity(Entity $entity, $organisationId)
    {
        $query = new Query\Ticketprinter(Query\Base::INSERT);
        $values = $query->reverseEntityMapping($entity, $organisationId);

        //get owner by organisation
        $owner = (new Owner())->readByOrganisationId($organisationId);
        $values['kundenid'] = $owner->id;

        $query->addValues($values);
        $this->writeItem($query);
        $lastInsertId = $this->getWriter()->lastInsertId();
        return $this->readEntity($lastInsertId);
    }

    /**
     * read list of ticketprinter by organisation
     *
     * @param
     * organisationId
     *
     * @return Resource Collection
     */
    public function readByOrganisationId($organisationId)
    {
        $ticketprinterList = new Collection();
        $query = new Query\Ticketprinter(Query\Base::SELECT);
        $query
            ->addEntityMapping()
            ->addConditionOrganisationId($organisationId);
        $result = $this->fetchList($query, new Entity());
        if (count($result)) {
            foreach ($result as $ticketprinter) {
                if ($ticketprinter instanceof Entity) {
                    $ticketprinter = $this->readWithContactData($ticketprinter);
                    $ticketprinterList->addEntity($ticketprinter);
                }
            }
        }
        return $ticketprinterList;
    }

     /**
     * remove an ticketprinter
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
