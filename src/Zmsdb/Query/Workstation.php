<?php

namespace BO\Zmsdb\Query;

/**
 * @SuppressWarnings(Public)
 *
 */
class Workstation extends Base implements MappingInterface
{
    /**
     * @var String TABLE mysql table reference
     */
    const TABLE = 'nutzer';

    const QUERY_LOGIN = '
        UPDATE
            '. self::TABLE .'
        SET
            SessionID=?,
            Datum=?,
            Arbeitsplatznr="",
            StandortID=0
        WHERE
            Name=? AND
            Passworthash = ?
    ';

    const QUERY_LOGOUT = '
        UPDATE
            '. self::TABLE .'
        SET
            SessionID="",
            StandortID=0,
            BehoerdenID=0,
            Arbeitsplatznr="",
            aufrufzusatz=""
        WHERE
            Name=?
    ';

    protected function addRequiredJoins()
    {
    }

    public function getEntityMapping()
    {
        return [
            'id' => 'workstation.NutzerID',
            'hint' => 'workstation.aufrufzusatz',
            'name' => 'workstation.Arbeitsplatznr',
            'queue__appointmentsOnly' => 'workstation.Kalenderansicht',
            'queue__clusterEnabled' => 'workstation.clusteransicht',
            'scope__id' => 'workstation.StandortID'
        ];
    }

    public function addJoin()
    {
        return [
            $this->addJoinProcess(),
            $this->addJoinUseraccount(),
            $this->addJoinScope(),
        ];
    }

    public function addJoinScope()
    {
        $this->leftJoin(
            new Alias(Scope::TABLE, 'scope'),
            'workstation.StandortID',
            '=',
            'scope.StandortID'
        );
        $joinQuery = new Scope($this, $this->getPrefixed('scope__'));
        return $joinQuery;
    }


    public function addJoinUseraccount()
    {
        $this->leftJoin(
            new Alias(UserAccount::TABLE, 'userAccount'),
            'workstation.NutzerID',
            '=',
            'userAccount.NutzerID'
        );
        $joinQuery = new UserAccount($this, $this->getPrefixed('useraccount__'));
        return $joinQuery;
    }

    public function addJoinProcess()
    {
        $this->leftJoin(
            new Alias(Process::TABLE, 'process'),
            'workstation.NutzerID',
            '=',
            'process.NutzerID'
        );
        $joinQuery = new Process($this, $this->getPrefixed('process__'));
        return $joinQuery;
    }

    public function addConditionLoginName($loginName)
    {
        $this->query->where('workstation.Name', '=', $loginName);
        return $this;
    }

    public function addConditionWorkstationName($workstationName)
    {
        $this->query->where('workstation.Arbeitsplatznr', '=', $workstationName);
        return $this;
    }

    public function addConditionWorkstationId($workstationId)
    {
        $this->query->where('workstation.NutzerID', '=', $workstationId);
        return $this;
    }

    public function addConditionScopeId($scopeId)
    {
        $this->query->where('workstation.StandortID', '=', $scopeId);
        return $this;
    }

    public function addConditionTime($now)
    {
        $this->query->where('workstation.Datum', '=', $now->format('Y-m-d'));
        return $this;
    }

    public function addConditionDepartmentId($departmentId)
    {
        $this->leftJoin(
            new Alias(UserAccount::TABLE_ASSIGNMENT, 'workstation_department'),
            'workstation.NutzerID',
            '=',
            'workstation_department.nutzerid'
        );
        $this->query->where('workstation_department.behoerdenid', '=', $departmentId);
        return $this;
    }

    public function reverseEntityMapping(\BO\Zmsentities\Workstation $entity)
    {
        $data = array();
        $data['aufrufzusatz'] = ('' == $entity->hint) ? $entity->name : $entity->hint;
        $data['Kalenderansicht'] = $entity->getQueuePreference('appointmentsOnly', true);
        $data['clusteransicht'] = $entity->getQueuePreference('clusterEnabled', true);
        $data['StandortID'] = $entity->scope['id'];
        $data['Arbeitsplatznr'] = $entity->name;

        $data = array_filter($data, function ($value) {
            return ($value !== null && $value !== false);
        });
        return $data;
    }

    public function postProcess($data)
    {
        if (isset($data["useraccount__lastLogin"])) {
            $data["useraccount__lastLogin"] = ('0000-00-00' != $data["useraccount__lastLogin"]) ?
                strtotime($data["useraccount__lastLogin"]) : null;
        }
        return $data;
    }
}
