<?php

use Bunny\Channel;
use Bunny\Client;
use Bunny\Message;

class GraciousStudios_Squashpix_Model_Worker
{
    /** @var Bunny\Client $bunny */
    public $client;
    /** @var  Bunny\Channel $channel */
    public $channel;

    public function __construct()    {
        /** @var GraciousStudios_Squashpix_Model_Bunny $bunny */
        $bunny = Mage::getSingleton('squashpix/bunny');
        if($bunny->connectClient()) {
            $this->client = $bunny->client;
            $this->channel = $bunny->channel;
        }
    }

    public function processMessages()   {
        Mage::log(__METHOD__ . ' @ ' . __LINE__ . str_repeat('-', 20), null, 'gracious.log');
        if(!$this->client)   {
            Mage::exception('GraciousStudios_Squashpix', 'Bunny not connected, can not run cron!');
        }
        $squasher = new GraciousStudios_Squashpix_Model_Squasher;
        $this->channel->consume(
            function (Message $message, Channel $channel, Client $client) use ($squasher) {
                $content = json_decode($message->content);
                Mage::log(__METHOD__ . ' @ ' . __LINE__ . ' -- $image = ' . print_r($content,true), null, 'gracious.log');
                if($squasher->processContent($content)) {
                    $channel->ack($message);
                }
            },
            Mage::getStoreConfig('squashpix/rabbitmq/queue')
        );
        $this->client->run(50);
    }
}