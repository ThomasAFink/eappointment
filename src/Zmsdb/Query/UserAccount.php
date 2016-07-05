<?php

namespace BO\Zmsdb\Query;

class UserAccount extends Base implements MappingInterface
{
    /**
     * @var String TABLE mysql table reference
     */
    const TABLE = 'nutzer';

    public function getEntityMapping()
    {
        return [
            'lastlogin' => 'userAccount.Datum',
            'id' => 'userAccount.Name',
            'rights__superuser' => self::expression('`userAccount`.`Berechtigung` = 90'),
            'rights__organisation' => self::expression('`userAccount`.`Berechtigung` >= 70'),
            'rights__department' => self::expression('`userAccount`.`Berechtigung` >= 50'),
            'rights__cluster' => self::expression('`userAccount`.`Berechtigung` >= 40'),
            'rights__useraccount' => self::expression('`userAccount`.`Berechtigung` >= 30'),
            'rights__scope' => self::expression('`userAccount`.`Berechtigung` >= 20'),
            'rights__availability' => self::expression('`userAccount`.`Berechtigung` >= 15'),
            'rights__ticketprinter' => self::expression('`userAccount`.`Berechtigung` >= 10'),
            'rights__sms' => self::expression('`userAccount`.`Berechtigung` >= 0'),
            'departments__0' => 'userAccount.BehoerdenID'
        ];
    }

    public function addConditionLoginName($loginName)
    {
        $this->query->where('userAccount.Name', '=', $loginName);
        return $this;
    }

    public function addConditionXauthKey($xAuthKey)
    {
        $this->query->where('userAccount.SessionID', '=', $xAuthKey);
        $this->query->where('userAccount.SessionID', '<>', '');
        return $this;
    }


    public function reverseEntityMapping(\BO\Zmsentities\UserAccount $entity)
    {
        $data = array();
        $data['id'] = $entity->loginName;
        $data['Passworthash'] = $entity->password;
        $data['Berechtigung'] = $entity->getRights();
        $data['BehoerdenID'] = $entity->getDepartments();

        $data = array_filter($data, function ($value) {
            return ($value !== null && $value !== false);
        });
            return $data;
    }
}
