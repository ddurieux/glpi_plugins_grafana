<?php

define('GLPI_ROOT', '../../..');
include (GLPI_ROOT . "/inc/includes.php");
include (GLPI_ROOT . "/plugins/grafana/inc/grafana.class.php");

$gr = new PluginGrafanaGrafana();
$gr->init();
$gr->generateDashboards($_GET['uri'], $_GET['name'], $_GET['begin_date'], $_GET['end_date'], $_GET['pgentities_id']);

