<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Kota extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Kota_model", "kota");
    }
}
