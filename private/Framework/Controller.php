<?php
/*
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace OpenSB\Framework;

use \SimpleXMLElement;

use OpenSB\App;
use OpenSB\Helpers\XML;
use OpenSB\Framework\DB;
use OpenSB\Framework\Frontend;

/**
 * OpenSBFramework Controller.
 *
 * This is a base class that you can extend to make a valid controller for your route.
 * @author RGB
 */
class Controller {
    public $db;
    public $frontend;
    public $sbdb;
    public $appConfig;

    public function __construct() {
        $this->db = App::container()->get(DB::class);
        $this->frontend = App::container()->get(Frontend::class);
        $this->appConfig = App::config();
    }

    public function returnJSON(object $data) {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    // TODO: remove this, not used by anything
    public function returnXML($data) {
        header('Content-Type: application/xml');

        $xml_data = new SimpleXMLElement('<?xml version="1.0"?><data></data>');
        XML::arrayToXML($data, $xml_data);

        return $xml_data->asXML();
    }

    public function redirect(string $url) {
        header("Location: " . $url);
        return;
    }
}