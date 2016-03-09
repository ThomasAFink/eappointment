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
class TwigExtension extends \Slim\Views\TwigExtension
{
    public function getName()
    {
        return 'boslim';
    }

    public function getFunctions()
    {
        $safe = array('is_safe' => array('html'));
        return array(
            new \Twig_SimpleFunction('urlGet', array($this, 'urlGet')),
            new \Twig_SimpleFunction('csvProperty', array($this, 'csvProperty')),
            new \Twig_SimpleFunction('azPrefixList', array($this, 'azPrefixList')),
            new \Twig_SimpleFunction('isValueInArray', array($this, 'isValueInArray')),
            new \Twig_SimpleFunction('remoteInclude', array($this, 'remoteInclude'), $safe),
            new \Twig_SimpleFunction('includeUrl', array($this, 'includeUrl')),
            new \Twig_SimpleFunction('currentLang', array($this, 'currentLang')),
            new \Twig_SimpleFunction('currentRoute', array($this, 'currentRoute')),
            new \Twig_SimpleFunction('formatDateTime', array($this, 'formatDateTime')),
            new \Twig_SimpleFunction('toGermanDateFromTs', array($this, 'toGermanDateFromTs')),
        );
    }

    public function formatDateTime($date)
    {
        $datetime = date_create($date->year .'-'. $date->month .'-'. $date->day);
        $formatDate['date']     = $datetime->format('%a, %d. %B %Y');
        $formatDate['fulldate'] = $datetime->format('%A, den %d. %B %Y');
        $formatDate['time']     = $datetime->format('%H:%M Uhr');
        $formatDate['ym']       = $datetime->format('Y-m');
        $formatDate['ymd']       = $datetime->format('Y-m-d');
        $formatDate['ts']       = $datetime->getTimestamp();

        return $formatDate;
    }

    public function toGermanDateFromTs($timestamp)
    {
        $datetime = \DateTime::createFromFormat('U', $timestamp);
        $datetime->setTimezone(new \DateTimeZone(\App::TIMEZONE));
        return array(
            'date' => strftime('%a. %d. %B %Y', $datetime->getTimestamp()),
            'time' => strftime('%H:%M Uhr', $datetime->getTimestamp())
        );
    }

    public function currentRoute($lang = null)
    {
        $routeInstance = \App::$slim->router()->getCurrentRoute();
        $routeParams = $routeInstance->getParams();
        $routeParams['lang'] = ($lang !== null) ? $lang : self::currentLang();
        $route = array(
            'name' => \App::$slim->router()->getCurrentRoute()->getName(),
            'params' => $routeParams
        );
        return $route;
    }

    public function currentLang()
    {
        return \App::$slim->config('lang');
    }

    public function urlGet($name, $params = array(), $getparams = array(), $appName = 'default')
    {
        //$url = \Slim\Slim::getInstance($appName)->urlFor($name, $params);
        $lang = (isset($params['lang'])) ? $params['lang'] : null;
        $url = I18nSlim::getInstance($appName)->urlFor($name, $params, $lang);
        $url = preg_replace('#^.*?(https?://)#', '\1', $url); // allow http:// routes
        if ($getparams) {
            $url .= '?' . http_build_query($getparams);
        }
        //\App::$log->info("urlGet", [$name, $url, $params, $getparams]);
        return Helper::proxySanitizeUri($url);
    }

    public function csvProperty($list, $property)
    {
        $propertylist = array();
        foreach ($list as $item) {
            if (!is_scalar($item) && array_key_exists($property, $item)) {
                $propertylist[] = $item[$property];
            }
        }
        return implode(',', array_unique($propertylist));
    }

    public function azPrefixList($list, $property)
    {
        $azList = array();
        foreach ($list as $item) {
            if (!is_scalar($item) && array_key_exists($property, $item)) {
                $currentPrefix = self::sortFirstChar($item[$property]);
                if (!array_key_exists($currentPrefix, $azList)) {
                    $azList[$currentPrefix] = array(
                        'prefix' => $currentPrefix,
                        'sublist' => array(),
                    );
                }
                $azList[$currentPrefix]['sublist'][] = $item;
            }
        }
        return $azList;
    }

    public function isValueInArray($value, $params)
    {
        $paramsArr = explode(',', $params);
        if (in_array($value, $paramsArr)) {
            return true;
        }
        return false;
    }

    protected static function sortFirstChar($string)
    {
        $firstChar = mb_substr($string, 0, 1);
        $firstChar = mb_strtoupper($firstChar);
        $firstChar = strtr($firstChar, array('Ä' => 'A', 'Ö' => 'O', 'Ü' => 'U'));
        return $firstChar;
    }

    public static function remoteInclude($uri)
    {
        $prepend = '';
        $append = '';
        if (\App::SLIM_DEBUG) {
            $prepend = "<!-- include($uri) -->\n";
            $append = "\n<!-- /include($uri) -->";
        }
        if (\App::ESI_ENABLED) {
            return $prepend . '<esi:include src="' . $uri . '" />' . $append;
        } else {
            return $prepend . file_get_contents($uri) . $append;
        }
    }

    public static function includeUrl($withUri = true)
    {
        $req = \App::$slim->request();
        $uri = $req->getUrl();

        if ($withUri) {
            $uri .= $req->getRootUri();
            $uri = preg_replace('#^https?://[^/]+#', '', $uri); //Do not force protocoll or host
        }
        return Helper::proxySanitizeUri($uri);
    }
}