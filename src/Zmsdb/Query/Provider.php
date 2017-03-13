<?php

namespace BO\Zmsdb\Query;

class Provider extends Base
{
    const TABLE = 'provider';

    public static function getQuerySlots()
    {
        return 'SELECT
            `request__id`,
            `slots`
        FROM `request_provider`
        WHERE
            `provider__id` = :provider_id
            ';
    }

    public function getEntityMapping()
    {
        $mapping = [
            'contact__city' => 'provider.contact__city',
            'contact__country' => self::expression('"Germany"'),
            'contact__name' => 'provider.name',
            'contact__postalCode' => 'provider.contact__postalCode',
            'contact__region' => 'provider.contact__region',
            'contact__street' => 'provider.contact__street',
            'contact__streetNumber' => 'provider.contact__streetNumber',
            'source' => 'provider.source',
            'id' => 'provider.id',
            'link' => 'provider.link',
            'name' => 'provider.name',
        ];
        if ($this->getResolveLevel() > 0) {
            $mapping['data'] = 'provider.data';
        }
        return $mapping;
    }

    public function addConditionIsAssigned($isAssigned)
    {
        $this->query->leftJoin(
            new Alias(Scope::TABLE, 'assignedscope'),
            'provider.id',
            '=',
            'assignedscope.InfoDienstleisterID'
        );
        if (true === $isAssigned) {
            $this->query->where('assignedscope.InfoDienstleisterID', 'IS NOT', null);
        } elseif (false === $isAssigned) {
            $this->query->where('assignedscope.InfoDienstleisterID', 'IS', null);
        }
        return $this;
    }

    public function addConditionProviderId($providerId)
    {
        $this->query->where('provider.id', '=', $providerId);
        return $this;
    }

    public function addConditionProviderSource($source)
    {
        $this->query->where('provider.source', '=', $source);
        return $this;
    }

    public function addConditionRequestCsv($requestIdCsv)
    {
        $requestIdList = explode(',', $requestIdCsv);
        $this->query->leftJoin(
            new Alias("request_provider", 'xprovider'),
            'provider.id',
            '=',
            'xprovider.provider__id'
        );
        $this->query->where('xprovider.request__id', 'IN', $requestIdList);
    }

    public function postProcess($data)
    {
        if (isset($data['data']) && $data['data']) {
            $data['data'] = json_decode($data['data']);
        }
        return $data;
    }
}
