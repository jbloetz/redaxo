<?php

class rex_addon_manager extends rex_package_manager
{
  /**
   * Constructor
   *
   * @param rex_addon $addon Addon
   */
  protected function __construct(rex_addon $addon)
  {
    parent::__construct($addon, 'addon_');
  }

  /* (non-PHPdoc)
   * @see rex_package_manager::install()
   */
  public function install($installDump = true)
  {
    $installed = $this->package->isInstalled();
    $return = parent::install($installDump);

    if (!$installed && $return === true) {
      foreach ($this->package->getSystemPlugins() as $plugin) {
        $manager = rex_plugin_manager::factory($plugin);
        if ($manager->install() === true) {
          $manager->activate();
        }
      }
    }

    return $return;
  }

  /* (non-PHPdoc)
   * @see rex_package_manager::checkDependencies()
   */
  public function checkDependencies()
  {
    $i18nPrefix = 'addon_dependencies_error_';
    $state = array();

    foreach (rex_package::getAvailablePackages() as $package) {
      if ($package->getAddon() === $this->package)
        continue;

      $requirements = $package->getProperty('requires', array());
      if (isset($requirements['addons'][$this->package->getName()])) {
        $state[] = rex_i18n::msg($i18nPrefix . $package->getType(), $package->getAddon()->getName(), $package->getName());
      }
    }

    return empty($state) ? true : implode('<br />', $state);
  }

  /* (non-PHPdoc)
   * @see rex_package_manager::addToPackageOrder()
   */
  protected function addToPackageOrder()
  {
    parent::addToPackageOrder();

    $plugins = new SplObjectStorage;

    // create the managers for all available plugins
    foreach ($this->package->getAvailablePlugins() as $plugin) {
      $plugins[$plugin] = rex_plugin_manager::factory($plugin);
    }

    // mark all plugins whose requirements are not met
    // to consider dependencies among each other, iterate over all plugins until no plugin was marked in a round
    $deactivate = array();
    $finished = false;
    while (!$finished && count($plugins) > 0) {
      $finished = true;
      foreach ($plugins as $plugin) {
        $pluginManager = $plugins[$plugin];
        $return = $pluginManager->checkRequirements();
        if (is_string($return) && !empty($return)) {
          $plugin->setProperty('status', false);
          $deactivate[] = $pluginManager;
          $finished = false;
          unset($plugins[$plugin]);
        }
      }
    }
    // deactivate all marked plugins
    foreach ($deactivate as $pluginManager) {
      $pluginManager->deactivate();
    }

    // add all other plugins to package order
    // (consider dependencies among each other, don't add in alphabetical order)
    foreach ($plugins as $plugin) {
      $plugin->setProperty('status', false);
    }
    while (count($plugins) > 0) {
      foreach ($plugins as $plugin) {
        $pluginManager = $plugins[$plugin];
        if ($pluginManager->checkRequirements() === true) {
          $plugin->setProperty('status', true);
          $pluginManager->addToPackageOrder();
          unset($plugins[$plugin]);
        }
      }
    }
  }

  /* (non-PHPdoc)
   * @see rex_package_manager::removeFromPackageOrder()
   */
  protected function removeFromPackageOrder()
  {
    parent::removeFromPackageOrder();

    foreach ($this->package->getRegisteredPlugins() as $plugin) {
      $pluginManager = rex_plugin_manager::factory($plugin);
      $pluginManager->removeFromPackageOrder($plugin);
    }
  }
}
