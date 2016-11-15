<?php
namespace BO\Zmsentities\Collection;

class DepartmentList extends Base
{
    public function withOutClusterDuplicates()
    {
        $departmentList = new self();
        foreach ($this as $department) {
            $entity = new \BO\Zmsentities\Department($department);
            $departmentList->addEntity($entity->withOutClusterDuplicates());
        }
        return $departmentList;
    }

    public function getUniqueScopeList()
    {
        $scopeList = new ScopeList();
        $clusterList = $this->getUniqueClusterList();
        foreach ($this as $department) {
            $entity = new \BO\Zmsentities\Department($department);
            foreach ($entity->scopes as $scope) {
                $scope = new \BO\Zmsentities\Scope($scope);
                $scopeList->addEntity($scope);
            }
        }
        foreach ($clusterList as $cluster) {
            foreach ($cluster->scopes as $scope) {
                $scope = new \BO\Zmsentities\Scope($scope);
                $scopeList->addEntity($scope);
            }
        }
        error_log(var_export($scopeList, 1));
        return $scopeList->withUniqueScopes();
    }

    public function getUniqueClusterList()
    {
        $clusterList = new ClusterList();
        foreach ($this as $department) {
            foreach ($department['clusters'] as $cluster) {
                $entity = new \BO\Zmsentities\Cluster($cluster);
                $clusterList->addEntity($entity);
            }
        }
        return $clusterList->withUniqueClusters();
    }
}
