<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_column_nohp_transaksi extends CI_Migration
{

    public function up()
    {
        //TODO : TABLE MASTER BARANG       
        $fields = array(
            'nohp_transaksi'        => array(
                'type'          => "VARCHAR",
                'constraint'    => 20,
                'default'       => NULL,
                'null'          => TRUE,
                'after'         => "alamatdetail_transaksi"
            ),            
        );
        $this->dbforge->add_column("transaksi", $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('transaksi', 'nohp_transaksi');        
    }
}
