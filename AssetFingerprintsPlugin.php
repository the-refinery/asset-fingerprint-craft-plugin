<?php

namespace Craft;

class AssetFingerprintsPlugin extends BasePlugin
{
  public function init()
  {
    parent::init();

    craft()->on('assets.onSaveAsset', function(Event $event)
    {
      $asset = $event->params['asset'];
      Craft::log("[assetfingerprint] Atttempting to fingerprint: ".$asset->filename, LogLevel::Info, true);

      $assetStateIndicator = "existing";
      $assetTimestamp = $asset->dateModified->getTimestamp();
      
      if($event->params['isNewAsset']) // New asset
      {
        $assetStateIndicator = "new";
        $assetTimestamp = time(); 
      }

      Craft::log("[assetfingerprint] ".$assetStateIndicator." asset: ".$asset->filename, LogLevel::Info);

      if($this->filenameHasFingerprint($asset->filename)) 
      {
        Craft::log("[assetfingerprint] ".$assetStateIndicator." asset already has timestamp in filename: ".$asset->filename, LogLevel::Info, true);
      }
      else 
      {
        Craft::log("[assetfingerprint] ".$assetStateIndicator." asset does NOT have timestamp in filename: ".$asset->filename, LogLevel::Info);

        // Generate new filename for new asset, using current time as fingerprint
        $updatedFilename = $this->newFingerprintFilename($asset->filename, $assetTimestamp);
        Craft::log("[assetfingerprint] ".$assetStateIndicator." asset filename with timestamp:".$updatedFilename, LogLevel::Info);

        // Set the new filename to the asset and update the files accordingly.
        $asset->setAttribute('filename', $updatedFilename);
        craft()->assets->renameFile($asset, $updatedFilename);
        $event->performAction = false;
      }
    });
  }

  private function filenameHasFingerprint($filename)
  {
    // Check for timestamp in the filename.
    // E.g. logo.1234567890.png vs. logo.png
    return preg_match('/\.\d{10}\..{2,4}$/', $filename) != 0;
  }

  private function filenameDoesntHaveFingerprint($filename)
  {
    return $this->filenameHasFingerprint($filename) == false;
  }

  private function newFingerprintFilename($filename, $timestamp)
  {
    $assetPathInfo = pathinfo($filename);
    return join(".", [$assetPathInfo['filename'], $timestamp, $assetPathInfo['extension']]);
  }

  function getName()
  {
    return Craft::t('Asset Fingerprints');
  }

  function getVersion()
  {
    return '0.4';
  }

  function getDeveloper()
  {
    return 'The Refinery, LLC';
  }

  function getDeveloperUrl()
  {
    return 'http://the-refinery.io/';
  }

  function getDocumentationUrl()
  {
    return 'https://github.com/the-refinery/asset-fingerprint-craft-plugin/blob/master/README.md';
  }
}

