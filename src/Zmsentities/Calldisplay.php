<?php

namespace BO\Zmsentities;

class Calldisplay extends Schema\Entity
{
    const PRIMARY = 'serverTime';

    public static $schema = "calldisplay.json";

    public function getDefaults()
    {
        return [
            'serverTime' => (new \DateTime())->getTimestamp(),
        ];
    }

    public function withResolvedCollections($input)
    {
        if (array_key_exists('scopelist', $input)) {
            $this->scopes = $this->getScopeListFromCsv($input['scopelist']);
        }
        if (array_key_exists('clusterlist', $input)) {
            $this->clusters = $this->getClusterListFromCsv($input['clusterlist']);
        }
        return $this;
    }

    public function hasScopeList()
    {
        return $this->toProperty()->scopes->isAvailable();
    }

    public function hasClusterList()
    {
        return $this->toProperty()->clusters->isAvailable();
    }

    public function setServerTime($timestamp)
    {
        $this->serverTime = $timestamp;
        return $this;
    }

    public function getScopeList()
    {
        $scopeList = new Collection\ScopeList();
        if ($this->hasScopeList()) {
            foreach ($this->scopes as $scope) {
                $scopeList->addEntity(new Scope($scope));
            }
        }
        return $scopeList;
    }

    public function getClusterList()
    {
        $clusterList = new Collection\ClusterList();
        if ($this->hasClusterList()) {
            foreach ($this->clusters as $cluster) {
                $clusterList->addEntity(new Cluster($cluster));
            }
        }
        return $clusterList;
    }

    public function getImageName()
    {
        $name = '';
        if (1 == $this->getScopeList()->count()) {
            $name = "s_" . $this->getScopeList()->getFirst()->id . "_bild";
        } elseif (1 == $this->getClusterList()->count()) {
            $name = "c_" . $this->getClusterList()->getFirst()->id . "_bild";
        }
        return $name;
    }

    public function withOutClusterDuplicates()
    {
        $calldisplay = new self($this);
        if ($calldisplay->hasClusterList() && $calldisplay->hasScopeList()) {
            $clusterScopeList = new Collection\ScopeList();
            foreach ($calldisplay->clusters as $cluster) {
                if (array_key_exists('scopes', $cluster)) {
                    foreach ($cluster['scopes'] as $clusterScope) {
                        $scope = new Scope($clusterScope);
                        $clusterScopeList->addEntity($scope);
                    }
                }
            }
            $scopeList = new Collection\ScopeList();
            foreach ($calldisplay->scopes as $scope) {
                if (! $clusterScopeList->hasEntity($scope['id'])) {
                    $scope = new Scope($scope);
                    $scopeList->addEntity($scope);
                }
            }
            $calldisplay->scopes = $scopeList;
        }
        return $calldisplay;
    }

    protected function getScopeListFromCsv($scopeIds = '')
    {
        $scopeList = new Collection\ScopeList();
        $scopeIds = explode(',', $scopeIds);
        if ($scopeIds) {
            foreach ($scopeIds as $scopeId) {
                $scope = new Scope(array('id' => $scopeId));
                $scopeList->addEntity($scope);
            }
        }
        return $scopeList;
    }

    protected function getClusterListFromCsv($clusterIds = '')
    {
        $clusterList = new Collection\ClusterList();
        $clusterIds = explode(',', $clusterIds);
        if ($clusterIds) {
            foreach ($clusterIds as $clusterId) {
                $cluster = new Cluster(array('id' => $clusterId));
                $clusterList->addEntity($cluster);
            }
        }
        return $clusterList;
    }
}
