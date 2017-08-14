<?php
use Bunny\Client;

class GraciousStudios_Squashpix_Model_Bunny
{

    /** @var  Bunny\Client $bunny */
    public $client;
    /** @var  Bunny\Channel $channel */
    public $channel;

    public function __construct()    {
        $this->setClient();

    }

    public function setClient()
    {

        if (!$this->client) {
            $connection = [
                'host'     => Mage::getStoreConfig('squashpix/rabbitmq/host'),
                'vhost'    => Mage::getStoreConfig('squashpix/rabbitmq/vhost'),
                'user'     => Mage::getStoreConfig('squashpix/rabbitmq/username'),
                'password' => Mage::getStoreConfig('squashpix/rabbitmq/password'),
            ];
            Mage::log(__METHOD__ . ' @ ' . __LINE__ . ' -- $connection = ' . print_r($connection, true), null, 'gracious.log');
            $this->client = new Client($connection);
        }
    }

    public function connectClient()
    {
        Mage::log(__METHOD__ . ' @ ' . __LINE__, null, 'gracious.log');
        if ($this->client && $this->client->isConnected()) {
            Mage::log(__METHOD__ . ' @ ' . __LINE__ . ' -- isConnected', null, 'gracious.log');
            return true;
        }
        if (!$this->client || ($this->client && !$this->client->isConnected())) {
            Mage::log(__METHOD__ . ' @ ' . __LINE__ . ' -- Not connected, trying to connect', null, 'gracious.log');
            $this->client->connect();
            $this->channel = $this->client->channel();
            $this->channel->queueDeclare(Mage::getStoreConfig('squashpix/rabbitmq/queue'));
            if ($this->client->isConnected()) {
                Mage::log(__METHOD__ . ' @ ' . __LINE__ . ' -- Is now connected', null, 'gracious.log');
                return true;
            }
        }
        Mage::log(__METHOD__ . ' @ ' . __LINE__ . ' -- Failed to connect', null, 'gracious.log');
        return false;
    }


}
