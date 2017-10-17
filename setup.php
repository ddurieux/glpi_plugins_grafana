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

define ("PLUGIN_GRAFANA_VERSION","9.1+1.0");

// Init the hooks
function plugin_init_grafana() {
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['grafana'] = true;

   $Plugin = new Plugin();
   if ($Plugin->isActivated('grafana')) {
      $autoload = GLPI_ROOT . '/plugins/grafana/vendor/autoload.php';
      require $autoload;

      $Plugin->registerClass('PluginGrafanaEntity',
                 array('addtabon' => array('Entity')));

      $PLUGIN_HOOKS["menu_toadd"]['grafana'] = array('tools'  => 'PluginGrafanaMenu');

   }
   return $PLUGIN_HOOKS;
}

// Name and Version of the plugin
function plugin_version_grafana() {
   return array('name'           => 'Grafana - reporting',
                'shortname'      => 'grafana',
                'version'        => PLUGIN_GRAFANA_VERSION,
                'license'        => 'AGPLv3+',
                'author'         =>'<a href="mailto:d.durieux@siprossii.com">David DURIEUX</a>',
                'homepage'       =>'https://github.com/ddurieux/glpi_plugin_grafana',
                'minGlpiVersion' => '9.1'
   );
}


// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_grafana_check_prerequisites() {

   if (version_compare(GLPI_VERSION,'9.1','lt') || version_compare(GLPI_VERSION,'9.3','ge')) {
      echo "error";
   } else {
      return true;
   }
}

function plugin_grafana_check_config() {
   return true;
}

function plugin_grafana_haveTypeRight($type,$right) {
   return true;
}

?>
