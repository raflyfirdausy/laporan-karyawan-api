<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Outlet extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Outlet_model", "outlet");
    }

    public function data_post()
    {
        $data       = json_decode(file_get_contents("php://input"));
        $id_outlet  = set($data->id_outlet);
        $id_kota    = set($data->id_kota);

        if (!empty($id_outlet)) {
            $kondisi = ["id_outlet" => $id_outlet];
        } else if (!empty($id_kota)) {
            $kondisi = ["id_kota" => $id_kota];
        } else {
            $kondisi = null;
        }

        $outlet = $this->outlet->where($kondisi)
            ->with_kota()
            ->get_all();

        if ($outlet) {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_OK,
                "response_message"      => "Outlet berhasil ditemukan",
                "data"                  => $outlet
            ), REST_Controller::HTTP_OK);
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                "response_message"      => "Outlet tidak ditemukan",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }

    public function add_post()
    {
        $data           = json_decode(file_get_contents("php://input"));
        $id_kota        = set($data->id_kota);
        $nama_outlet    = set($data->nama_outlet);

        if (empty($id_kota) || empty($nama_outlet)) {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_BAD_REQUEST,
                "response_message"      => "Bad Request!",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }

        $dataInsert = [
            "id_kota"       => $id_kota,
            "nama_outlet"   => $nama_outlet
        ];
        $insert = $this->outlet->insert($dataInsert);
        if ($insert) {
            $dataInsert = $this->outlet->get($insert);
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_OK,
                "response_message"      => "Outlet berhasil ditambahkan",
                "data"                  => $dataInsert
            ), REST_Controller::HTTP_OK);
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                "response_message"      => "Outlet gagal ditambahkan : " . db_error(),
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }

    public function update_post()
    {
        $data           = json_decode(file_get_contents("php://input"));
        $id_outlet      = set($data->id_outlet);
        $id_kota        = set($data->id_kota);
        $nama_outlet    = set($data->nama_outlet);

        if (empty($id_outlet) || empty($id_kota) || empty($nama_outlet)) {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_BAD_REQUEST,
                "response_message"      => "Bad Request!",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }

        $cekOutlet = $this->outlet->get($id_outlet);
        if ($cekOutlet) {
            $update = $this->outlet
                ->where(["id_outlet" => $id_outlet])
                ->update([
                    "id_kota"       => $id_kota,
                    "nama_outlet"   => $nama_outlet
                ]);
            if ($update) {
                $dataOutlet = $this->outlet->get($cekOutlet["id_outlet"]);
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_OK,
                    "response_message"      => "Outlet berhasil diupdate",
                    "data"                  => $dataOutlet
                ), REST_Controller::HTTP_OK);
            } else {
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                    "response_message"      => "Outlet gagal diupdate : " . db_error(),
                    "data"                  => null
                ), REST_Controller::HTTP_OK);
            }
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                "response_message"      => "Outlet tidak ditemukan",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }

    public function delete_post()
    {
        $data                   = json_decode(file_get_contents("php://input"));
        $id_outlet              = set($data->id_outlet);

        if (empty($id_outlet)) {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_BAD_REQUEST,
                "response_message"      => "Bad Request!",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }

        $cekOutlet = $this->outlet->get($id_outlet);
        if ($cekOutlet) {
            $delete = $this->outlet->delete($cekOutlet["id_outlet"]);
            if ($delete) {
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_OK,
                    "response_message"      => "Outlet berhasil di hapus",
                    "data"                  => null
                ), REST_Controller::HTTP_OK);
            } else {
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                    "response_message"      => "Outlet gagal di hapus : " . db_error(),
                    "data"                  => null
                ), REST_Controller::HTTP_OK);
            }
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_PRECONDITION_FAILED,
                "response_message"      => "Outlet tidak ditemukan!",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }
}
