<?php

namespace BO\Zmsentities;

class Provider extends Schema\Entity
{
    const PRIMARY = 'id';

    public static $schema = "provider.json";

    public function __construct($input = null, $flags = \ArrayObject::ARRAY_AS_PROPS, $iterator_class = "ArrayIterator")
    {
        $refString = '$ref';
        $providerId = isset($input['id']) ? $input['id'] : null;
        if (isset($input[$refString]) && (!$providerId || !isset($input['source']))) {
            $providerRef = $input[$refString];
            $providerId = preg_replace('#^.*/(\d+)/$#', '$1', $providerRef);
            $input['source'] = preg_replace('#^.*provider/([^/]+)/\d+/$#', '$1', $providerRef);
        }
        $input['id'] = $providerId;
        parent::__construct($input, $flags, $iterator_class);
    }

    public function hasRequest($requestId)
    {
        return $this->getRequestList()->hasRequests($requestId);
    }

    public function getRequestList()
    {
        $requestList = new \BO\Zmsentities\Collection\RequestList();
        if (isset($this['data']['services'])) {
            foreach ($this['data']['services'] as $item) {
                $request = new Request([
                    'id' => $item['service'],
                    'source' => 'dldb',
                    'link' => isset($item['url']) ? $item['url'] : '',
                ]);
                $requestList->addEntity($request);
            }
        }
        return $requestList;
    }
}
