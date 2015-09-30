<?php
/**
 * @package Dldb
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Dldb\Collection;

class Authorities extends Base
{

    public function __clone()
    {
        foreach ($this as $key => $authority) {
            $this[$key] = clone $authority;
        }
    }

    public function addLocation(\BO\Dldb\Entity\Location $location)
    {
        if (array_key_exists('authority', $location)) {
            $this->addAuthority($location['authority']['id'], $location['authority']['name']);
            $this[$location['authority']['id']]['locations'][$location['id']] = $location;
        }
        return $this;
    }

    public function addAuthority($authority_id, $name)
    {
        if (!$this->hasAuthority($authority_id)) {
            $authority = \BO\Dldb\Entity\Authority::create($name);
            $this[$authority_id] = $authority;
        }
        return $this;
    }

    public function hasAuthority($authority_id)
    {
        return array_key_exists($authority_id, $this);
    }
    
    
    /**
     * Check if appointments are available
     *
     * @param String $serviceCsv only check for this serviceCsv
     * @param Bool $external allow external links, default false
     *
     * @return Bool
     */
    public function hasLocations()
    {
        foreach ($this as $authority) {
            if ($authority->hasLocations()) {
                return true;
            }
        }
        return false;
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
        foreach ($this as $authority) {
            if ($authority->hasAppointments($serviceCsv, $external)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Check if ea_id location exists
     *
     * @param Int $locationId
     *
     * @return Bool
     */
    public function hasLocationId($locationId)
    {
        foreach ($this as $authority) {
            if ($authority->hasLocationId($locationId)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Remove a location
     *
     * @param Int $locationId
     *
     * @return clone self
     */
    public function removeLocation($locationId)
    {
        $authorityList = clone $this;
        foreach ($authorityList as $key => $authority) {
            $authorityList[$key] = $authority->removeLocation($locationId);
        }
        return $authorityList;
    }

    /**
     * remove locations if no appointment is available
     *
     * @param String $serviceCsv only check for this serviceCsv
     * @param Bool $external allow external links, default false
     *
     * @return self
     */
    public function removeLocationsWithoutAppointments($serviceCsv = null, $external = false)
    {
        $authorityIterator = $this->getIterator();
        foreach ($authorityIterator as $key => $authority) {            
            if ($authority->hasAppointments($serviceCsv, $external)) {                
                $locationIterator = $authority['locations']->getIterator();
                foreach ($locationIterator as $subkey => $location) {
                    if (!$location->hasAppointments($serviceCsv, $external)) {
                        $locationIterator->offsetUnset($subkey);
                    }
                }
            } else {
                $authorityIterator->offsetUnset($key);
            }
        }
        return $this;
    }

    public function removeEmptyAuthorities()
    {
        $authoritylist = new self();
        foreach ($this as $key => $authority) {
            if ($authority->hasLocations()) {
                $authoritylist[$key] = clone $authority;
            }
        }
        return $authoritylist;
    }

    public function removeLocations()
    {
        $authoritylist = clone $this;
        foreach ($authoritylist as $authority) {
            $authority['locations'] = new Locations();
        }
        return $authoritylist;
    }

    public function getWithOffice($officepath)
    {
        $authoritylist = clone $this;
        foreach ($authoritylist as $key => $authority) {
            $authoritylist[$key] = $authority->getWithOffice($officepath);
        }
        return $authoritylist->removeEmptyAuthorities();
    }
}
