<?php

namespace BO\Zmsdb\Exception;

class ClusterWithoutScopes extends \Exception
{
    protected $code = 500;

    protected $message = "No scopes found for cluster";
}
