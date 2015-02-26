<?php
/**
 * @package Dldb
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Dldb\Indexer;

use BO\Dldb\FileAccess;

/**
  * Index DLDB data into ElasticSearch
  *
  */
class ElasticSearch
{
    const ES_INDEX_PREFIX = 'dldb-';
    const ES_INDEX_DATE = 'Ymd-H';

    /**
      * Access to DLDB files
      *
      * @var FileAccess $dldb
      */
    protected $dldb;

    /**
      * hostname for ES
      *
      * @var String $host
      */
    protected $host = 'localhost';

    /**
      * port for ES
      *
      * @var String $port
      */
    protected $port = '9200';

    /**
      * transport method for ES
      *
      * @var String $transport
      */
    protected $transport = 'Http';

    /**
     * The client used to talk to elastic search.
     *
     * @var \Elastica\Client
     */
    protected $connection;

    /**
      * Index from elastic search
      *
      * @var \Elastica\Index $index
      */
    protected $index;

    public function __construct($locationsFile, $servicesFile)
    {
        $this->dldb = new FileAccess($locationsFile, $servicesFile);
    }

    /**
     * @return self
     */
    public function run()
    {
        $this->indexServices();
        $this->indexLocations();
        return $this;
    }

    /**
     * @return self
     */
    protected function indexServices()
    {
        $esType = $this->getIndex()->getType('service');
        $docs = array();
        foreach ($this->dldb->fetchServiceList() as $service) {
            $docs[] = new \Elastica\Document($service['id'], $service);
        }
        $esType->addDocuments($docs);
        return $this;
    }

    /**
     * @return self
     */
    protected function indexLocations()
    {
        $esType = $this->getIndex()->getType('location');
        $docs = array();
        foreach ($this->dldb->fetchLocationList() as $location) {
            $docs[] = new \Elastica\Document($location['id'], $location);
        }
        $esType->addDocuments($docs);
        return $this;
    }

    /**
     * @return \Elastica\Client
     */
    protected function getConnection()
    {
        if (null === $this->connection) {
            $this->connection = new \Elastica\Client(
                array(
                    'host' => $this->host,
                    'port' => $this->port,
                    'transport' => $this->transport
                )
            );
        }
        return $this->connection;
    }

    /**
     * @return \Elastica\Index
     */
    protected function getIndex($indexname = null)
    {
        if (null === $this->index) {
            $connection = $this->getConnection();
            if (null === $indexname) {
                $this->index = $connection->getIndex(self::ES_INDEX_PREFIX . date(self::ES_INDEX_DATE));
                if (!$this->index->exists()) {
                    $indexSettings = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'ElasticSearch_Index.json');
                    $indexSettings = json_decode($indexSettings, true);
                    $this->index->create($indexSettings);
                }
            } else {
                $this->index = $connection->getIndex($indexname);
            }
        }
        return $this->index;
    }

    /**
     * @return self
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return self
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return self
     */
    public function setTransport($transport)
    {
        $this->transport = $transport;
        return $this;
    }

    /**
     * @return self
     */
    public function setAlias($alias)
    {
        $this->getIndex()->addAlias($alias, true);
        return $this;
    }
}
