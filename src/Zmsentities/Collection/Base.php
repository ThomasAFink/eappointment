<?php
/**
 * @package Dldb
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsentities\Collection;

use \BO\Zmsentities\Helper\Sorter;
use \BO\Zmsentities\Schema\Entity;

/**
 * @SuppressWarnings(NumberOfChildren)
 * @SuppressWarnings(Public)
 *
 */
class Base extends \ArrayObject
{
    const ENTITY_CLASS = '';

    public function getFirst()
    {
        $item = reset($this);
        return $item;
    }

    public function getLast()
    {
        $item = end($this);
        return $item;
    }

    public function sortByName()
    {
        $this->uasort(function ($a, $b) {
            return strcmp(
                Sorter::toSortableString(ucfirst($a->name)),
                Sorter::toSortableString(ucfirst($b->name))
            );
        });
        return $this;
    }

    public function sortByContactName()
    {
        $this->uasort(function ($a, $b) {
            return strcmp(
                Sorter::toSortableString(ucfirst($a->contact['name'])),
                Sorter::toSortableString(ucfirst($b->contact['name']))
            );
        });
        return $this;
    }

    public function sortByArrivalTime()
    {
        $this->uasort(function ($a, $b) {
            return ($a->queue['arrivalTime'] - $b->queue['arrivalTime']);
        });
        return $this;
    }

    public function sortByTimeKey()
    {
        $this->uksort(function ($a, $b) {
            return ($a - $b);
        });
        return $this;
    }

    public function sortByCustomKey($key)
    {
        $this->uasort(function ($a, $b) use ($key) {
            return ($a[$key] - $b[$key]);
        });
        return $this;
    }

    public function __clone()
    {
        foreach ($this as $key => $property) {
            $this[$key] = clone $property;
        }
    }

    public function hasEntity($primary)
    {
        foreach ($this as $entity) {
            if (isset($entity->{$entity::PRIMARY}) && $primary == $entity->{$entity::PRIMARY}) {
                return true;
            }
        }
        return false;
    }

    public function getEntity($primary)
    {
        foreach ($this as $entity) {
            if (isset($entity->{$entity::PRIMARY}) && $primary == $entity->{$entity::PRIMARY}) {
                return $entity;
            }
        }
        return null;
    }

    public function addEntity(\BO\Zmsentities\Schema\Entity $entity)
    {
        $this->offsetSet(null, $entity);
        return $this;
    }

    public function offsetSet($index, $value)
    {
        $className = $this::ENTITY_CLASS;
        if (is_a($value, $className)) {
            return parent::offsetSet($index, $value);
        } elseif (is_array($value)) {
            return parent::offsetSet($index, new $className($value));
        } else {
            throw new \Exception('Invalid entity ' . get_class($value) . ' for collection '. __CLASS__);
        }
    }

    public function addData($mergeData)
    {
        foreach ($mergeData as $item) {
            if ($item instanceof Entity) {
                $this->addEntity($item);
            } else {
                $className = $this::ENTITY_CLASS;
                $entity = new $className($item);
                $this->addEntity($entity);
            }
        }
        return $this;
    }

    public function addList(Base $list)
    {
        foreach ($list as $item) {
            $this->addEntity($item);
        }
        return $this;
    }

    public function getIds()
    {
        $list = [];
        foreach ($this as $entity) {
            $list[] = $entity->id;
        }
        return $list;
    }

    public function getIdsCsv()
    {
        return implode(',', $this->getIds());
    }

    /**
     * Reduce items data of dereferenced entities to a required minimum
     *
     */
    public function withLessData()
    {
        $list = new static();
        foreach ($this as $key => $item) {
            $list[$key] = $item->withLessData();
        }
        return $list;
    }

    public function __toString()
    {
        $list = [];
        foreach ($this as $item) {
            $list[] = $item->__toString();
        }
        return "[" . implode(',', $list) . "]";
    }
}
