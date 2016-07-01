<?php

namespace BO\Zmsdb;

use \BO\Zmsentities\UserAccount as Entity;

class UserAccount extends Base
{
    public function readEntity($loginname, $resolveReferences = 0)
    {
        $query = new Query\UserAccount(Query\Base::SELECT);
        $query
            ->addEntityMapping()
            ->addResolvedReferences($resolveReferences)
            ->addConditionLoginName($loginname);
        return $this->fetchOne($query, new Entity());
    }

    /**
     * update a userAccount
     *
     * @param
     * userAccountId
     *
     * @return Entity
     */
    public function updateEntity($loginName, \BO\Zmsentities\UserAccount $entity)
    {
        $query = new Query\UserAccount(Query\Base::UPDATE);
        $query->addConditionLoginName($loginName);
        $values = $query->reverseEntityMapping($entity);
        $query->addValues($values);
        $this->writeItem($query, 'userAccount', $query::TABLE);
        return $this->readEntity($userAccountId);
    }
}
