<?php

class GraciousStudios_Squashpix_Model_Observer
{

    public function processSavedImage($observer)
    {

        if(!Mage::getStoreConfig('squashpix/squashpix/enabled'))    {
            return $this;
        }

        /** @var GraciousStudios_Squashpix_Model_Bunny $bunny */
        $bunny = new GraciousStudios_Squashpix_Model_Bunny();
        if ($bunny->connectClient()) {
            Mage::log(__METHOD__ . ' @ ' . __LINE__ . ' -- connected', null, 'gracious.log');
            /** @var Mage_Catalog_Model_Product_Image $image */
            $image = $observer->getEvent()->getImage();
            if ($newfile = file_get_contents($image->getNewFile())) {

                $message = [
                    'fullpath'    => $image->getNewFile(),
                    'filename'    => pathinfo($image->getNewFile(), PATHINFO_FILENAME),
                    'basename'    => pathinfo($image->getNewFile(), PATHINFO_BASENAME),
                    'dirname'     => pathinfo($image->getNewFile(), PATHINFO_DIRNAME),
                    'extension'   => pathinfo($image->getNewFile(), PATHINFO_EXTENSION),
                    'hostname'    => gethostname(),
                    'server_addr' => $_SERVER['SERVER_ADDR'],
                    'server_name' => $_SERVER['SERVER_NAME'],
                    'image'       => base64_encode($newfile),
                ];
                $bunny->channel->publish(
                    json_encode($message),
                    ['new_image'],
                    '',
                    Mage::getStoreConfig('squashpix/rabbitmq/queue')
                );
            }
        }

        return $this;
    }


}