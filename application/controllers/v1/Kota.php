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

    public function data_post()
    {
        $data       = json_decode(file_get_contents("php://input"));
        $id_kota    = set($data->id_kota);

        if (empty($id_kota)) {
            $kota = $this->kota->get_all();
        } else {
            $kota = $this->kota
                ->where(["id_kota" => $id_kota])
                ->order_by("nama_kota", "ASC")
                ->get_all();
        }

        if ($kota) {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_OK,
                "response_message"      => "Kota berhasil ditemukan",
                "data"                  => $kota
            ), REST_Controller::HTTP_OK);
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                "response_message"      => "Kota tidak ditemukan",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }

    public function add_post()
    {
        $data       = json_decode(file_get_contents("php://input"));
        $nama_kota  = set($data->nama_kota);

        if (empty($nama_kota)) {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_BAD_REQUEST,
                "response_message"      => "Bad Request!",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }

        $insert = $this->kota->insert(["nama_kota" => $nama_kota]);
        if ($insert) {
            $dataInsert = $this->kota->get($insert);
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_OK,
                "response_message"      => "Kota berhasil ditambahkan",
                "data"                  => $dataInsert
            ), REST_Controller::HTTP_OK);
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                "response_message"      => "Kota gagal ditambahkan : " . db_error(),
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }

    public function update_post()
    {
        $data       = json_decode(file_get_contents("php://input"));
        $id_kota    = set($data->id_kota);
        $nama_kota  = set($data->nama_kota);

        if (empty($id_kota)) {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_BAD_REQUEST,
                "response_message"      => "Bad Request!",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }

        $cekKota = $this->kota->get($id_kota);
        if ($cekKota) {
            $update = $this->kota->where(["id_kota" => $id_kota])->update(["nama_kota" => $nama_kota]);
            if ($update) {
                $dataKota = $this->kota->get($cekKota["id_kota"]);
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_OK,
                    "response_message"      => "Kota berhasil diupdate",
                    "data"                  => $dataKota
                ), REST_Controller::HTTP_OK);
            } else {
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                    "response_message"      => "Kota gagal diupdate : " . db_error(),
                    "data"                  => null
                ), REST_Controller::HTTP_OK);
            }
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                "response_message"      => "kota tidak ditemukan",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }

    public function delete_post()
    {
        $data                   = json_decode(file_get_contents("php://input"));
        $id_kota                = set($data->id_kota);

        if (empty($id_kota)) {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_BAD_REQUEST,
                "response_message"      => "Bad Request!",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }

        $cekKota = $this->kota->get($id_kota);
        if ($cekKota) {
            $delete = $this->kota->delete($cekKota["id_kota"]);
            if ($delete) {
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_OK,
                    "response_message"      => "Kota berhasil di hapus",
                    "data"                  => null
                ), REST_Controller::HTTP_OK);
            } else {
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                    "response_message"      => "Kota gagal di hapus : " . db_error(),
                    "data"                  => null
                ), REST_Controller::HTTP_OK);
            }
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_PRECONDITION_FAILED,
                "response_message"      => "Kota tidak ditemukan!",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }
}
