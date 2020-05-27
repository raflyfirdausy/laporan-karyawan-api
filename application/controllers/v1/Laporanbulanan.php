<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Laporanbulanan extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("Laporanbulanan_model", "bulanan");
        $this->load->model("User_model", "user");
    }

    public function data_post()
    {
        $data                   = json_decode(file_get_contents("php://input"));
        $id_laporanbulanan      = set($data->id_laporanbulanan);
        $id_user                = set($data->id_user);
        $tahun_laporanbulanan   = set($data->tahun_laporanbulanan);
        $bulan_laporanbulanan   = set($data->bulan_laporanbulanan);
        $status_laporanbulanan  = set($data->status_laporanbulanan);

        if (!empty($id_laporanbulanan)) {
            $kondisi = [
                "id_laporanbulanan"      => $id_laporanbulanan
            ];
        } else if (!empty($status_laporanbulanan)) {
            $kondisi = [
                "status_laporanbulanan"  => $status_laporanbulanan
            ];
        } else {
            if (empty($bulan_laporanbulanan) || empty($tahun_laporanbulanan)) {
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_BAD_REQUEST,
                    "response_message"      => "Bad Request! ",
                    "data"                  => null
                ), REST_Controller::HTTP_OK);
            } else {
                if (!empty($id_user)) {
                    $kondisi = [
                        "id_user"           => $id_user,
                        "YEAR(created_at)"  => $tahun_laporanbulanan,
                        "MONTH(created_at)" => $bulan_laporanbulanan
                    ];
                } else {
                    $kondisi = [
                        "YEAR(created_at)"  => $tahun_laporanbulanan,
                        "MONTH(created_at)" => $bulan_laporanbulanan
                    ];
                }
            }
        }

        $laporanBulanan = $this->bulanan
            ->where($kondisi)
            ->with_user()
            ->get_all();

        if ($laporanBulanan) {
            for ($a = 0; $a < sizeof($laporanBulanan); $a++) {
                if (empty($laporanBulanan[$a]["user"])) {
                    $laporanBulanan[$a]["user"] = $this->user->with_trashed()->get($laporanBulanan[$a]["id_user"]);
                }
                $laporanBulanan[$a]["user"]["foto_user"]     = asset("foto/" . $laporanBulanan[$a]["user"]["foto_user"]);
            }
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_OK,
                "response_message"      => "Laporan Bulanan berhasil ditemukan",
                "data"                  => $laporanBulanan
            ), REST_Controller::HTTP_OK);
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                "response_message"      => "Laporan Bulanan tidak ditemukan",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }

    public function add_post()
    {
        $data                       = json_decode(file_get_contents("php://input"));
        $id_user                    = set($data->id_user);
        $isi_laporanbulanan         = set($data->isi_laporanbulanan);

        $dataInsert = [
            "id_user"                   => $id_user,
            "isi_laporanbulanan"        => $isi_laporanbulanan,
            "status_laporanbulanan"     => LAPORAN_BELUM,
        ];

        if (date("d") >= 28) { //? INSERT HANYA BISA TANGGAL 28 - 31
            $cekInsert = $this->bulanan->where([
                "id_user"           => $id_user,                
                "YEAR(created_at)"  => date("Y"),
                "MONTH(created_at)" => date("n")
            ])->get();
            if (!$cekInsert) {
                $insert = $this->bulanan->insert($dataInsert);
                if ($insert) {
                    $dataInsert = $this->bulanan
                        ->with_user()                        
                        ->get($insert);
                    return $this->response(array(
                        "status"                => true,
                        "response_code"         => REST_Controller::HTTP_OK,
                        "response_message"      => "Laporan bulanan berhasil ditambahkan",
                        "data"                  => $dataInsert
                    ), REST_Controller::HTTP_OK);
                } else {
                    return $this->response(array(
                        "status"                => true,
                        "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                        "response_message"      => "Laporan bulanan gagal ditambahkan : " . db_error(),
                        "data"                  => null
                    ), REST_Controller::HTTP_OK);
                }
            } else {
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_PRECONDITION_FAILED,
                    "response_message"      => "Anda sudah melakukan laporan pada bulan tersebut",
                    "data"                  => null
                ), REST_Controller::HTTP_OK);
            }
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_PRECONDITION_FAILED,
                "response_message"      => "Maaf laporan bulanan hanya dapat dilakukan mulai tanggal 28 s.d 31",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }

    public function proses_post()
    {
        $data                       = json_decode(file_get_contents("php://input"));
        $id_laporanbulanan          = set($data->id_laporanbulanan);
        $status_laporanbulanan      = set($data->status_laporanbulanan);

        if (empty($id_laporanbulanan) || empty($status_laporanbulanan)) {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_BAD_REQUEST,
                "response_message"      => "Bad Request!",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }

        $cekLaporanBulanan = $this->bulanan->get($id_laporanbulanan);
        if ($cekLaporanBulanan) {
            $status = [LAPORAN_BELUM, LAPORAN_DITERIMA, LAPORAN_DITOLAK];
            if (in_array($status_laporanbulanan, $status)) {
                $update = $this->bulanan->where(["id_laporanbulanan" => $id_laporanbulanan])
                    ->update(["status_laporanbulanan" => $status_laporanbulanan]);
                if ($update) {
                    $dataUpdate = $this->bulanan
                        ->with_user()
                        ->get($cekLaporanBulanan["id_laporanbulanan"]);
                    return $this->response(array(
                        "status"                => true,
                        "response_code"         => REST_Controller::HTTP_OK,
                        "response_message"      => "Laporan bulanan berhasil diupdate",
                        "data"                  => $dataUpdate
                    ), REST_Controller::HTTP_OK);
                } else {
                    return $this->response(array(
                        "status"                => true,
                        "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                        "response_message"      => "Laporan bulanan gagal diupdate : " . db_error(),
                        "data"                  => null
                    ), REST_Controller::HTTP_OK);
                }
            } else {
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_PRECONDITION_FAILED,
                    "response_message"      => "Proses ditolak! status laporan tidak diketahui",
                    "data"                  => null
                ), REST_Controller::HTTP_OK);
            }
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                "response_message"      => "Laporan bulanan tidak ditemukan",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }


}
