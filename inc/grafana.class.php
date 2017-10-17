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

class MYPDF extends TCPDF {

    //Page header
    public function Header() {

    }

	// Page footer
	public function Footer() {

      /*
		$this->SetY(-32);
		$this->SetX(-35);
		// Coin / triangle
		$image_file = '../pics/coin.png';
		$this->Image($image_file);
       */

      /*
		$this->SetY(-18);
		// Logo
		$image_file = '../pics/logo.png';
		$this->Image($image_file);

		// Set font
		$this->SetY(-15);
		$this->SetFont('helvetica', 'I', 8);
		// Page number
		$this->SetX(-1);
		$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
       */
	}
}


class PluginGrafanaGrafana {

   function init() {
      ini_set("memory_limit", "-1");
      ini_set("max_execution_time", "0");
      $this->client = new GuzzleHttp\Client();
   }


   /**
    * Get and display grafana dashboards
    */
   function getDashboards() {
      global $CFG_GLPI;

      $pgEntity = new PluginGrafanaEntity();
      $entity = new Entity();

      echo Html::scriptBlock('function setType(uri, name, pgentities_id)
   {
      document.forms["generate"].elements["uri"].value = uri;
      document.forms["generate"].elements["name"].value = name;
      document.forms["generate"].elements["pgentities_id"].value = pgentities_id;
   }');

      $target = $CFG_GLPI['root_doc'].
          '/plugins/grafana/front/generate.php';

      echo "<form action='".$target."' method='get' target='_blank' name='generate'>";

      echo "<table class='tab_cadre_fixe'>";
      echo "<tr>";
      echo "<th>";
      echo "date début";
      echo "</th>";
      echo "<th>";
      echo "date fin";
      echo "</th>";
      echo "</tr>";

      echo "<tr>";
      echo "<td>";
      Html::showDateTimeField("begin_date", ['value' => date('Y-m-01 00:00:00')]);
      echo "</td>";
      echo "<td>";
      Html::showDateTimeField("end_date", ['value' => date('Y-m-d H:i:s')]);
      echo "</td>";
      echo "</tr>";
      echo "</table>";

      echo "<input type='hidden' name='uri' value=''/>";
      echo "<input type='hidden' name='name' value='' />";
      echo "<input type='hidden' name='pgentities_id' value='' />";

      echo "<table class='tab_cadre_fixe'>";
      echo "<tr>";
      echo "<th colspan='3'>";
      echo "Grafana dashboards";
      echo "</th>";
      echo "</tr>";

      foreach ($_SESSION['glpiactiveentities'] as $entities_id) {
          $grafanas = $pgEntity->find("`entities_id`='".$entities_id."'");
          if (count($grafanas) > 0) {
              $grafana = current($grafanas);
              $token = $grafana['token'];
              $url = $grafana['url'];
              $entity->getFromDB($entities_id);

              $res = $this->client->get($url.'/api/search',
                      ['headers' =>
                          [
                              'Authorization' => "Bearer ".$token
                          ]
                      ]
              );
              $body = $res->getBody();
              $dashboards = json_decode($body->getContents());

              foreach ($dashboards as $dashboard) {
                 if (!empty($dashboard->title)) {
                    echo "<tr>";
                    echo "<td>";
                    echo $entity->getLink(['comments' => True]);
                    echo "</td>";
                    echo "<td>";
                    echo $dashboard->title;
                    echo "</td>";
                    echo "<td>";
                    echo "<input type='submit' value='generate the PDF' class='submit' onclick='setType(\"".$dashboard->uri."\",\"".$dashboard->title."\",\"".$grafana['id']."\")'/>";
                    echo "</td>";
                    echo "</tr>";
                 }
              }
          }
      }

      echo "</table>";
      echo "</form>";
   }



   function generateDashboards($uri, $dashboard_name, $begin_date, $end_date, $pgentities_id) {
      $pgEntity = new PluginGrafanaEntity();
      if ($pgEntity->getFromDB($pgentities_id)) {
         if (!isset($_SESSION['glpiactiveentities'][$pgEntity->fields['entities_id']])) {
            return;
         }

        $token = $pgEntity->fields['token'];
        $url = $pgEntity->fields['url'];

        // Get current organisation
        $res = $this->client->get($url.'/api/org',
                ['headers' =>
                    [
                        'Authorization' => "Bearer ".$token
                    ]
                ]
        );
        $body = $res->getBody();
        $organization = json_decode($body->getContents());

        // get dashboard with name
        $res = $this->client->get($url.'/api/dashboards/'.$uri,
                ['headers' =>
                    [
                        'Authorization' => "Bearer ".$token
                    ]
                ]
        );
        $body = $res->getBody();
        $dashboard = json_decode($body->getContents());

        $pdf = new MYPDF();
        $pdf->SetCreator(PDF_CREATOR);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set font
        $pdf->SetFont('helvetica', '', 10);

        $pdf->AddPage('L');
        $pdf->setPageUnit('px');
        $page_width = $pdf->getPageWidth();
        $page_height = $pdf->getPageHeight();

        $scale = 4;
        $pdf->setImageScale($scale);

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 25, $dashboard_name." / du ".$begin_date." au ".$end_date, 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $y = 30;
        $dates = explode(' ', $begin_date);
        $d = explode('-', $dates[0]);
        $t = explode(':', $dates[1]);
        if (!isset($t[2])) {
           $t[2] = 0;
        }
        $from = (date('U', mktime($t[0], $t[1], $t[2], $d[1], $d[2], $d[0]))) * 1000;

        $dates = explode(' ', $end_date);
        $d = explode('-', $dates[0]);
        $t = explode(':', $dates[1]);
        if (!isset($t[2])) {
           $t[2] = 0;
        }
        $to = date('U', mktime($t[0], $t[1], $t[2], $d[1], $d[2], $d[0])) * 1000;

        foreach ($dashboard->dashboard->rows as $row) {
           $space = 7;
           $span_width = ceil($page_width - ($space * 13)) / 12;
           $x = $space;
           $max_height_image = 0;
           foreach ($row->panels as $panel) {
              // get the image

              $width = (250 * $panel->span) + ($space * ($panel->span - 1) * $scale);
              $height = 500;
              $resimg = $this->client->get($url.'/render/dashboard-solo/'.$uri.'?orgId='.$organization->id.'&panelId='.$panel->id.'&from='.$from.'&to='.$to.'&width='.$width.'&height='.$height.'&theme=light&tz=UTC+02:00&timeout=200',
                ['headers' =>
                    [
                        'Authorization' => "Bearer ".$token
                    ]
                ]
              );
              $bodyimg = $resimg->getBody();
              $image_string = $bodyimg->getContents();
              $img_info = getimagesizefromstring($image_string);
              if ($img_info[1] > $max_height_image) {
                 $max_height_image = $img_info[1];
              }
              $pdf->SetLineWidth(1);
              $pdf->Image('@'.$image_string, $x, $y, 0, 0, 'PNG', '', '', false, 300, '', false, false, 1);
              //$x += ($panel->span * 25);
              $x += $space;
              $x += ($width / $scale);
           }
           $y += ($height / $scale) + 10;
           if ($y > $page_height) {
               $y = 30 + ($height / $scale) + 10;
           }
           Toolbox::logDebug("y value : ".$y);
        }
        // reset pointer to the last page
        $pdf->lastPage();
        $pdf->Output('/tmp/grafana.pdf', 'I');
      }
   }
}
