<?php

namespace BO\Zmsdb;

use \BO\Zmsentities\Provider as Entity;
use \BO\Zmsentities\Collection\ProviderList as Collection;

class Provider extends Base
{
    public function readEntity($source, $providerId, $resolveReferences = 0)
    {
        $this->testSource($source);
        $query = new Query\Provider(Query\Base::SELECT);
        $query
            ->setResolveLevel($resolveReferences)
            ->addEntityMapping()
            ->addConditionProviderSource($source)
            ->addConditionProviderId($providerId);
        $provider = $this->fetchOne($query, new Entity());
        $provider = $this->readResolvedReferences($provider, $resolveReferences);
        return $provider;
    }

    public function readResolvedReferences(\BO\Zmsentities\Schema\Entity $provider, $resolveReferences)
    {
        if (0 < $resolveReferences) {
            $provider = $this->readWithRequestRelation($provider, $resolveReferences - 1);
        }
        return $provider;
    }

    public function readWithRequestRelation(\BO\Zmsentities\Schema\Entity $provider, $resolveReferences)
    {
        if ($provider->hasId()) {
            $requestRelationList = (new RequestRelation)->readListByProviderId($provider->getId(), $resolveReferences);
            $provider->requestrelation = $requestRelationList->toRequestRelation();
        }
        return $provider;
    }

    /**
     * @SuppressWarnings(Param)
     *
     */
    protected function readCollection($query, $resolveReferences)
    {
        $providerList = new Collection();
        $statement = $this->fetchStatement($query);
        while ($providerData = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $entity = new Entity($query->postProcessJoins($providerData));
            $entity = $this->readResolvedReferences($entity, $resolveReferences);
            $providerList->addEntity($entity);
        }
        return $providerList;
    }

    public function readListByRequest($source, $requestIdCsv, $resolveReferences = 0)
    {
        $this->testSource($source);
        $query = new Query\Provider(Query\Base::SELECT);
        $query->setResolveLevel($resolveReferences);
        $query->addEntityMapping();
        $query->addConditionProviderSource($source);
        $query->addConditionRequestCsv($requestIdCsv);
        return $this->readCollection($query, $resolveReferences);
    }

    public function readListBySource($source, $resolveReferences = 0, $isAssigned = null)
    {
        $this->testSource($source);
        $query = new Query\Provider(Query\Base::SELECT);
        $query->setResolveLevel($resolveReferences);
        $query->addEntityMapping();
        $query->addConditionProviderSource($source);
        if (null !== $isAssigned) {
            $query->addConditionIsAssigned($isAssigned);
        }
        return $this->readCollection($query, $resolveReferences);
    }

    public function writeEntity(Entity $entity)
    {
        $contact =  $entity->getContact();
        if (! $contact->count()) {
            throw new Exception\Provider\ProviderContactMissed();
        }
        $query = new Query\Provider(Query\Base::INSERT);
        $query->addValues([
            'source' => $entity->getSource(),
            'id' => $entity->getId(),
            'name' => $entity->getName(),
            'contact__city' => ($contact->hasProperty('city')) ? $contact->getProperty('city') : '',
            'contact__country' => ($contact->hasProperty('country')) ? $contact->getProperty('country') : '',
            'contact__lat' => ($contact->hasProperty('lat')) ? $contact->getProperty('lat') : 0,
            'contact__lon' => ($contact->hasProperty('lon')) ? $contact->getProperty('lon') : 0,
            'contact__postalCode' => $contact->hasProperty('postalCode')
                ? intval($contact->getProperty('postalCode'))
                : '',
            'contact__region' => $contact->hasProperty('region') ? $contact->getProperty('region') : '',
            'contact__street' => $contact->hasProperty('street') ? $contact->getProperty('street') : '',
            'contact__streetNumber' => $contact->hasProperty('streetNumber')
                ? $contact->getProperty('streetNumber')
                : '-',
            'link' =>  ($entity->getLink()) ? $entity->getLink() : '',
            'data' => json_encode($entity)
        ]);
        $this->writeItem($query);
        $lastInsertId = $this->getWriter()->lastInsertId();
        return $this->readEntity($entity->getSource(), $lastInsertId);
    }

    public function writeListBySource(\BO\Zmsentities\Source $source)
    {
        foreach ($source->getProviderList() as $provider) {
            $this->writeEntity($provider);
        }
        return $this->readListBySource($source->getSource());
    }

    public function writeImportList($providerList, $source = 'dldb')
    {
        foreach ($providerList as $provider) {
            $this->writeImportEntity($provider, $source);
        }
        return $this->readListBySource($source);
    }

    public function writeImportEntity($provider, $source = 'dldb')
    {
        if ($provider['address']['postal_code']) {
            $query = new Query\Provider(Query\Base::REPLACE);
            $query->addValues([
                'source' => $source,
                'id' => $provider['id'],
                'name' => $provider['name'],
                'contact__city' => $provider['address']['city'],
                'contact__country' => $provider['address']['city'],
                'contact__lat' => $provider['geo']['lat'],
                'contact__lon' => $provider['geo']['lon'],
                'contact__postalCode' => intval($provider['address']['postal_code']),
                'contact__region' => $provider['address']['city'],
                'contact__street' => $provider['address']['street'],
                'contact__streetNumber' => $provider['address']['house_number'],
                'link' => ('dldb' == $source)
                    ? 'https://service.berlin.de/standort/'. $provider['id'] .'/'
                    : ((isset($provider['link'])) ? $provider['link'] : ''),
                'data' => json_encode($provider)
            ]);
            $this->writeItem($query);
            return $this->readEntity($source, $provider['id']);
        }
    }

    public function writeDeleteListBySource($source)
    {
        $query = new Query\Provider(Query\Base::DELETE);
        $query->addConditionProviderSource($source);
        return $this->deleteItem($query);
    }

    protected function testSource($source)
    {
        if (! (new Source())->readEntity($source)) {
            throw new Exception\Source\UnknownDataSource();
        }
    }
}
