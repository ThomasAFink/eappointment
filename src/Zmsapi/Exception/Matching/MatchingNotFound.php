<?php

namespace BO\Zmsapi\Exception\Matching;

/**
 *
 */
class MatchingNotFound extends \Exception
{
    protected $code = 500;

    protected $message = 'Provider does not match with given request or does not over request';
}
