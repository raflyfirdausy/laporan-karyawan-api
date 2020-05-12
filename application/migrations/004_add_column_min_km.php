<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_column_min_km extends CI_Migration
{

    public function up()
    {
        //TODO : TABLE MASTER BARANG       
        $fields = array(
            'min_km_toko'        => array(
                'type'          => "INT",
                'constraint'    => 5,
                'default'       => 5,
                'null'          => FALSE,
                'after'         => "alamat_toko"
            ),            
        );
        $this->dbforge->add_column("toko", $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('toko', 'min_km_toko');        
    }
}
