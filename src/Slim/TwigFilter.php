<?php
/**
 * @package   BO Slim
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Slim;

/**
  * Extension for Twig and Slim
  *
  */
class TwigFilter extends \Twig_Extension
{

    /**
     * @var \Slim\Interfaces\RouterInterface
     */
    private $router;

    /**
     * @var string|\Slim\Http\Uri
     */
    private $uri;

    public function __construct($router, $uri)
    {
        $this->router = $router;
        $this->uri = $uri;
    }

    public function getName()
    {
        return 'slim';
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('msort', array($this, 'msort')),
            new \Twig_SimpleFilter('getObjectName', array($this, 'getObjectName'))
        );
    }

    public function msort($array, $key, $sort_flags = SORT_REGULAR)
    {
        if (is_array($array) && count($array) > 0) {
            if (!empty($key)) {
                $mapping = array();
                foreach ($array as $k => $v) {
                    $sort_key = '';
                    if (!is_array($key)) {
                        $sort_key = $v[$key];
                    } else {
                        // @TODO This should be fixed, now it will be sorted as string
                        foreach ($key as $key_key) {
                            $sort_key .= $v[$key_key];
                        }
                        $sort_flags = SORT_STRING;
                    }
                    $mapping[$k] = $sort_key;
                }
                asort($mapping, $sort_flags);
                $sorted = array();
                foreach ($mapping as $k => $v) {
                    $sorted[] = $array[$k];
                }
                return $sorted;
            }
        }
        return $array;
    }

    public function getObjectName($object)
    {
        return (new \ReflectionClass($object))->getShortName();
    }
}
