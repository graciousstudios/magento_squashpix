<?php

use Spatie\ImageOptimizer\OptimizerChainFactory;

class GraciousStudios_Squashpix_Model_Squasher
{

    public function processContent($content)
    {
        if(!Mage::getStoreConfig('squashpix/squashpix/enabled'))    {
            return false;
        }
        Mage::log(__METHOD__ . ' @ ' . __LINE__ . ' -- $content = ' . print_r($content, true), null, 'gracious.log');
        //        $tempName = md5($content->image) . '_' . time() . '.' . $content->extension;
        $tempName = md5($content->image) . '.' . $content->extension;
        Mage::log(__METHOD__ . ' @ ' . __LINE__ . ' -- $tempName = ' . $tempName, null, 'gracious.log');
        $tempDir = Mage::getBaseDir('var') . DS . 'squashpix' . DS;
        Mage::log(__METHOD__ . ' @ ' . __LINE__ . ' -- $tempDir = ' . $tempDir, null, 'gracious.log');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777);
        }
        file_put_contents($tempDir . $tempName, base64_decode($content->image));
        $filesizeBefore = filesize($tempDir . $tempName);
        Mage::log(__METHOD__ . ' @ ' . __LINE__ . ' -- $filesizeBefore = ' . $filesizeBefore, null, 'gracious.log');
        $optimizerChain = OptimizerChainFactory::create();
        $optimizerChain->useLogger(new fool\echolog\Echolog())->optimize($tempDir . $tempName);
        clearstatcache($tempDir . $tempName);
        $filesizeAfter = filesize($tempDir . $tempName);
        if($filesizeAfter >= $filesizeBefore)   {
            unlink($tempDir . $tempName);
            return false;
        }
        Mage::log(__METHOD__ . ' @ ' . __LINE__ . ' -- $filesizeAfter = ' . $filesizeAfter, null, 'gracious.log');
        Mage::log(__METHOD__ . ' @ ' . __LINE__ . ' -- saved bytes = ' . ($filesizeBefore - $filesizeAfter), null, 'gracious.log');

        $dirname = pathinfo($content->fullpath, PATHINFO_DIRNAME);
        if(!is_dir($dirname))   {
            mkdir($dirname, 0755, true);
        }
        rename($tempDir . $tempName, $content->fullpath);

    }

}