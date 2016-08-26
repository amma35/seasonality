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

$sensibility = new PluginSeasonalitySensibility();
if (isset($_POST["add"])) {
   $sensibility->check(-1, UPDATE);
   $sensibility->add($_POST);
   if ($_SESSION['glpibackcreated']) {
      Html::redirect($sensibility->getFormURL() . "?id=" . $newID);
   }
   Html::back();
} else if (isset($_POST["update"])) {
   $sensibility->check($_POST["id"], UPDATE);
   $sensibility->update($_POST);
   Html::back();
} else if (isset($_POST["delete"])) {
   $sensibility_id = $_POST["id"];
   $sensibility->check($_POST["id"], UPDATE);
   $sensibility->delete($_POST, 1);
   Html::redirect(Toolbox::getItemTypeFormURL('Sensibility')."?id=".$sensibility_id);
}else {
   Html::header(PluginSeasonalitySensibility::getTypeName(2), '', "helpdesk", "pluginseasonalityseasonality", "sensibility");
   $sensibility->display($_GET);
   Html::footer();
}
?>