<?php

namespace BO\Zmsapi\Response;

use \BO\Zmsdb\Connection\Select;

/**
 * example class to generate a response
 */
class Message implements \JsonSerializable
{
    /**
     * @var \BO\Zmsentities\Metaresult $meta
     */
    public $meta = null;

    /**
     * @var Mixed $data
     */
    public $data = null;

    /**
     * @var Mixed $data
     */
    public $statuscode = 200;

    /**
     * @var \Psr\Http\Message\RequestInterface $request;
     *
     */
    protected $request = null;

    protected function __construct(\Psr\Http\Message\RequestInterface $request)
    {
        $this->request = $request;
        $this->meta = new \BO\Zmsentities\Metaresult();
        $this->meta->error = false;
        $this->meta->exception = null;
        $this->setUpdatedMetaData();
    }


    public static function create(\Psr\Http\Message\RequestInterface $request)
    {
        $message = new self($request);
        return $message;
    }

    public function hasData()
    {
        return (
            ($this->data instanceof \BO\Zmsentities\Schema\Entity && $this->data->hasId())
            || ($this->data instanceof \BO\Zmsentities\Collection\Base && count($this->data))
            || (is_array($this->data) && count($this->data))
        );
    }

    /**
     * Update meta-data
     * check for data in response
     *
     */
    public function setUpdatedMetaData()
    {
        $this->meta->generated = date('c');
        $this->meta->server = \App::IDENTIFIER;
        if ($this->data !== null && $this->statuscode == 200 && !$this->hasData()) {
            $this->statuscode = 404;
            $this->meta->error = true;
            $this->meta->message = 'Not found';
        }
        return $this;
    }

    public function getStatuscode()
    {
        return $this->statuscode;
    }

    public function jsonSerialize()
    {
        $schema = $this->request->getUri()->getScheme();
        $schema .= '://';
        $schema .= $this->request->getUri()->getHost();
        $schema .= \App::$slim->urlFor('index');
        $message = [
            '$schema' => $schema,
            "meta" => $this->meta,
            "data" => $this->data,
        ];
        if (\App::DEBUG) {
            $message['profiler'] = $this->getProfilerData();
        }
        return $message;
    }

    protected function getProfilerData()
    {
        $profiles = [];
        if (Select::hasWriteConnection()) {
            $profiles += Select::getWriteConnection()->getProfiler()->getProfiles();
        }
        if (Select::hasReadConnection()) {
            $profiles += Select::getReadConnection()->getProfiler()->getProfiles();
        }
        $filtered = array();
        foreach ($profiles as $profile) {
            if ($profile['function'] == 'perform') {
                unset($profile['trace']);
                $profile['statement'] = preg_replace('#\s+#', ' ', $profile['statement']);
                $filtered[] = $profile;
            }
        }
        return $filtered;
    }
}
