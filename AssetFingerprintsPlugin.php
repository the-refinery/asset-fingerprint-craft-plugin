<?php

namespace Craft;

class AssetFingerprintsPlugin extends BasePlugin
{
  public function init()
  {
    parent::init();
    //craft()->on('assets.onBeforeSaveAsset', function(Event $event) {
    craft()->on('assets.onSaveAsset', function(Event $event) {
      // Only rename if it’s a new asset being saved
      if($event->params['isNewAsset'])
      {
        $asset = $event->params['asset'];
        Craft::log("assets.onsaveasset new asset: ".$asset->filename, LogLevel::Error);
        if(preg_match('/\.\d{10}\..{2,4}$/', $asset->filename)==0) { 
          Craft::log("assets.onsaveasset [new asset] does NOT have timestamp in filename: ".$asset->filename, LogLevel::Error);
          $assetPathInfo = pathinfo($asset->filename);
          Craft::log("filename: ".$assetPathInfo['filename'], LogLevel::Error);
          $updatedFile = join(".", [$assetPathInfo['filename'], time(), $assetPathInfo['extension']]);
          Craft::log("Updated filename: ".$updatedFile, LogLevel::Error);
          $asset->setAttribute('filename', $updatedFile);
          Craft::log("asset filename after adjustment: ".$asset->filename, LogLevel::Error);
          craft()->assets->renameFile($asset, $updatedFile);
          craft()->assets->storeFile($asset);
          $event->performAction = false;
          /*
          $asset = $event->params['asset'];
          Craft::log("assets.onbeforesaveasset is new asset: ".$asset->filename, LogLevel::Error);
          $assetPathInfo = pathinfo($asset->filename);
          Craft::log("filename: ".$assetPathInfo['filename'], LogLevel::Error);
          $updatedFile = join(".", [$assetPathInfo['filename'], time(), $assetPathInfo['extension']]);
          Craft::log("Updated filename: ".$updatedFile, LogLevel::Error);
          $asset->setAttribute('filename', $updatedFile);
          Craft::log("asset filename after adjustment: ".$asset->filename, LogLevel::Error);
          //craft()->assets->renameFile($asset, $updatedFile);
          //craft()->assets->storeFile($asset);
          $event->performAction = true;
           */
        }
        else {
          Craft::log("assets.onsaveasset [new asset] has timestamp already in filename: ".$asset->filename, LogLevel::Error);
        }
      } else {
        $asset = $event->params['asset'];
        Craft::log("assets.onsaveasset NOT new asset: ".$asset->filename, LogLevel::Error);
        
        Craft::log($asset->dateModified->getTimestamp(), LogLevel::Error);
        if(preg_match('/\.\d{10}\..{2,4}$/', $asset->filename)==0) { 
          Craft::log("assets.onsaveasset [existing asset] filename does NOT have timestamp set!", LogLevel::Error);
          $assetPathInfo = pathinfo($asset->filename);
          Craft::log("filename: ".$assetPathInfo['filename'], LogLevel::Error);
          $updatedFile = join(".", [$assetPathInfo['filename'], $asset->dateModified->getTimestamp(), $assetPathInfo['extension']]);
          Craft::log("Updated filename: ".$updatedFile, LogLevel::Error);
          $asset->setAttribute('filename', $updatedFile);
          Craft::log("asset filename after adjustment: ".$asset->filename, LogLevel::Error);
          //craft()->assets->renameFile($asset, $updatedFile);
          craft()->assets->renameFile($asset, $updatedFile);
          craft()->assets->storeFile($asset);
          $event->performAction = false;
          //$a = craft()->assets->storeFile($asset);
          //Craft::log("a: ".$a, LogLevel::Error);
          //return $asset;
        }
        else {
          Craft::log("assets.onsaveasset [existing asset] has timestamp already in filename: ".$asset->filename, LogLevel::Error);
        }
       
      }

      //Craft::log("Returning because end of mfunction", LogLevel::Error);

    });
  }

  function getName()
  {
    return Craft::t('Asset Fingerprints');
  }

  function getVersion()
  {
    return '0.1';
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
