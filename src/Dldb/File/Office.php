<?php
/**
 * @package 115Mandant
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Dldb\File;

use \BO\Dldb\Entity\Office as Entity;
use \BO\Dldb\Collection\Office as Collection;

/**
  * Common methods shared by access classes
  *
  */
class Office extends Base
{

    protected function parseData($data)
    {
        $itemList = new Collection();
        foreach ($data['data']['office'] as $item) {
            $itemList[$item['path']] = new Entity($item);
        }
        return $itemList;
    }

    public function fetchList()
    {
        return $this->getItemList();
    }

    public function fetchPath($itemId)
    {
        $itemList = $this->getItemList();
        if (isset($itemList[$itemId])) {
            return $itemList[$itemId];
        }
        return null;
    }
}
