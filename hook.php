<?php
/*
 -------------------------------------------------------------------------
 Seasonality plugin for GLPI
 Copyright (C) 2003-2015 by the Seasonality Development Team.

 https://github.com/InfotelGLPI/seasonality
 -------------------------------------------------------------------------

 LICENSE

 This file is part of Seasonality.

 Seasonality is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 Seasonality is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Seasonality. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

function plugin_seasonality_install() {
   global $DB;

   include_once (GLPI_ROOT . "/plugins/seasonality/inc/profile.class.php");
   include_once (GLPI_ROOT."/plugins/seasonality/inc/config.class.php");

   // Table sql creation
   if (!TableExists("glpi_plugin_seasonality_seasonalities")) {
      $DB->runFile(GLPI_ROOT . "/plugins/seasonality/install/sql/empty-1.2.0.sql");
   }elseif(!TableExists('glpi_plugin_seasonality_configs')){
      $query = "CREATE TABLE `glpi_plugin_seasonality_configs` (
         `id` int(11) NOT NULL auto_increment,
         `config` tinyint(1) NOT NULL default '0',
         PRIMARY KEY  (`id`)
      ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
      $DB->query($query);
      $query = "INSERT INTO `glpi_plugin_seasonality_configs` (`id`,`config`) VALUES ('1', '0');";
      $DB->query($query);
      
      $query = "DROP TABLE IF EXISTS `glpi_plugin_seasonality_sensibilities`;";
      $DB->query($query);
      $query = "CREATE TABLE `glpi_plugin_seasonality_sensibilities` (
                  `id` int(11) NOT NULL auto_increment,
                  `name` varchar(255) DEFAULT NULL,
                  `matrix` text,
                  PRIMARY KEY  (`id`)
               ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
      $DB->query($query);

   }

   PluginSeasonalityProfile::createFirstAccess($_SESSION['glpiactiveprofile']['id']);
   return true;
}

// Uninstall process for plugin : need to return true if succeeded
function plugin_seasonality_uninstall() {
   global $DB;

   // Plugin tables deletion
   $tables = array("glpi_plugin_seasonality_seasonalities", 
                   "glpi_plugin_seasonality_items",
                   "glpi_plugin_seasonality_configs",
                   "glpi_plugin_seasonality_sensibilities");

   foreach ($tables as $table)
      $DB->query("DROP TABLE IF EXISTS `$table`;");
   
   return true;
}

function plugin_seasonality_postinit() {
   global $PLUGIN_HOOKS;
}

function plugin_seasonality_getAddSearchOptions($itemtype) {
   $tab = array();
   
   if ($itemtype == 'ITILCategory') {
      $item = new PluginSeasonalityItem();
      $tab = $item->getAddSearchOptions();
   }
   
   return $tab;
}

function plugin_seasonality_MassiveActions($type) {
   
   switch($type){
      case 'ITILCategory':
         $item = new PluginSeasonalityItem();
         $output = $item->massiveActions($type);
         return $output;
   }
}

function plugin_datainjection_populate_seasonality() {
   global $INJECTABLE_TYPES;
   
   $INJECTABLE_TYPES['PluginSeasonalityItemInjection'] = 'seasonality';
   $INJECTABLE_TYPES['PluginSeasonalitySeasonalityInjection'] = 'seasonality';
}

?>