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

function plugin_grafana_install() {
   global $DB;

/**
Need fields :

url:
apikey:
apikey_name:

 */

   if (!TableExists('glpi_plugin_grafana_configs')) {
      $query = "CREATE TABLE `glpi_plugin_grafana_configs` (
         `id` int(11) NOT NULL AUTO_INCREMENT,
         `entities_id` int(11) NOT NULL DEFAULT '0',
         `createfollowupwithsolve` varchar(255) DEFAULT NULL,
         `assigntechsolveticket` varchar(255) DEFAULT NULL,
         `deletetechsonsolve` varchar(255) DEFAULT NULL,
         `assigntechsolveticketempty` varchar(255) DEFAULT NULL,
         PRIMARY KEY (`id`)
      ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
      $DB->query($query);
      $query = "INSERT INTO `glpi_plugin_grafana_configs`
         (`id`, `entities_id`, `createfollowupwithsolve`, `assigntechsolveticket`, `deletetechsonsolve`, `assigntechsolveticketempty`)
         VALUES (1, 0, '0', '0', '0', '0');";
      $DB->query($query);
//   } else {
//      if (!FieldExists('glpi_plugin_grafana_configs', '')) {
//         $migration = new Migration(PLUGIN_SOLVECLOSETICKETACTION_VERSION);
//         $migration->addField(
//                 'glpi_plugin_solvecloseticketaction_configs',
//                 'assigntechsolveticketempty',
//                 'string',
//                 array('value' => '0'));
//         $migration->executeMigration();
//      }
   }

   return true;
}

// Uninstall process for plugin : need to return true if succeeded
function plugin_grafana_uninstall() {
   global $DB;

   $query = "DROP TABLE `glpi_plugin_grafana_configs`";
   $DB->query($query);

   return true;
}

?>