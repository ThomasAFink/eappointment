<?php

namespace BO\Zmsentities\Schema;

use \Exception;

class Loader
{
    public static function asArray($schemaFilename)
    {
        $jsonString = self::asJson($schemaFilename);
        $array = json_decode($jsonString, true);
        if (null === $array && $jsonString) {
            $json_error = json_last_error();
            $json_error_list = array(
                JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded',
                JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
                JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
                JSON_ERROR_STATE_MISMATCH => 'Underflow or the modes mismatch',
                JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON',
                JSON_ERROR_NONE => 'No errors',
            );
            throw new Exception("Could not parse JSON File $schemaFilename: ".$json_error_list[$json_error]);
        }
        return new \ArrayObject($array);
    }
    public static function asJson($schemaFilename)
    {
        if (!$schemaFilename) {
            throw new Exception("Missing JSON-Schema file");
        }
        $directory = self::getSchemaPath();
        $filename = $directory . DIRECTORY_SEPARATOR . $schemaFilename;
        return file_get_contents($filename);
    }

    public static function getSchemaPath()
    {
        $pathTrace = [
            __DIR__,
            '..',
            '..',
            '..',
            'schema'
        ];
        return realpath(implode(DIRECTORY_SEPARATOR, $pathTrace));
    }
}
