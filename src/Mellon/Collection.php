<?php
/**
 * @package Mellon
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Mellon;

/**
  * Collection for Validation tests
  *
  */
class Collection
{
    /**
      * Hash of validators
      *
      * @var Array $validatorList
      */
    protected $validatorList = array();

    public function __construct($validatorList)
    {
        $this->validatorList = $validatorList;
        $flat = $this->getFlatArray();
        foreach ($flat as $key => $valid) {
            if (!$valid instanceof Valid) {
                throw new Exception("No Valid value for $key");
            }
        }
    }

    /**
     * @return Array
     */
    protected function getFlatArray()
    {
        $arrayIterator = new \RecursiveArrayIterator(array($this->validatorList));
        $iterator = new \RecursiveIteratorIterator($arrayIterator, \RecursiveIteratorIterator::SELF_FIRST);
        $flat = array_filter(iterator_to_array($iterator), function ($value) {
            return !is_array($value);
        });
        return $flat;
    }

    /**
     * @return Bool
     */
    public function hasFailed()
    {
        $flat = $this->getFlatArray();
        foreach ($flat as $valid) {
            if ($valid->hasFailed()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return Array
     */
    public function getStatus($sub = null, $getUnvalidated = false)
    {
        $messages = array();
        if (null === $sub) {
            $sub = $this->validatorList;
        }
        foreach ($sub as $key => $value) {
            if ($value instanceof Valid) {
                $messages[$key] = $value->getStatus($getUnvalidated);
            } else {
                $messages[$key] = $this->getStatus($value);
            }
        }
        return $messages;
    }

    /**
     * @return Array
     */
    public function getValues()
    {
        return $this->validatorList;
    }
}
