<?php

namespace BO\Zmsapi\Helper;

use \BO\Slim\Render;
use \BO\Zmsdb\UserAccount;
use \BO\Zmsdb\Workstation;

/**
 * example class to generate a response
 */
class User
{
    public static $workstation = null;

    public static $assignedWorkstation = null;

    public static function readWorkstation()
    {
        if (! static::$workstation) {
            $xAuthKey = static::getXAuthKey();
            $useraccount = (new UserAccount())->readEntityByAuthKey($xAuthKey);
            if ($useraccount->hasId()) {
                static::$workstation = (new Workstation())->readEntity($useraccount->id, 2);
            } else {
                static::$workstation = new \BO\Zmsentities\Workstation();
            }
        }
        return static::$workstation;
    }

    /**
     * @throws \BO\Zmsapi\Exception\Workstation\WorkstationAlreadyAssigned
     *
     */
    public static function testWorkstationAssigend(\BO\Zmsentities\Workstation $entity, $resolveReferences = 0)
    {
        if (! static::$assignedWorkstation) {
            static::$assignedWorkstation = (new Workstation())->readWorkstationByScopeAndName(
                $entity->scope['id'],
                $entity->name,
                $resolveReferences
            );
        }
        if (static::$assignedWorkstation && ! static::$assignedWorkstation->getUseraccount()->isOveraged(\App::$now)) {
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
            $workstation->getUseraccount()->testRights(func_get_args(), \App::$now);
        }
        return $workstation;
    }

    /**
     * @return \BO\Zmsentities\Department
     *
     */
    public static function checkDepartment($departmentId)
    {
        $workstation = static::readWorkstation();
        $userAccount = $workstation->getUseraccount();
        if (!$userAccount->hasId()) {
            throw new \BO\Zmsentities\Exception\UserAccountMissingLogin();
        }
        $department = $userAccount->testDepartmentById($departmentId);
        return $department;
    }

    public static function hasRights()
    {
        $userAccount = static::readWorkstation()->getUseraccount();
        return $userAccount->hasId();
    }

    public static function getXAuthKey()
    {
        $request = Render::$request;
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
}
