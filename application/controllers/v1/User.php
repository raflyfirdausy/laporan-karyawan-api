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

    public function data_post()
    {
        $data       = json_decode(file_get_contents("php://input"));
        $id_user    = set($data->id_user);

        if (empty($id_user)) {
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

    //TODO : CONVERT BASE64 TO IMAGE $foto_user
    public function update_data_post()
    {
        $data                   = json_decode(file_get_contents("php://input"));
        $id_user                = set($data->id_user);
        $nip_user               = set($data->nip_user);
        $nama_user              = set($data->nama_user);
        $foto_user              = set($data->foto_user);

        if (!empty($foto_user)) {            
            $image  = base64_decode($foto_user);
            $foto_user = now() . ".jpg";
            file_put_contents("assets/foto/" . $foto_user, $image);
        } else {
            $foto_user = null;
        }

        if (empty($id_user)) {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_BAD_REQUEST,
                "response_message"      => "Bad Request!",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }

        $cekUser = $this->user->get($id_user);
        if ($cekUser) {
            $dataUpdate = [
                "nip_user"          => $nip_user,
                "nama_user"         => $nama_user,
                "foto_user"         => $foto_user
            ];
            $update = $this->user->where(["id_user" => $id_user])->update($dataUpdate);
            if ($update) {
                $dataUser = $this->user->get($cekUser["id_user"]);
                $dataUser["foto_user"] = asset("foto/" . $dataUser["foto_user"]);
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_OK,
                    "response_message"      => "User berhasil diupdate",
                    "data"                  => $dataUser
                ), REST_Controller::HTTP_OK);
            } else {
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_PRECONDITION_FAILED,
                    "response_message"      => "User gagal diupdate : " . db_error(),
                    "data"                  => null
                ), REST_Controller::HTTP_OK);
            }
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                "response_message"      => "User tidak ditemukan",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }

    public function update_password_post()
    {
        $data                   = json_decode(file_get_contents("php://input"));
        $id_user                = set($data->id_user);
        $password_lama          = md5(set($data->password_lama));
        $password_baru          = md5(set($data->password_baru));

        if (empty($id_user) || empty($password_lama) || empty($password_baru)) {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_BAD_REQUEST,
                "response_message"      => "Bad Request!",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }

        $cekUser = $this->user->where([
            "id_user"           => $id_user,
            "password_user"     => $password_lama
        ])->get();
        if ($cekUser) {
            $updatePassword = $this->user->where(["id_user" => $cekUser["id_user"]])
                ->update(["password_user" => $password_baru]);
            if ($updatePassword) {
                $dataUser = $this->user->get($cekUser["id_user"]);
                $dataUser["foto_user"] = asset("foto/" . $dataUser["foto_user"]);
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_OK,
                    "response_message"      => "Password user berhasil diupdate",
                    "data"                  => $dataUser
                ), REST_Controller::HTTP_OK);
            } else {
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_PRECONDITION_FAILED,
                    "response_message"      => "password gagal diupdate : " . db_error(),
                    "data"                  => null
                ), REST_Controller::HTTP_OK);
            }
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                "response_message"      => "Password lama yang anda masukan salah",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }

    public function delete_post()
    {
        $data                   = json_decode(file_get_contents("php://input"));
        $id_user                = set($data->id_user);

        if (empty($id_user)) {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_BAD_REQUEST,
                "response_message"      => "Bad Request!",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }

        $cekUser = $this->user->get($id_user);
        if ($cekUser) {
            $delete = $this->user->delete($cekUser["id_user"]);
            if ($delete) {
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_OK,
                    "response_message"      => "User berhasil di hapus",
                    "data"                  => null
                ), REST_Controller::HTTP_OK);
            } else {
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                    "response_message"      => "User gagal di hapus : " . db_error(),
                    "data"                  => null
                ), REST_Controller::HTTP_OK);
            }
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_PRECONDITION_FAILED,
                "response_message"      => "User tidak ditemukan!",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }
}
