<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Dashboard extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("User_model", "user");
        $this->load->model("Laporanharian_model", "harian");
        $this->load->model("Laporanbulanan_model", "bulanan");
    }

    public function data_get()
    {
        $pegawai    = $this->user
            ->where(["level_user" => LEVEL_KARYAWAN])
            ->count_rows();

        $lapHarian = $this->harian
            ->where([
                "YEAR(created_at)"  => date("Y"),
                "MONTH(created_at)" => date("n")
            ])->count_rows();

        $lapBulanan = $this->bulanan
            ->where([
                "YEAR(created_at)"  => date("Y"),
                "MONTH(created_at)" => date("n")
            ])->count_rows();

        $lapMasuk = $this->harian
            ->where([
                "status_laporanharian" => LAPORAN_BELUM
            ])->count_rows();

        return $this->response(array(
            "status"                => true,
            "response_code"         => REST_Controller::HTTP_OK,
            "response_message"      => "Data ditemukan",
            "data"                  => [
                "pegawai"       => $pegawai,
                "lap_harian"    => $lapHarian,
                "lap_bulanan"   => $lapBulanan,
                "lap_masuk"     => $lapMasuk
            ]
        ), REST_Controller::HTTP_OK);
    }
}
