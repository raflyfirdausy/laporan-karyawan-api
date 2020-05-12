<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_column_kode_transaksi extends CI_Migration
{

    public function up()
    {
        // FORMAT KODE TRANSAKSI
        // B{id_user}-{id_toko}-{UNIX timestamp}
        $fields = array(
            'kode_transaksi'    => array(
                'type'          => "VARCHAR",
                'constraint'    => 100,                
                'null'          => FALSE,
                'after'         => "id_transaksi"
            ),            
        );
        $this->dbforge->add_column("transaksi", $fields);
        $this->db->query('ALTER TABLE `transaksi` ADD UNIQUE INDEX (`kode_transaksi`)');
    }

    public function down()
    {
        $this->dbforge->drop_column('transaksi', 'kode_transaksi');        
    }
}
