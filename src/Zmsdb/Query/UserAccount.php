<?php

namespace BO\Zmsdb\Query;

use BO\Zmsdb\Helper\RightsLevelManager;

class UserAccount extends Base implements MappingInterface
{
    /**
     * @var String TABLE mysql table reference
     */
    const TABLE = 'nutzer';
    const TABLE_ASSIGNMENT = 'nutzerzuordnung';

    const QUERY_READ_ASSIGNED_DEPARTMENTS = '
        SELECT userAssignment.`behoerdenid` AS id
        FROM '. self::TABLE_ASSIGNMENT .' userAssignment
        LEFT JOIN '. self::TABLE .' userAccount ON userAccount.Name = :userAccountName
        WHERE
            userAccount.`NutzerID` = userAssignment.`nutzerid`
    ';

    public function getEntityMapping()
    {
        return [
        'id' => 'userAccount.Name','lastLogin' => 'userAccount.Datum',
        'rights__superuser' => self::expression('`userAccount`.`Berechtigung` = 90'),
        'rights__organisation' => self::expression('`userAccount`.`Berechtigung` >= 70'),
        'rights__department' => self::expression('`userAccount`.`Berechtigung` >= 50'),
        'rights__cluster' => self::expression('`userAccount`.`Berechtigung` >= 40'),
        'rights__useraccount' => self::expression('`userAccount`.`Berechtigung` >= 30'),
        'rights__scope' => self::expression('`userAccount`.`Berechtigung` >= 20'),
        'rights__availability' => self::expression('`userAccount`.`Berechtigung` >= 15'),
        'rights__ticketprinter' => self::expression('`userAccount`.`Berechtigung` >= 10'),
        'rights__sms' => self::expression('`userAccount`.`Berechtigung` >= 0'),
        'rights__basic' => self::expression('`userAccount`.`Berechtigung` = 0'),
        'scope__0__id' => 'userAccount.StandortID'
        ];
    }

    public function getReferenceMapping()
    {
        return [
            'departments__0__$ref' => self::expression('CONCAT("/department/", `userAccount`.`BehoerdenID`, "/")'),
        ];
    }

    public function addConditionLoginName($loginName)
    {
        $this->query->where('userAccount.Name', '=', $loginName);
        return $this;
    }

    public function addConditionPassword($password)
    {
        $this->query->where('userAccount.Passworthash', '=', md5($password));
        return $this;
    }

    public function addConditionXauthKey($xAuthKey)
    {
        $this->query->where('userAccount.SessionID', '=', $xAuthKey);
        return $this;
    }


    public function reverseEntityMapping(\BO\Zmsentities\UserAccount $entity)
    {
        $data = array();
        $data['Name'] = $entity->id;
        $data['Passworthash'] = (isset($entity->password)) ? md5($entity->password) : null;
        $data['Berechtigung'] = RightsLevelManager::getLevel($entity->rights);
        $data['BehoerdenID'] = $entity->getDepartmentId();
        //default values because of strict mode
        $data['notrufinitiierung'] = 0;
        $data['notrufantwort'] = 0;

        $data = array_filter($data, function ($value) {
            return ($value !== null && $value !== false);
        });
            return $data;
    }
}
