<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

defined('APP_CREDENTIAL')       or define('APP_CREDENTIAL', 'laporanKaryawanMantap');
defined('APP_SCREET')           or define('APP_SCREET', md5(APP_CREDENTIAL));

//? FOR CREDENTIAL
defined('LEVEL_KARYAWAN')       or define('LEVEL_KARYAWAN', 1);
defined('LEVEL_ADMIN')          or define('LEVEL_ADMIN', 2);

//? FOR LAPORAN
defined('LAPORAN_BELUM')        or define('LAPORAN_BELUM', 0);
defined('LAPORAN_DITERIMA')     or define('LAPORAN_DITERIMA', 1);
defined('LAPORAN_DITOLAK')      or define('LAPORAN_DITOLAK', 2);
