<?php

/*
  -------------------------------------------------------------------------
  Seasonalities plugin for GLPI
  Copyright (C) 2003-2012 by the Seasonalities Development Team.

  https://github.com/InfotelGLPI/seasonality
  -------------------------------------------------------------------------

  LICENSE

  This file is part of Seasonality.

  Seasonalities is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  Seasonalities is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with Seasonalities. If not, see <http://www.gnu.org/licenses/>.
  --------------------------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginSeasonalitySensibility extends CommonDBTM {

   static $rightname = 'plugin_seasonality';
   // From CommonDBTM
   public $dohistory = true;

   static function getTypeName($nb = 0) {
      return _n('Sensibility', 'Sensibilities', $nb, 'seasonality');
   }

   function getSearchOptions() {
      $tab = parent::getSearchOptions();

      return $tab;
   }

   function showForm($ID, $options = array("")) {
      global $CFG_GLPI, $DB;

      if ($ID > 0) {
         $this->check($ID, READ);
      } else {
         // Create item
         $this->check(-1, CREATE);  
      }
      $this->initForm($ID, $options);
      $this->showFormHeader($options);

      echo "<tr class='tab_bg_1'>";
      echo "<td>" . __('Name') . "&nbsp;<span class='red'>*</span></td>";
      echo "<td>";
      Html::autocompletionTextField($this, "name", array('value' => $this->fields["name"]));
      echo "</td>";
      echo "<td colspan='2'></td></tr>";
      
      echo "<tr class='tab_bg_1'><td colspan='3'> ";
      echo __('Matrix', 'seasonality'). " : &nbsp;<span class='red'>*</span>";
      echo "</td></tr>";
      echo "<tr><td colspan='3'> ";
      if(!is_array($this->fields['matrix'])){
         $this->fields['matrix'] = json_decode($this->fields['matrix'], true);
      }
      self::showMatrix($this->fields['matrix'], true);
      echo "</td></tr>";
      $this->showFormButtons($options);

      return true;
   }

   static function addNewSensibility($options = array()) {
 
      $addButton = "";

      if (Session::haveRight('plugin_seasonality', UPDATE)) {
         $rand = mt_rand();

         $addButton = "<form method='post' name='sensibility_form'.$rand.'' id='sensibility_form" . $rand . "'
               action='" . Toolbox::getItemTypeFormURL('PluginSeasonalitySensibility') . "'>
               <input type='hidden' name='sensibility_id' value='sensibility'>
               <input type='hidden' name='id' value=''>
               <input type='submit' name='addperiod' value='" . _sx('button', 'Add') . "' class='submit'>";
      }

      if (isset($options['title'])) {
         echo '<table class="tab_cadre_fixe">';
         echo '<tr><th>' . $options['title'] . '</th></tr>';
         echo '<tr class="tab_bg_1">
               <td class="center">';
         echo $addButton;
         Html::closeForm();
         echo '</td></tr></table>';
      } else {
         echo '<tr class="tab_bg_1">
               <td class="center" colspan="' . $options['colspan'] . '">';
         echo $addButton;
         Html::closeForm();
         echo '</td></tr>';
      }
   
   }

   function prepareInputForUpdate($input) {
      if(!empty($input['matrix'])){
         $input['matrix'] = json_encode($input['matrix']);
      }
      if (!$this->checkMandatoryFields($input)) {
         return false;
      }
      return $input;
   }
   
   function prepareInputForAdd($input) {
      if(!empty($input['matrix'])){
         $input['matrix'] = json_encode($input['matrix']);
      }
      if (!$this->checkMandatoryFields($input)) {
         return false;
      }
      return $input;
   }
   
   /** 
   * checkMandatoryFields 
   * 
   * @param type $input
   * @return boolean
   */
   function checkMandatoryFields($input){
      $msg     = array();
      $checkKo = false;
      

      $mandatory_fields = array('name'          => __('Name'),
                                 'matrix'        => __('Matrix'));
      
      foreach ($input as $key => $value) {
         if (array_key_exists($key, $mandatory_fields)) {
            if (empty($value)) {
               $msg[] = $mandatory_fields[$key];
               $checkKo = true;
            }
         }
      }
      
      if ($checkKo) {
         Session::addMessageAfterRedirect(sprintf(__("Mandatory fields are not filled. Please correct: %s"), implode(', ', $msg)), true, ERROR);
         return false;
      }
      return true;
   }

   /**
    * Matrix
    * @global type $CFG_GLPI
    * @param type $matrix
    */
   static function showMatrix($matrix = array()){
      global $CFG_GLPI;
      echo "<table class='tab_cadre_fixe'>";
      echo "<tr class='tab_bg_2'>";
      echo "<td class='b right' colspan='2'>".__('Impact')."</td>";

      for ($users=5 ; $users>=1 ; $users--) {
         echo "<td class='center'>".Ticket::getImpactName($users).'<br>';

         if ($users==3) {
            $isusers[3] = 1;
            echo "<input type='hidden' name='matrix[3]' value='1'>";

         } else {
            $isusers[$users] = (($CFG_GLPI['impact_mask']&(1<<$users)) >0);
            Dropdown::showYesNo("matrix[$users]", $isusers[$users]);
         }
         echo "</td>";
      }
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td class='b' colspan='2'>".__('Urgency')."</td>";

      for ($users=5 ; $users>=1 ; $users--) {
         echo "<td>&nbsp;</td>";
      }
      echo "</tr>";

      for ($urgency=5 ; $urgency>=1 ; $urgency--) {
         echo "<tr class='tab_bg_1'>";
         echo "<td>".Ticket::getUrgencyName($urgency)."&nbsp;</td>";
         echo "<td>";

         if ($urgency==3) {
            $isurgency[3] = 1;
            echo "<input type='hidden' name='matrix[3]' value='1'>";

         } else {
            $isurgency[$urgency] = (($CFG_GLPI['urgency_mask']&(1<<$urgency)) >0);
            Dropdown::showYesNo("matrix[$urgency]", $isurgency[$urgency]);
         }
         echo "</td>";

         for ($users=5 ; $users>=1 ; $users--) {
            $pri = round(($urgency+$users)/2);
            if (isset($matrix[$urgency][$users])) {
               $pri = $matrix[$urgency][$users];
            }


            if ($isurgency[$urgency] && $isusers[$users]) {
               $bgcolor=$_SESSION["glpipriority_$pri"];
               echo "<td class='center' bgcolor='$bgcolor'>";
               Ticket::dropdownPriority(array('value' => $pri,
                                              'name'  => "matrix[$urgency][$users]"));
               echo "</td>";
            } else {
               echo "<td><input type='hidden' name='matrix[$urgency][$users]' value='$pri'>
                     </td>";
            }
         }
         echo "</tr>\n";
      }
      echo "</table>";
      
   }
   
   static function dropdownSensitivity(array $options = array()) {
      global $CFG_GLPI;
      $p['name']     = 'urgency';
      $p['value']    = 0;
      $p['showtype'] = 'normal';
      $p['display']  = true;
      if (is_array($options) && count($options)) {
         foreach ($options as $key => $val) {
            $p[$key] = $val;
         }
      }
      $sensibility = new self();
      $values = array();
      $values[-1] = static::getSensitivityName(-1);
      $result = $sensibility->find();
      foreach ($result as $data){
         $values[$data['id']] = static::getSensitivityName($data['id']);
      }
      return Dropdown::showFromArray($p['name'], $values, $p);
   }
  
    /**
    * Get ITIL object sensitivity Name
    *
    * @param $value sensitivity ID
    * */
   static function getSensitivityName($value){
      if($value == -1){
         return __('Matrix of the general configuration', 'seasonality');
      }else{
         $sensibility = new self();
         $sensibility->getFromDB($value);
         return $sensibility->fields['name'];
      }
   }
   

}

?>
