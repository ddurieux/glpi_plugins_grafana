<?php


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

?>