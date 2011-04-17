<?php

/**
 * Klasse zum prüfen ob Addons installiert/aktiviert sind
 * @package redaxo5
 * @version svn:$Id$
 */

class rex_ooAddon extends rex_package
{
  /**
   * Erstellt eine rex_ooAddon instanz
   *
   * @param string $addon Name des Addons
   */
  protected function __construct($addon)
  {
    parent::__construct($addon);
  }

  /**
   * Prüft, ob ein System-Addon vorliegt
   *
   * @param string $addon Name des Addons
   *
   * @return boolean TRUE, wenn es sich um ein System-Addon handelt, sonst FALSE
   */
  static public function isSystemAddon($addon)
  {
    global $REX;
    return in_array($addon, $REX['SYSTEM_PACKAGES']);
  }

  /**
   * Gibt ein Array von verfügbaren Addons zurück.
   *
   * @return array Array der verfügbaren Addons
   */
  static public function getAvailableAddons()
  {
    $avail = array();
    foreach(rex_ooAddon::getRegisteredAddons() as $addonName)
    {
      if(rex_ooAddon::isAvailable($addonName))
        $avail[] = $addonName;
    }

    return $avail;
  }

  /**
   * Gibt ein Array aller registrierten Addons zurück.
   * Ein Addon ist registriert, wenn es dem System bekannt ist (addons.inc.php).
   *
   * @return array Array aller registrierten Addons
   */
  static public function getRegisteredAddons()
  {
    global $REX;

    $addons = array();
    if(isset($REX['ADDON']) && is_array($REX['ADDON']) &&
       isset($REX['ADDON']['install']) && is_array($REX['ADDON']['install']))
    {
      $addons = array_keys($REX['ADDON']['install']);
    }

    return $addons;
  }
}