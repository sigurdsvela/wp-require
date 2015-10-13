<?php
namespace WPRequire\lib;

use WPRequire\lib\WPPlugin;
use InvalidArgumentException;

/**
 * Sortes a set of WP plugin objects based on
 * their dependencies
 */
class DependencySorter {
    // Maps pluginBaseFile=>DependencySorterLink
    private $depLinkMap = array();

    private $plugins = array();

    private $pluginsOrdered = array();

    public function __construct($plugins) {
        foreach ($plugins as $plugin) {
            if (!($plugin instanceof WPPlugin)) {
                throw new InvalidArgumentException(
                    "DependencySorter constructor expects an array of WPPlugin objects"
                );
            }
            $this->plugins[$plugin->getPluginFile()] = $plugin;
        }

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

        foreach ($pluginsOrderedKey as $pluginFile => $nul) {
            $this->pluginsOrdered[] = $this->plugins[$pluginFile];
        }
    }

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

        // Retrive the basefile for all the deps
        $deps = array();

        foreach($dependencies as $p => $version) {
            if (!isset($this->plugins[$p])) {
                throw new InvalidArgumentException(
                    "Plugin \"$p\" listed in dependency was not found in the initial list"
                );
            }
            $_pluginsAdded = $this->getPluginDependencyOrder($this->plugins[$p]);
            $pluginsAdded = array_merge($pluginsAdded, $_pluginsAdded);
            $deps[] = $p;
        }

        $pluginFile = $plugin->getPluginFile();
        $this->depLinkMap[$pluginFile] = new DependencySorterLink($plugin);

        // Set all of the dependencies for this plugin
        foreach ($deps as $dep) {
            $this->depLinkMap[$pluginFile]->addDependency($this->depLinkMap[$dep]);
        }

        return array_merge($pluginsAdded, array($pluginFile => null));
    }

    /**
     * Get an array of the plugins passed
     * in an order the guaranties that
     * no plugin at any index requires
     * a plugin in a later position.
     * 
     * @return WPPlugin[]
     */
    public function getDependencieOrder() {

    }
}

class DependencySorterLink {
    private $dependencies = [];
    private $plugin;

    /**
     * Sort of a linked list type of thing
     * 
     * @param WPPlugin This plugin
     * @param DependencySorterLink The links containsing the plugins this one reuqire
     */
    public function __construct(WPPlugin $plugin) {
        $this->plugin = $plugin;
    }

    public function addDependency(DependencySorterLink &$link) {
        $this->dependencies[$link->getPlugin()->getPluginFile()] = &$link;
    }

    public function &getDependecies() {
        return $this->dependencies;
    }

    public function getPlugin() {
        return $this->plugin;
    }
}
