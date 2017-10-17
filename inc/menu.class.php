<?php


class PluginGrafanaMenu extends CommonGLPI {

   static $rightname = 'report';

   static function getMenuName() {
      return __('Grafana export in PDF', 'grafana');
   }

   static function getMenuContent() {
      $menu          = array();
      $menu['title'] = self::getMenuName();
      $menu['page']  = '/plugins/grafana/front/grafana.php';

      return $menu;
   }

}
