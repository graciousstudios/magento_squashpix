<?php

class GraciousStudios_Squashpix_Model_Catalog_Product_Image extends Mage_Catalog_Model_Product_Image
{

    /**
     * @return Mage_Catalog_Model_Product_Image
     */
    public function saveFile()
    {

        $filename = $this->getNewFile();
        $this->getImageProcessor()->save($filename);
        Mage::helper('core/file_storage_database')->saveFile($filename);
        Mage::dispatchEvent('squashpix_save_image_after', ['filename' => $filename, 'image' => $this]);
        return $this;
    }

}
		