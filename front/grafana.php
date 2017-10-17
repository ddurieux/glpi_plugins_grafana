<?php

include ("../../../inc/includes.php");

Html::header("grafana", $_SERVER["PHP_SELF"], "plugins",
             "pluginGrafanaGrafana", "grafana");

$gr = new PluginGrafanaGrafana();

$gr->init();
$gr->getDashboards();

Html::footer();