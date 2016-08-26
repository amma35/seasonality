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

include ('../../../inc/includes.php');

$plugin = new Plugin();


if ($plugin->isActivated("seasonality")) {
   if (Session::haveRight("plugin_seasonality", UPDATE)) {
      $config = new PluginSeasonalityConfig();

      if (isset($_POST["update_config"])) {
         Session::checkRight("config", UPDATE);
         $config->update($_POST);
         Html::back();
         
      } else {
         Html::header(PluginSeasonalitySeasonality::getTypeName(1), '', "helpdesk", "pluginseasonalityseasonality", "config");
         $config->GetFromDB(1);
         $config->showForm();
         $config->showFormSensibility();
         Html::footer();
      }
      
   } else {
       Html::header(PluginSeasonalitySeasonality::getTypeName(1), '', "helpdesk", "pluginseasonalityseasonality", "config");
      echo "<div align='center'><br><br>";
      echo "<img src=\"".$CFG_GLPI["root_doc"]."/pics/warning.png\" alt='warning'><br><br>";
      echo "<b>".__("You don't have permission to perform this action.")."</b></div>";
      Html::footer();
   }
   
} else {
      
   Html::header(PluginSeasonalitySeasonality::getTypeName(1), '', "helpdesk", "pluginseasonalityseasonality", "config");
   echo "<div align='center'><br><br><img src=\"".$CFG_GLPI["root_doc"]."/pics/warning.png\" alt=\"warning\"><br><br>";
   echo "<b>Please activate the plugin</b></div>";
   Html::footer();
}

?>