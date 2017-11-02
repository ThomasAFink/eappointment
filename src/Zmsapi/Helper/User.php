<?php

namespace BO\Zmsapi\Helper;

use \BO\Slim\Render;
use \BO\Zmsdb\Useraccount;
use \BO\Zmsdb\Workstation;

/**
 *
 * @SuppressWarnings(CouplingBetweenObjects)
 */
class User
{
    public static $workstation = null;
    public static $workstationResolved = null;

    public static $assignedWorkstation = null;

    public static $request = null;

    public function __construct($request, $resolveReferences = 0)
    {
        static::$request = $request;
        static::readWorkstation($resolveReferences);
    }

    public static function readWorkstation($resolveReferences = 0)
    {
        if (! static::$workstation) {
            $xAuthKey = static::getXAuthKey();
            $useraccount = (new Useraccount())->readEntityByAuthKey($xAuthKey);
            if ($useraccount->hasId()) {
                static::$workstation = (new Workstation())->readEntity($useraccount->id, $resolveReferences);
                if ($resolveReferences < 1) {
                    static::$workstation->useraccount = $useraccount;
                }
                static::$workstationResolved = $resolveReferences;
            } else {
                static::$workstation = new \BO\Zmsentities\Workstation();
            }
        }
        if ($resolveReferences > static::$workstationResolved && static::$workstation->hasId()) {
            static::$workstation = (new Workstation())
                ->readResolvedReferences(static::$workstation, $resolveReferences);
        }
        return static::$workstation;
    }

    /**
     * @throws \BO\Zmsapi\Exception\Workstation\WorkstationAlreadyAssigned
     *
     */
    public static function testWorkstationAssigend(\BO\Zmsentities\Workstation $entity, $resolveReferences = 0)
    {
        if (! static::$assignedWorkstation && $entity->name) {
            static::$assignedWorkstation = (new Workstation())->readWorkstationByScopeAndName(
                $entity->scope['id'],
                $entity->name,
                $resolveReferences
            );
        }
        if (static::$assignedWorkstation &&
            static::$assignedWorkstation->id != $entity->id &&
            static::$assignedWorkstation->name == $entity->name &&
            static::$assignedWorkstation->scope['id'] == $entity->scope['id'] &&
            ! static::$assignedWorkstation->getUseraccount()->isOveraged(\App::$now)
        ) {
            throw new \BO\Zmsapi\Exception\Workstation\WorkstationAlreadyAssigned();
        }
    }

    /**
     * @return \BO\Zmsentities\Workstation
     *
     */
    public static function checkRights()
    {
        $workstation = static::readWorkstation();
        if (\App::RIGHTSCHECK_ENABLED) {
            $workstation->getUseraccount()->testRights(func_get_args());
        }
        return $workstation;
    }

    /**
     * @return \BO\Zmsentities\Department
     *
     */
    public static function checkDepartment($departmentId)
    {
        $workstation = static::readWorkstation(2);
        $userAccount = $workstation->getUseraccount();
        if (! $userAccount->hasId()) {
            throw new \BO\Zmsentities\Exception\UseraccountMissingLogin();
        }
        if ($userAccount->rights['organisation']) {
            $department = self::testReadDepartmentByOrganisation($departmentId, $userAccount);
        } elseif (! $userAccount->isSuperUser()) {
            $department = $userAccount->testDepartmentById($departmentId);
        } else {
            $department = (new \BO\Zmsdb\Department())->readEntity($departmentId);
        }
        if (! $department) {
            throw new \BO\Zmsentities\Exception\UserAccountMissingDepartment(
                "No access to department " . htmlspecialchars($departmentId)
            );
        }
        return $department;
    }

    public static function hasRights()
    {
        $userAccount = static::readWorkstation()->getUseraccount();
        return $userAccount->hasId();
    }

    public static function getXAuthKey()
    {
        $request = (static::$request) ? static::$request : Render::$request;
        $xAuthKey = $request->getHeader('X-AuthKey');
        if (!$xAuthKey) {
            $cookies = $request->getCookieParams();
            if (array_key_exists('Zmsclient', $cookies)) {
                $xAuthKey = $cookies['Zmsclient'];
            }
        } else {
            $xAuthKey = current($xAuthKey);
        }
        return $xAuthKey;
    }


    public static function testWorkstationIsOveraged($workstation)
    {
        if ($workstation->hasId() && $workstation->getUseraccount()->isOveraged(\App::$now)) {
            $exception = new \BO\Zmsapi\Exception\Useraccount\AuthKeyFound();
            $exception->data = $workstation;
            throw $exception;
        }
    }

    protected static function testReadDepartmentByOrganisation($departmentId, $userAccount)
    {
        $organisation = (new \BO\Zmsdb\Organisation())->readByDepartmentId($departmentId, 1);
        $organisation->departments = $organisation->getDepartmentList()->withAccess($userAccount);
        $department = $organisation->departments->getEntity($departmentId);
        return $department;
    }

    public static function testUseraccountExists($loginName, $password, $input)
    {
        $query = new Useraccount();
        if (! $query->readIsUserExisting($loginName) || 0 == count($input)) {
            throw new \BO\Zmsapi\Exception\Useraccount\UseraccountNotFound();
        }
        if (! $query->readIsUserExisting($loginName, $password)) {
            throw new \BO\Zmsapi\Exception\Useraccount\InvalidCredentials();
        }
    }
}
