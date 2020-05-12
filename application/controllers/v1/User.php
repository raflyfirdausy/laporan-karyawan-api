<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class User extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("User_model", "user");
    }

    public function index_post()
    {
        $data       = json_decode(file_get_contents("php://input"));
        $id_user    = set($data->id_user);

        if(empty($id_user)){
            $kondisi = [
                "level_user"    => LEVEL_KARYAWAN
            ];
        } else {
            $kondisi = [
                "id_user"       => $id_user,
                "level_user"    => LEVEL_KARYAWAN
            ];
        }

        $user       = $this->user->where($kondisi)->get_all();
        if ($user) {
            for ($index = 0; $index < sizeof($user); $index++) {
                $user[$index]["foto_user"] = asset("foto/" . $user[$index]["foto_user"]);
            }
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_OK,
                "response_message"      => "User berhasil ditemukan",
                "data"                  => $user
            ), REST_Controller::HTTP_OK);
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                "response_message"      => "User tidak ditemukan",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }
}
