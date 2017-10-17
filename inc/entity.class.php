<?php

/*
   ------------------------------------------------------------------------
   Plugin Grafana for GLPI
   Copyright (C) 2017-2017 by the Plugin Grafana for David Durieux.

   https://github.com/ddurieux/glpi_plugin_grafana
   ------------------------------------------------------------------------

   LICENSE

   This file is part of Plugin Grafana project.

   Plugin Grafana for GLPI is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Plugin Grafana for GLPI is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   GNU Affero General Public License for more details.

   You should have received a copy of the GNU Affero General Public License
   along with Grafana. If not, see <http://www.gnu.org/licenses/>.

   ------------------------------------------------------------------------

   @package   Plugin Grafana for GLPI
   @author    David Durieux
   @co-author
   @comment
   @copyright Copyright (c) 2017-2017 Plugin Grafana for David Durieux
   @license   AGPL License 3.0 or (at your option) any later version
              http://www.gnu.org/licenses/agpl-3.0-standalone.html
   @link      https://github.com/ddurieux/glpi_plugin_grafana
   @since     2017

   ------------------------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

/**
 * Manage the entity configuration.
 */
class PluginGrafanaEntity extends CommonDBTM {

   /**
    * The right name for this class
    *
    * @var string
    */
   static $rightname = 'entity';

   /**
    * Get name of this type by language of the user connected
    *
    * @param integer $nb number of elements
    * @return string name of this type
    */
   static function getTypeName($nb=0) {
      return __('Entity');
   }



   /**
    * Get the tab name used for item
    *
    * @param object $item the item object
    * @param integer $withtemplate 1 if is a template form
    * @return string name of the tab
    */
   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {

      if ($item->getID() > -1) {
         if (Session::haveRight("config", READ)) {
            return self::createTabEntry('Grafana');
         }
      }
      return '';
   }



   /**
    * Display the content of the tab
    *
    * @param object $item
    * @param integer $tabnum number of the tab to display
    * @param integer $withtemplate 1 if is a template form
    * @return boolean
    */
   static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {

      if ($item->getID() > -1) {
         $pgEntity = new PluginGrafanaEntity();
         $pgEntity->showForm($item->fields['id']);
         return TRUE;
      }
      return FALSE;
   }



   /**
    * Display form
    *
    * @global array $CFG_GLPI
    * @param integer $entities_id
    * @param array $options
    * @return true
    */
   function showForm($entities_id, $options=[]) {
      global $CFG_GLPI;

      $a_configs = $this->find("`entities_id`='".$entities_id."'", "", 1);
      $id = 0;
      if (count($a_configs) == 1) {
         $a_config = current($a_configs);
         $id = $a_config['id'];
         $this->getFromDB($id);
      } else {
         $this->getEmpty();
      }
      $this->initForm($id, $options);
      $this->showFormHeader($options);

      echo "<tr>";
      echo "<td>";
      echo __("Grafana URL", 'grafana');
      echo "</td>";
      echo "<td colspan='3'>";
      echo Html::input('url', ['value' => $this->fields['url'], 'size' => 50]);
      echo Html::hidden('entities_id', ['value' => $entities_id]);
      echo "</td>";
      echo "</tr>";

      echo "<tr>";
      echo "<td colspan='3'>";
      echo "Grafana API Key";
      echo "</td>";
      echo "<td>";
      echo Html::input('token', ['value' => $this->fields['token'], 'size' => 50]);
      echo "</td>";
      echo "</tr>";

      $this->showFormButtons();
      return TRUE;
   }

}
