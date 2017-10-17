<?php

include ("../../../inc/includes.php");

Html::header(__('Grafana', 'grafana'),
             $_SERVER["PHP_SELF"],
             "plugins",
             "",
             "");

Session::checkRight('entity', UPDATE);
if (isset($_POST['add'])) {
   $_POST['id'] = 0;
}

if (isset($_POST['id'])) {
   $pgEntity = new PluginGrafanaEntity();

   $input = ['entities_id' => $_POST['entities_id']];
   $input['url'] = $_POST['url'];
   $input['token'] = $_POST['token'];
   if ($_POST['id'] > 0) {
      $input['id'] = $_POST['id'];
      $pgEntity->update($input);
   } else {
      $pgEntity->add($input);
   }
   Html::back();
}

Html::footer();
