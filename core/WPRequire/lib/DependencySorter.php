<?php
namespace WPRequire\lib;

use WPRequire\lib\WPPlugin;
use InvalidArgumentException;

/**
 * Sortes a set of WP plugin objects based on
 * their dependencies
 */
class DependencySorter {
    /** @var WPPlugin[string] holds pluginFile=>WPPlugin pairs */
    private $plugins = array();

    /** @var WPPlugin Holds the WPPlugin array sorted by dependency */
    private $pluginsOrdered = array();

    public function __construct($plugins) {
        // Add all plugins to the $this->plugin array, with pluginFile as index
        foreach ($plugins as $plugin) {
            if (!($plugin instanceof WPPlugin)) {
                throw new InvalidArgumentException(
                    "DependencySorter constructor expects an array of WPPlugin objects"
                );
            }
            $this->plugins[$plugin->getPluginFile()] = $plugin;
        }

        // Holds the plugins ordered by dependency, in pluginFile=>null pairs
        $pluginsOrderedKey = array();

        foreach($plugins as $i => $plugin) {
            $pluginFile = $plugin->getPluginFile();

            // If this plugins position has been accounted for
            if (isset($pluginsOrdered[$pluginFile])) continue;

            // Find this plugins cicle
            $pluginsOrderedKey = array_merge(
                $pluginsOrderedKey,
                $this->getPluginDependencyOrder($plugin)
            );
        }

        // From the pluginFiles=>null pairs, make a sorted array of WPPlugin objects
        foreach ($pluginsOrderedKey as $pluginFile => $nul) {
            $this->pluginsOrdered[] = $this->plugins[$pluginFile];
        }
    }

    /**
     * Get the plugins passed to the constructor, sorted by dependency
     * 
     * @return WPPlugin
     */
    public function getDependencySortedArray() {
        return $this->pluginsOrdered;
    }

    /**
     * Get the order that is required to load this plugin
     * 
     * @return string[] List of the plugin files for the plugins added
     */
    private function getPluginDependencyOrder(WPPlugin $plugin) {
        /*
         * To hold track of plugins added by this call
         * holdes pluginfile=>null pairs,
         * for fast lookup, the pluginfile is the "key"
         */
        $pluginsAdded = array();

        // Get the dependencies for this plugins
        $dependencies = $plugin->getWpRequire()->getRequiredPlugins();

        foreach($dependencies as $p => $version) {
            if (!isset($this->plugins[$p])) {
                throw new InvalidArgumentException(
                    "Plugin \"$p\" listed in dependency was not found in the initial list"
                );
            }
            $_pluginsAdded = $this->getPluginDependencyOrder($this->plugins[$p]);
            $pluginsAdded = array_merge($pluginsAdded, $_pluginsAdded);
        }

        $pluginFile = $plugin->getPluginFile();

        return array_merge($pluginsAdded, array($pluginFile => null));
    }
}
