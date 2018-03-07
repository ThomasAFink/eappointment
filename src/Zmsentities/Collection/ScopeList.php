<?php
namespace BO\Zmsentities\Collection;

use \BO\Zmsentities\Helper\Sorter;
use \BO\Zmsentities\Scope;

class ScopeList extends Base
{
    const ENTITY_CLASS = '\BO\Zmsentities\Scope';

    protected $slotsByID = [];

    public function getAlternateRedirectUrl()
    {
        $scope = reset($this);
        return (1 == count($this) && $scope->getAlternateRedirectUrl()) ? $scope->getAlternateRedirectUrl() : null;
    }

    /**
    * Get longest bookable end date of a scope in scopelist
    *
    * @return \DateTimeImmutable $date
    */
    public function getGreatestBookableEnd($now)
    {
        $date = $now;
        foreach ($this as $scope) {
            $endDate = $scope->getBookableEndDate($now);
            $date = ($endDate > $date) ? $endDate : $date;
        }
        return $date;
    }

    public function withoutDublicates($scopeList = null)
    {
        $collection = new self();
        foreach ($this as $scope) {
            if (! $scopeList || ! $scopeList->hasEntity($scope->id)) {
                $collection->addEntity(clone $scope);
            }
        }
        return $collection;
    }

    public function withUniqueScopes()
    {
        $scopeList = new self();
        foreach ($this as $scope) {
            if (! $scopeList->hasEntity($scope->id)) {
                $scopeList->addEntity(clone $scope);
            }
        }
        return $scopeList;
    }

    public function addScopeList($scopeList)
    {
        foreach ($scopeList as $scope) {
            $this->addEntity($scope);
        }
        return $this;
    }

    public function withLessData(array $keepArray = [])
    {
        $scopeList = new self();
        foreach ($this as $scope) {
            $scopeList->addEntity(clone $scope->withLessData($keepArray));
        }
        return $scopeList;
    }

    public function sortByName()
    {
        $this->uasort(function ($a, $b) {
            $nameA = (isset($a->provider['name'])) ? $a->provider['name'] : $a->shortName;
            $nameB = (isset($b->provider['name'])) ? $b->provider['name'] : $b->shortName;
            return strcmp(
                Sorter::toSortableString(ucfirst($nameA)),
                Sorter::toSortableString(ucfirst($nameB))
            );
        });
        return $this;
    }

    public function withProviderID($source, $providerID)
    {
        $list = new ScopeList();
        foreach ($this as $scope) {
            if ($scope->provider['source'] == $source && $scope->provider['id'] == $providerID) {
                $list->addEntity(clone $scope);
            }
        }
        return $list;
    }

    public function addRequiredSlots($source, $providerID, $slotsRequired)
    {
        $scopeList = $this->withProviderID($source, $providerID);
        foreach ($scopeList as $scope) {
            if (!isset($this->slotsByID[$scope->id])) {
                $this->slotsByID[$scope->id] = 0;
            }
            $this->slotsByID[$scope->id] += $slotsRequired;
        }
        return $this;
    }

    public function getRequiredSlotsByScope(Scope $scope)
    {
        if (isset($this->slotsByID[$scope->id])) {
            return $this->slotsByID[$scope->id];
        }
        return 0;
    }
}
