<?php
/**
 * @package Dldb
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Dldb\Collection;

class Offices extends Base
{
    public static function setSidebar()
    {
        $menu = array(
                array('path' => 'authoritylist', 'name' => _('Behörden A-Z'), 'rank' => '-2'),
                array('path' => 'serviceatlas', 'name' => _('Serviceatlas'), 'rank' => '-1')
        );
        $officelist = \App::$repository->fetchOfficeList();
        foreach ($officelist as $office) {
            $menu[] = array(
                'type' => 'office',
                'path' => $office['path'],
                'name' => _($office['name']),
                'rank' => $office['rank']
            );
        }
        return $menu;
    }
}
