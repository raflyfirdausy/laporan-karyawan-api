<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_beberapa_column extends CI_Migration
{

    public function up()
    {        
        //TODO : TABLE MASTER BARANG       
        $fields = array(
            'id_kategori_barang'=> array(
                'type'          => "INT",
                'constraint'    => 11,
                'null'          => TRUE,
                'after'         => "id_masterbarang"
            )    
        );
        $this->dbforge->add_column("master_barang", $fields);          
        $fields = array(
            'kode_barang'=> array(
                'type'          => "VARCHAR",
                'constraint'    => 20,
                'null'          => TRUE,
                'after'         => "id_kategori_barang"
            ),           
        );
        $this->dbforge->add_column("master_barang", $fields);


        //TODO : TABLE BARANG
        $fields = array(
            'id_kategori_barang'=> array(
                'type'          => "INT",
                'constraint'    => 11,
                'null'          => TRUE,
                'after'         => "id_toko"
            )
        );
        $this->dbforge->add_column("barang", $fields);  
        $fields = array(
            'kode_barang'=> array(
                'type'          => "VARCHAR",
                'constraint'    => 20,
                'null'          => TRUE,
                'after'         => "id_kategori_barang"
            ),  
            'hargadiskon_barang'=> array(
                'type'          => "INT",
                'constraint'    => 11,
                'null'          => TRUE,
                'after'         => "hargajual_barang"
            ),             
        );
        $this->dbforge->add_column("barang", $fields);      
        
        //TODO : ADD FOREIGN KEY
        $this->db->query('ALTER TABLE `master_barang` ADD FOREIGN KEY(`id_kategori_barang`) REFERENCES kategori_barang(`id_kategori_barang`)');
        $this->db->query('ALTER TABLE `barang` ADD FOREIGN KEY(`id_kategori_barang`) REFERENCES kategori_barang(`id_kategori_barang`)');
    }

    public function down()
    {
        $this->dbforge->drop_column('barang', 'hargadiskon_barang');
        $this->dbforge->drop_column('barang', 'kode_barang');
        $this->dbforge->drop_column('barang', 'id_kategori_barang');
        $this->dbforge->drop_column('master_barang', 'kode_barang');
        $this->dbforge->drop_column('master_barang', 'id_kategori_barang');
    }
}
