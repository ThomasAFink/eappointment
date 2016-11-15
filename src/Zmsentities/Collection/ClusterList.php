<?php
namespace BO\Zmsentities\Collection;

class ClusterList extends Base
{
    public function hasScope($scopeId)
    {
        foreach ($this as $entity) {
            foreach ($entity['scopes'] as $scope) {
                $scope = new \BO\Zmsentities\Scope($scope);
                if ($scopeId == $scope->id) {
                    return true;
                }
            }
        }
        return false;
    }

    public function withUniqueClusters()
    {
        $clusterList = new self();
        foreach ($this as $cluster) {
            if (! $clusterList->hasEntity($cluster->id)) {
                $clusterList->addEntity($cluster);
            }
        }
        return $clusterList;
    }
}
