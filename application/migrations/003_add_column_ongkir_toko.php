<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_column_ongkir_toko extends CI_Migration
{

    public function up()
    {
        //TODO : TABLE MASTER BARANG       
        $fields = array(
            'ongkir_toko'        => array(
                'type'          => "INT",
                'constraint'    => 10,
                'default'       => 5000,
                'null'          => FALSE,
                'after'         => "alamat_toko"
            ),
            'ongkirtambahan_toko'=> array(
                'type'          => "INT",
                'constraint'    => 10,
                'default'       => 1000,
                'null'          => FALSE,
                'after'         => "ongkir_toko"
            ),
        );
        $this->dbforge->add_column("toko", $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('toko', 'ongkirtambahan_toko');
        $this->dbforge->drop_column('toko', 'ongkir_toko');
    }
}
