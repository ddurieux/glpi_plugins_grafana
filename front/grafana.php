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

include ("../../../inc/includes.php");

Html::header("grafana", $_SERVER["PHP_SELF"], "plugins",
             "pluginGrafanaGrafana", "grafana");

$gr = new PluginGrafanaGrafana();

$gr->init();
$gr->getDashboards();

Html::footer();