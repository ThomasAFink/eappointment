<?php
/**
 * @package ClientDldb
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/
namespace BO\Dldb\MySQL;

use \BO\Dldb\MySQL\Entity\Authority as Entity,
    \BO\Dldb\MySQL\Entity\Location as LocationEntity,   
    \BO\Dldb\MySQL\Collection\Authorities as Collection,
    \BO\Dldb\Elastic\Authority as Base,
    \BO\Dldb\MySQL\Location AS LocationAccess
;

/**
 */
class Authority extends Base
{

    /**
     * fetch locations for a list of service and group by authority
     *
     * @return Collection\Authorities
     */
    public function fetchList($servicelist = [])
    {
        try {
            $authorityList = new Collection();

            $sqlArgs = [$this->locale];
            $sqlArgs = ['de'];
            
            if (!empty($servicelist)) {
                $sqlArgs[] = $this->locale;
                $qm = array_fill(0, count($servicelist), '?');

                $sql = "SELECT l.data_json 
                    FROM location_service ls
                    LEFT JOIN location AS l ON l.id = ls.location_id AND l.locale = ?
                    WHERE ls.locale = ? AND ls.service_id IN (" . implode(', ', $qm) . ")
                    GROUP BY ls.location_id 
                    ORDER BY l.name";

                array_push($sqlArgs, ...$servicelist);
                
                $stm = $this->access()->prepare($sql);
                $stm->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE, '\\BO\\Dldb\\MySQL\\Entity\\Location');
                $stm->execute($sqlArgs);

                $locations = $stm->fetchAll();
                foreach ($locations as $location) {
                    $authorityList->addLocation($location);
                }
            }
            else {
                $sql = 'SELECT data_json FROM authority WHERE locale = ?';
                $stm = $this->access()->prepare($sql);
                $stm->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE, '\\BO\\Dldb\\MySQL\\Entity\\Authority');
                $stm->execute($sqlArgs);
                $authorities = $stm->fetchAll();
                foreach ($authorities as $authority) {
                    $authorityList[$authority['id']] = $authority;
                }
            }
            return $authorityList;
        }
        catch (\Exception $e) {
            throw $e;
        }

    }

    /**
     * fetch locations for a list of service and group by authority
     *
     * @return Collection\Authorities
     */
    public function fetchId($id)
    {
        try {
            $sqlArgs = [$this->locale, $id];
            $sqlArgs = ['de', $id];
            
            
            $sql = 'SELECT data_json FROM authority WHERE locale = ? AND id = ?';
            $stm = $this->access()->prepare($sql);
            $stm->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE, '\\BO\\Dldb\\MySQL\\Entity\\Authority');
            $stm->execute($sqlArgs);


            $stm->execute($sqlArgs);
            if (!$stm || ($stm && $stm->rowCount() == 0)) {
                return false;
            }
            $authority = $stm->fetch();
            return $authority;
        }
        catch (\Exception $e) {
            throw $e;
        }

    }

    /**
     *
     * @return Collection
     */
    public function readListByOfficePath($officepath)
    {
        $authorityList = new Collection();

        $locations = $this->access()->fromLocation($this->locale)->fetchListByOffice($officepath);

        foreach ($locations AS $location) {
            $authorityList->addLocation($location);
        }
        
        #echo '<pre>' . print_r($authorityList,1) . '</pre>';exit;


        return $authorityList;
    }
}
