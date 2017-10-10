<?php

define('GLPI_ROOT', '../../..');
include (GLPI_ROOT . "/inc/includes.php");
include (GLPI_ROOT . "/plugins/grafana/inc/grafana.class.php");
$gr = new PluginGrafanaGrafana();
$gr->init();
$gr->get_dashboards();
?>