<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_create_table_kategori_barang extends CI_Migration
{

    public function up()
    {
        $field = [
            'id_kategori_barang'    => array(
                'type'              => 'INT',
                'constraint'        => 11,
                'unsigned'          => FALSE,
                'auto_increment'    => TRUE
            ),
            'nama_kategori_barang'  => array(
                'type'              => 'VARCHAR',
                'constraint'        => 50,
                'null'              => FALSE,
            ),
            'foto_kategori_barang'  => array(
                'type'              => 'TEXT',
                'null'              => TRUE,
            ),           
            'created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'deleted_at DATETIME NULL DEFAULT NULL'
        ];
        $this->dbforge->add_field($field);                         //? ADD FIELDS        
        $this->dbforge->add_key('id_kategori_barang', TRUE);       //? ADD PRIMARY KEY
        $attributes = array('ENGINE' => 'InnoDB');
        $this->dbforge->create_table('kategori_barang', TRUE, $attributes);

        //TODO : INSERT DATA DEFAULT
        $dataInsert = [
            [
                "nama_kategori_barang" => "Makanan",
                "foto_kategori_barang" => "chips.png"
            ],
            [
                "nama_kategori_barang" => "Minuman",
                "foto_kategori_barang" => "drink.png"
            ],
            [
                "nama_kategori_barang" => "Bodycare",
                "foto_kategori_barang" => "shampoo.png"
            ],
            [
                "nama_kategori_barang" => "Homecare",
                "foto_kategori_barang" => "spray.png"
            ],
            [
                "nama_kategori_barang" => "Alat Tulis",
                "foto_kategori_barang" => "sketch.png"
            ],
        ];
        $this->db->insert_batch("kategori_barang", $dataInsert);
    }

    public function down()
    {
        $this->dbforge->drop_table('kategori_barang');
    }
}
