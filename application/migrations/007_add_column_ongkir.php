<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_column_ongkir extends CI_Migration
{

    public function up()
    {        
        $fields = array(
            'ongkir_transaksi'    => array(
                'type'          => "INT",
                'constraint'    => 10,                
                'null'          => FALSE,
                'after'         => "nohp_transaksi"
            ),            
        );
        $this->dbforge->add_column("transaksi", $fields);        
    }

    public function down()
    {
        $this->dbforge->drop_column('transaksi', 'ongkir_transaksi');        
    }
}
