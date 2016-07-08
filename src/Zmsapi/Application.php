<?php
/**
 * @package 115Mandant
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsapi;

class Application extends \BO\Slim\Application
{
    /**
     * Name of the application
     */
    const IDENTIFIER = 'Zmsapi';

    /**
     * @var String VERSION_MAJOR
     */
    const VERSION_MAJOR = '0';

    /**
     * @var String VERSION_MINOR
     */
    const VERSION_MINOR = '1';

    /**
     * @var String VERSION_PATCH
     */
    const VERSION_PATCH = '0';

    /**
     * @var Bool DEBUG
     */
    const DEBUG = false;

    /**
     * @var Bool RIGHTSCHECK_ENABLED
     */
    const RIGHTSCHECK_ENABLED = true;

    /**
     * @var String DB_DSN_READONLY
     */
    const DB_DSN_READONLY = 'mysql:dbname=zmsbo;host=127.0.0.1';

    /**
     * @var String DB_DSN_READWRITE
     */
    const DB_DSN_READWRITE = 'mysql:dbname=zmsbo;host=127.0.0.1';

    /**
     * @var String DB_USERNAME
     */
    const DB_USERNAME = 'server';

    /**
     * @var String DB_PASSWORD
     */
    const DB_PASSWORD = 'internet';

    /**
     * language preferences
     */

    public static $locale = 'de';

    public static $supportedLanguages = array(
        // Default language
        'de' => array(
            'name'    => 'Deutsch',
            'locale'  => 'de_DE.utf-8',
            'default' => true,
        )
    );

    /**
     * dldb data path
     */
    public static $data = '/data';


    /**
     * @var public static DateTimeInterface $now time to use for today (testing)
     */
    public static $now = null;

    public static function getNow()
    {
        if (self::$now instanceof \DateTimeInterface) {
            return self::$now;
        }
        return new \DateTimeImmutable();
    }
}
