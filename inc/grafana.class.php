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

   function get_dashboards() {
      $token = "eyJrIjoiRmp6R2t2NGttVnV3YldKdHRvbTVDMHhPVDFqaUM4NVMiLCJuIjoidGVzdDIiLCJpZCI6MX0=";
      $url = "http://127.0.0.1:3000";
      $dashboard_name = "glpi-dashboard-test";


      $res = $this->client->get($url.'/api/search',
              ['headers' =>
                  [
                      'Authorization' => "Bearer ".$token
                  ]
              ]
      );
      $body = $res->getBody();

      // get dashboard with name
      $res = $this->client->get($url.'/api/dashboards/db/'.$dashboard_name,
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

      $pdf->setPrintHeader(false);

      // set auto page breaks
      $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

      // set image scale factor
//      $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

      // set font
      $pdf->SetFont('helvetica', '', 10);

      $pdf->AddPage('L');
      $pdf->setPageUnit('px');
      $page_width = $pdf->getPageWidth();
      $y = 0;
      // get each graph of each panel and recompose each line
      foreach ($dashboard->dashboard->rows as $row) {
//         $page_width = 900;
         $space = 2;
         $span_width = ceil($page_width - ($space * 13)) / 12;

         //$html = $this->loadCSS(False);
         //$pdf->writeHTML($html, true, false, false, false, '');

         $x = $space;
         $max_height_image = 0;
         foreach ($row->panels as $panel) {
            /**
             * we need:
             *  id
             *  span (/12 max)
             *
             */
            // get the image
            $width = $span_width * $panel->span + ($space * ($panel->span - 1)) *4;
            $height = 200 * 2;
            $from = (date('U') - (3600 * 24 * 7)) * 1000;
            $to = date('U') * 1000;

            $resimg = $this->client->get($url.'/render/dashboard-solo/db/'.$dashboard_name.'?orgId=1&panelId='.$panel->id.'&from='.$from.'&to='.$to.'&width='.$width.'&height='.$height.'&theme=light&tz=UTC+02:00',
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
            $pdf->setImageScale(2);
            $pdf->SetLineWidth(1);
            $pdf->Image('@'.$image_string, $x, $y, ($img_info[0]), ($img_info[1]), 'PNG', '', '', TRUE, 600, '', false, false, 1);
            //$x += ($panel->span * 25);
            $x += $space;
            $x += ($width / 2);
         }
         $y += $max_height_image;
      }
      // reset pointer to the last page
      $pdf->lastPage();

      $pdf->Output('/tmp/grafana.pdf', 'I');



   }

}
