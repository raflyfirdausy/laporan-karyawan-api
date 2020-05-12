<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Auth extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("User_model", "user");
    }

    //TODO : CONVERT BASE64 TO IMAGE $foto_user
    public function register_post()
    {
        $data                   = json_decode(file_get_contents("php://input"));
        $nip_user               = set($data->nip_user);
        $username_user          = set($data->username_user);
        $password_user          = $data->password_user;
        $nama_user              = $data->nama_user;
        $foto_user              = set($data->foto_user);
        $level_user             = $data->level_user;

        $dataInsert = [
            "nip_user"          => $nip_user,
            "username_user"     => $username_user,
            "password_user"     => md5($password_user),
            "nama_user"         => $nama_user,
            "foto_user"         => $foto_user,
            "level_user"        => $level_user
        ];

        $insert     = $this->user->insert($dataInsert);
        if ($insert) {
            $data = $this->user->get($insert);
            if ($data) {
                $data["foto_user"] = asset("foto/" . $data["foto_user"]);
            }
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_OK,
                "response_message"      => "User berhasil didaftarkan",
                "data"                  => $data
            ), REST_Controller::HTTP_OK);
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                "response_message"      => "User gagal didaftarkan : " . db_error(),
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }

    public function login_post()
    {
        $data                   = json_decode(file_get_contents("php://input"));
        $username_nip           = set($data->username_nip);
        $password_user          = md5(set($data->password_user));

        if (empty($username_nip) || empty($password_user)) {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_BAD_REQUEST,
                "response_message"      => "Bad Request!",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }

        $login  = $this->user->where("nip_user", $username_nip)
            ->where("username_user", "=", $username_nip, TRUE)
            ->where("password_user", $password_user)
            ->get();

        if ($login) {
            $login["foto_user"] = asset("foto/" . $login["foto_user"]);
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_OK,
                "response_message"      => "Username ditemukan",
                "data"                  => $login
            ), REST_Controller::HTTP_OK);
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_NOT_FOUND,
                "response_message"      => "User tidak ditemukan ",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }
}
