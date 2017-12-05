<?php

namespace BO\Zmsapi\Helper;

use \BO\Slim\Render;

/**
 *
 * @SuppressWarnings(CouplingBetweenObjects)
 */
class ExchangeAccessFilter
{
    protected static $filterList = [
        'scope.id' => 'getFilteredEntityByScope',
        'department.id' => 'getFilteredEntityByDepartment',
        'organisation.id' => 'getFilteredEntityByOrganisation'
    ];

    protected static $exchangeEntity = null;

    protected static $filteredEntity = null;

    protected static $workstation = null;

    public function __construct($exchangeEntity, $workstation)
    {
        static::$exchangeEntity = $exchangeEntity;
        static::$workstation = $workstation;
    }

    /**
     * @return \BO\Zmsentities\Exchange
     *
     */
    public function getFilteredEntity()
    {
        static::$filteredEntity = clone static::$exchangeEntity;
        foreach (static::$exchangeEntity->dictionary as $entry) {
            if ($entry['reference'] && isset(static::$filterList[$entry['reference']])) {
                $filterMethod = self::$filterList[$entry['reference']];
                foreach (static::$filteredEntity->data as $key => $data) {
                    static::$filterMethod($data[$entry['position']], $key);
                }
            }
        }

        return static::$filteredEntity;
    }

    protected static function getFilteredEntityByScope($entityId, $filteredKey)
    {
        if (static::$workstation->getUseraccount()->hasRights(['scope'])) {
            if (! static::$workstation->getScopeListFromAssignedDepartments()->hasEntity($entityId)) {
                unset(static::$filteredEntity->data[$filteredKey]);
            }
        }
    }

    protected static function getFilteredEntityByDepartment($entityId, $filteredKey)
    {
        if (static::$workstation->getUseraccount()->hasRights(['department'])) {
            if (! static::$workstation->getDepartmentList()->hasEntity($entityId)) {
                unset(static::$filteredEntity->data[$filteredKey]);
            }
        }
    }

    protected static function getFilteredEntityByOrganisation($entityId, $filteredKey)
    {
        if (static::$workstation->getUseraccount()->hasRights(['organisation'])) {
            if (! static::getOrganisationListByDepartments()->hasEntity($entityId)) {
                unset(static::$filteredEntity->data[$filteredKey]);
            }
        }
    }

    protected static function getOrganisationListByDepartments()
    {
        $organisationList = new \BO\Zmsentities\Collection\OrganisationList();
        foreach (static::$workstation->getDepartmentList() as $department) {
            $organisation = (new \BO\Zmsdb\Organisation())->readByDepartmentId($department->id);
            if ($organisation && $organisation instanceof \BO\Zmsentities\Organisation) {
                $organisationList->addEntity($organisation);
            }
        }
        return $organisationList;
    }
}
