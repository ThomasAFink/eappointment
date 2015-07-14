<?php
/**
 * @package Dldb
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Dldb\Entity;

/**
  * Helper for service export
  *
  */
class Authority extends Base
{

    public static function create($name)
    {
        $data = array(
            'name' => $name,
            'locations' => new \BO\Dldb\Collection\Locations(),
        );
        return new self($data);
    }

    /**
     * Check if appointments are available
     *
     * @param String $serviceCsv only check for this serviceCsv
     * @param Bool $external allow external links, default false
     *
     * @return Bool
     */
    public function hasAppointments($serviceCsv = null, $external = false)
    {
        foreach ($this['locations'] as $location) {
            if ($location->hasAppointments($serviceCsv, $external)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Check if locations are available for defined office
     * @todo Remove this function, this is a data query and self manipulation, extreme bug probability
     *
     * @param String $officepath only check for this office
     *
     * @return Authority
     */
    public function getOffice($officepath = null)
    {
        foreach ($this['locations'] as $key => $location) {
            // better: Entity\Location::isOffice($officepath)
            if ($location['office'] != $officepath) {
                unset($this['locations'][$key]); // help :-/
            }
        }
        $data = array(
            'name' => $this['name'],
            'locations' => $this['locations']
        );
        if (count($data['locations'])) {
            return new self($data);
        }
    }
    
    /**
     * Check if locations are available for defined office
     * @todo this should be renamed to hasLocationId()
     *
     * @param String $officepath only check for this office
     *
     * @return Bool
     */
    public function hasEaId($ea_id = null)
    {
        foreach ($this['locations'] as $location) {
            if ($location['id'] == $ea_id) {
                return true;
            }
        }
        return false;
    }
}
