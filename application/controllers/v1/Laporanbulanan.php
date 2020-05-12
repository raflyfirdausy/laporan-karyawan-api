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
    }

    public function data_post()
    {
        $data                   = json_decode(file_get_contents("php://input"));
        $id_laporanbulanan      = set($data->id_laporanbulanan);
        $id_user                = set($data->id_user);
        $status_laporanbulanan  = set($data->tahun_laporanharian);
        $tahun_laporanbulanan   = set($data->laporanbulanan);
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
            if (empty($bulan_laporanbulanan) || empty($tahun_laporanharian)) {
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_BAD_REQUEST,
                    "response_message"      => "Bad Request! " . $status_laporanharian,
                    "data"                  => null
                ), REST_Controller::HTTP_OK);
            } else {
                if (!empty($id_user)) {
                    $kondisi = [
                        "id_user"           => $id_user,
                        "YEAR(created_at)"  => $tahun_laporanharian,
                        "MONTH(created_at)" => $bulan_laporanharian
                    ];
                } else if (!empty($id_outlet)) {
                    $kondisi = [
                        "id_outlet"         => $id_outlet,
                        "YEAR(created_at)"  => $tahun_laporanharian,
                        "MONTH(created_at)" => $bulan_laporanharian
                    ];
                } else {
                    $kondisi = [
                        "YEAR(created_at)"  => $tahun_laporanharian,
                        "MONTH(created_at)" => $bulan_laporanharian
                    ];
                }
            }
        }

        $laporanharian = $this->harian
            ->where($kondisi)
            ->with_user()
            ->with_outlet(["with"  => ["relation"  => "kota"]])
            ->get_all();

        if ($laporanharian) {
            for ($a = 0; $a < sizeof($laporanharian); $a++) {
                $laporanharian[$a]["bukti_laporanharian"]   = asset("laporan/" . $laporanharian[$a]["bukti_laporanharian"]);
                $laporanharian[$a]["user"]["foto_user"]     = asset("foto/" . $laporanharian[$a]["user"]["foto_user"]);
            }
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_OK,
                "response_message"      => "Laporan Harian berhasil ditemukan",
                "data"                  => $laporanharian
            ), REST_Controller::HTTP_OK);
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                "response_message"      => "Laporan Harian tidak ditemukan",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }
}
