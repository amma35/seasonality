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

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginSeasonalityConfig extends CommonDBTM {
   const URGENCY_TYPE = 0;
   const SENSIBILITY_TYPE = 1;
   
   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
        return __('Plugin Setup', 'seasonality');
   }


   static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {
      global $CFG_GLPI;

      if ($item->getType()=='CronTask') {

         $target = $CFG_GLPI["root_doc"]."/plugins/seasonality/front/config.form.php";
         PluginSeasonalitySeasonality::configCron($target);
      }
      return true;
   }
   
	function showForm() {
      global $DB;
     
      echo "<div align='center'>";
      echo "<form method='post' action='".
         Toolbox::getItemTypeFormURL('PluginSeasonalityConfig')."'>";
      echo "<table class='tab_cadre_fixe' cellspacing='2' cellpadding='2'><tr><th colspan='2'>";
      echo __('Configuration')."</th></tr>";
      echo "<tr class='tab_bg_1'><td>";
      echo __('Definition of fields to be modified according to seasonality', 'seasonality');
      echo "</td>";
      echo "<td>";

      Dropdown::showFromArray('config', $this->getseasonalityType(), array('value' => $this->fields['config']));
      
      echo "</td>";
     
      echo "<tr><th colspan='2'>";
      echo "<input type='hidden' name='id' value='1'>";
      echo "<div align='center'>";
      echo "<input type='submit' name='update_config' value=\""._x('button', 'Post')."\" class='submit' >";
      echo "</div></th></tr>";
      echo "</table>";
      Html::closeForm();
      echo "</div>";
   }
   
   function showFormSensibility() {

      $config = new PluginSeasonalityConfig();
      $config->getFromDB(1);
      if ($config->fields['config'] == 1) {
         PluginSeasonalitySensibility::addNewSensibility(array('title' => __('Add a sensibility', 'seasonality')));
         Html::closeForm();

         $plugin_sensibility = new PluginSeasonalitySensibility();
         $result = $plugin_sensibility->find();
         echo "<div align='center'>";
         echo "<table class='tab_cadre_fixe' cellpadding='5'>";
         echo "<tr><th colspan='2'>" . $plugin_sensibility->getTypeName(2) . "</th></tr>";

         foreach ($result as $data) {
            echo "<tr>";
            echo "<td>";
            $link_period = Toolbox::getItemTypeFormURL("PluginSeasonalitySensibility");
            echo "<a class='ganttWhite' href='" . $link_period . "?id=" . $data["id"] . "'>";
            $plugin_sensibility->getFromDB($data["id"]);
            echo $plugin_sensibility->getNameID() . "</a>";
            echo "</td>";
            echo "</tr>";
         }
         echo "<tr>";
         echo "</tr>";
         echo "</table>";
         echo "</div>";
      }
   }

   function getseasonalityType(){
      return array(self::URGENCY_TYPE =>  __('Urgency'), self::SENSIBILITY_TYPE =>  __('Sensibility', 'seasonality'));
   }

   
}

?>