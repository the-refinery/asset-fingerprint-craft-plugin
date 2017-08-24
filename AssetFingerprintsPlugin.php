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
      
      if($event->params['isNewAsset']) // New asset
      {
        Craft::log("[assetfingerprint] new asset: ".$asset->filename, LogLevel::Info);

        if($this->filenameDoesntHaveFingerprint($asset->filename)) 
        { 
          Craft::log("[assetfingerprint] new asset does NOT have timestamp in filename: ".$asset->filename, LogLevel::Info);
        
          // Generate new filename for new asset, using current time as fingerprint
          $updatedFilename = $this->newFingerprintFilename($asset->filename, time());
          Craft::log("[assetfingerprint] new asset filename with timestamp:".$updatedFilename, LogLevel::Info);

          // Set the new filename to the asset and update the files accordingly.
          $asset->setAttribute('filename', $updatedFilename);
          craft()->assets->renameFile($asset, $updatedFilename);
          $event->performAction = false;
        }
        else 
        {
          Craft::log("[assetfingerprint] new asset already has timestamp in filename: ".$asset->filename, LogLevel::Info, true);
        }
      }

      else // Existing asset
      {
        Craft::log("[assetfingerprint] existing asset: ".$asset->filename, LogLevel::Info);
        
        if($this->filenameDoesntHaveFingerprint($asset->filename))
        { 
          Craft::log("[assetfingerprint] existing asset does NOT have timestamp in filename: ".$asset->filename, LogLevel::Info);

          // Generate new filename for asset, using modified time as fingerprint
          $updatedFilename = $this->newFingerprintFilename($asset->filename, $asset->dateModified->getTimestamp());
          Craft::log("[assetfingerprint] existing asset filename with timestamp:".$updatedFilename, LogLevel::Info);
        
          // Set the new filename to the asset and update the files accordingly.
          $asset->setAttribute('filename', $updatedFilename);
          craft()->assets->renameFile($asset, $updatedFilename);
          $event->performAction = false;
        }
        else 
        {
          Craft::log("[assetfingerprint] existing asset already has timestamp in filename: ".$asset->filename, LogLevel::Info, true);
        }
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
    return '0.2';
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

  public function getElementRoute(BaseElementModel $element)
  {
   // Craft::log("Get ELement Route: ".$element, LogLevel::Error);
  }

  public function getResourcePath($path)
  {
    //Craft::log("getResourcePath", LogLevel::Error);
  }

  public function getAssetTableAttributeHtml(AssetFileModel $asset, $attribute)
  {
   // Craft::log($asset, LogLevel::Error);
  }

  public function modifyAssetFilename($filename)
  {
  //  Craft::log("modifyAssetFilename", LogLevel::Error);
  }
}
