<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Laporanharian extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Laporanharian_model", "harian");
        $this->load->model("User_model", "user");
        $this->load->model("Outlet_model", "outlet");
        $this->load->model("Kota_model", "kota");
    }

    public function data_post()
    {
        $data                   = json_decode(file_get_contents("php://input"));
        $id_laporanharian       = set($data->id_laporanharian);
        $id_user                = set($data->id_user);
        $id_outlet              = set($data->id_outlet);
        $tahun_laporanharian    = set($data->tahun_laporanharian);
        $bulan_laporanharian    = set($data->bulan_laporanharian);
        $status_laporanharian   = set($data->status_laporanharian);

        if (!empty($id_laporanharian)) {
            $kondisi = [
                "id_laporanharian"      => $id_laporanharian
            ];
        } else if (!empty($status_laporanharian)) {
            $kondisi = [
                "status_laporanharian"  => $status_laporanharian
            ];
        } else {
            if (empty($bulan_laporanharian) || empty($tahun_laporanharian)) {
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

                if (empty($laporanharian[$a]["outlet"])) {
                    $laporanharian[$a]["outlet"] = $this->outlet
                        ->with_kota()
                        ->with_trashed()
                        ->get($laporanharian[$a]["id_outlet"]);
                }

                if (empty($laporanharian[$a]["outlet"]["kota"])) {
                    $laporanharian[$a]["outlet"]["kota"] = $this->kota
                        ->with_trashed()
                        ->get($laporanharian[$a]["outlet"]["id_kota"]);
                }

                if (empty($laporanharian[$a]["user"])) {
                    $laporanharian[$a]["user"] = $this->user->with_trashed()->get($laporanharian[$a]["id_user"]);
                }


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

    //TODO : CONVERT BASE64 TO IMAGE $bukti_laporanharian
    //TODO : USER UDAH PERNAH LAPORAN BELUM PADA HARI INI DAN OUTLET TSB
    public function add_post()
    {
        $data                       = json_decode(file_get_contents("php://input"));
        $id_user                    = set($data->id_user);
        $id_outlet                  = set($data->id_outlet);
        $alamat_laporanharian       = set($data->alamat_laporanharian);
        $latitude_laporanharian     = set($data->latitude_laporanharian);
        $longitude_laporanharian    = set($data->longitude_laporanharian);
        $keterangan_laporanharian   = set($data->keterangan_laporanharian);
        $bukti_laporanharian        = set($data->bukti_laporanharian);

        if (!empty($bukti_laporanharian)) {
            $image  = base64_decode($bukti_laporanharian);
            $bukti_laporanharian = now() . ".jpg";
            file_put_contents("assets/laporan/" . $bukti_laporanharian, $image);
        } else {
            $bukti_laporanharian = null;
        }

        $dataInsert = [
            "id_user"                   => $id_user,
            "id_outlet"                 => $id_outlet,
            "alamat_laporanharian"      => $alamat_laporanharian,
            "latitude_laporanharian"    => $latitude_laporanharian,
            "longitude_laporanharian"   => $longitude_laporanharian,
            "keterangan_laporanharian"  => $keterangan_laporanharian,
            "bukti_laporanharian"       => $bukti_laporanharian,            //! AJA KELALEN KIE CONVERT
            "status_laporanharian"      => LAPORAN_BELUM
        ];

        $cekInsert = $this->harian->where([
            "id_user"           => $id_user,
            "id_outlet"         => $id_outlet,
            "YEAR(created_at)"  => date("Y"),
            "MONTH(created_at)" => date("n")
        ])->get();

        if (!$cekInsert) {
            $insert = $this->harian->insert($dataInsert);
            if ($insert) {
                $dataInsert = $this->harian
                    ->with_user()
                    ->with_outlet(["with"  => ["relation"  => "kota"]])
                    ->get($insert);
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_OK,
                    "response_message"      => "Laporan harian berhasil ditambahkan",
                    "data"                  => $dataInsert
                ), REST_Controller::HTTP_OK);
            } else {
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                    "response_message"      => "Laporan harian gagal ditambahkan : " . db_error(),
                    "data"                  => null
                ), REST_Controller::HTTP_OK);
            }
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_PRECONDITION_FAILED,
                "response_message"      => "Anda sudah melakukan laporan pada outlet tersebut pada hari ini",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }

    public function proses_post()
    {
        $data                       = json_decode(file_get_contents("php://input"));
        $id_laporanharian           = set($data->id_laporanharian);
        $status_laporanharian       = set($data->status_laporanharian);

        if (empty($id_laporanharian) || empty($status_laporanharian)) {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_BAD_REQUEST,
                "response_message"      => "Bad Request!",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }

        $cekLaporanHarian = $this->harian->get($id_laporanharian);
        if ($cekLaporanHarian) {
            $status = [LAPORAN_BELUM, LAPORAN_DITERIMA, LAPORAN_DITOLAK];
            if (in_array($status_laporanharian, $status)) {
                $update = $this->harian->where(["id_laporanharian" => $id_laporanharian])
                    ->update(["status_laporanharian" => $status_laporanharian]);
                if ($update) {
                    $dataUpdate = $this->harian
                        ->with_user()
                        ->with_outlet(["with"  => ["relation"  => "kota"]])
                        ->get($cekLaporanHarian["id_laporanharian"]);
                    return $this->response(array(
                        "status"                => true,
                        "response_code"         => REST_Controller::HTTP_OK,
                        "response_message"      => "Laporan harian berhasil diupdate",
                        "data"                  => $dataUpdate
                    ), REST_Controller::HTTP_OK);
                } else {
                    return $this->response(array(
                        "status"                => true,
                        "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                        "response_message"      => "Laporan harian gagal diupdate : " . db_error(),
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
                "response_message"      => "Laporan harian tidak ditemukan",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }

    public function update_post()
    {
        $data                       = json_decode(file_get_contents("php://input"));
        $id_laporanharian           = set($data->id_laporanharian);
        $id_outlet                  = set($data->id_outlet);
        $alamat_laporanharian       = set($data->alamat_laporanharian);
        $latitude_laporanharian     = set($data->latitude_laporanharian);
        $longitude_laporanharian    = set($data->longitude_laporanharian);
        $keterangan_laporanharian   = set($data->keterangan_laporanharian);
        $bukti_laporanharian        = set($data->bukti_laporanharian);

        if (empty($id_laporanharian)) {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_BAD_REQUEST,
                "response_message"      => "Bad Request! : id laporan tidak deketahui",
                "data"                  => $data
            ), REST_Controller::HTTP_OK);
        }

        $cekLaporan = $this->harian->as_object()->get($id_laporanharian);
        if ($cekLaporan) {
            if ($cekLaporan->status_laporanharian == LAPORAN_BELUM) {
                if (!empty($bukti_laporanharian)) {
                    $image  = base64_decode($bukti_laporanharian);
                    $bukti_laporanharian = now() . ".jpg";
                    file_put_contents("assets/laporan/" . $bukti_laporanharian, $image);

                    if (!empty($cekLaporan->bukti_laporanharian)) {
                        unlink("assets/laporan/" . $cekLaporan->bukti_laporanharian);
                    }

                    $dataUpdate = [
                        "id_outlet"                 => $id_outlet,
                        "alamat_laporanharian"      => $alamat_laporanharian,
                        "latitude_laporanharian"    => $latitude_laporanharian,
                        "longitude_laporanharian"   => $longitude_laporanharian,
                        "keterangan_laporanharian"  => $keterangan_laporanharian,
                        "bukti_laporanharian"       => $bukti_laporanharian
                    ];
                } else {
                    $dataUpdate = [
                        "id_outlet"                 => $id_outlet,
                        "alamat_laporanharian"      => $alamat_laporanharian,
                        "latitude_laporanharian"    => $latitude_laporanharian,
                        "longitude_laporanharian"   => $longitude_laporanharian,
                        "keterangan_laporanharian"  => $keterangan_laporanharian
                    ];
                }

                $update = $this->harian->where($cekLaporan->id_laporanharian)->update($dataUpdate);
                if ($update) {
                    $dataUpdate = $this->harian
                        ->with_user()
                        ->with_outlet(["with"  => ["relation"  => "kota"]])
                        ->get($cekLaporan->id_laporanharian);
                    return $this->response(array(
                        "status"                => true,
                        "response_code"         => REST_Controller::HTTP_OK,
                        "response_message"      => "Laporan harian berhasil diubah",
                        "data"                  => $dataUpdate
                    ), REST_Controller::HTTP_OK);
                } else {
                    return $this->response(array(
                        "status"                => true,
                        "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                        "response_message"      => "Laporan harian gagal di edit!",
                        "data"                  => null
                    ), REST_Controller::HTTP_OK);
                }
            } else {
                return $this->response(array(
                    "status"                => true,
                    "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                    "response_message"      => "Laporan harian tidak dapat di edit",
                    "data"                  => null
                ), REST_Controller::HTTP_OK);
            }
        } else {
            return $this->response(array(
                "status"                => true,
                "response_code"         => REST_Controller::HTTP_EXPECTATION_FAILED,
                "response_message"      => "Laporan harian tidak ditemukan",
                "data"                  => null
            ), REST_Controller::HTTP_OK);
        }
    }
}
