<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_rename_and_add_kolom_detail_transaksi extends CI_Migration
{

    public function up()
    {        
        $fields = array(
            'id_detaltransaksi'    => array(
                'name'          => "id_detailtransaksi",
                'type'          => "INT",
                'constraint'    => 11
            ),            
        );
        $this->dbforge->modify_column("detail_transaksi", $fields);   
        
        $fields = array(
            'hargadiskon_barang'    => array(
                'type'          => "INT",
                'constraint'    => 11,       
                'default'       => NULL,         
                'null'          => TRUE,
                'after'         => "hargajual_barang"
            ),            
        );
        $this->dbforge->add_column("detail_transaksi", $fields);   
    }

    public function down()
    {
        $this->dbforge->drop_column('detail_transaksi', 'hargadiskon_barang');        
    }
}
