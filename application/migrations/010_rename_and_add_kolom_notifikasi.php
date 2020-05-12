<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_rename_and_add_kolom_notifikasi extends CI_Migration
{

    public function up()
    {        
        $fields = array(
            'judul_notifikasi'  => array(
                'name'          => "title_notifikasi",
                'type'          => "VARCHAR",
                'constraint'    => 100
            ),            
        );
        $this->dbforge->modify_column("notifikasi", $fields); 
        
        $fields = array(
            'content_aplikasi'  => array(
                'name'          => "message_notifikasi",
                'type'          => "TEXT",                
            ),            
        );
        $this->dbforge->modify_column("notifikasi", $fields);   

        $fields = array(
            'gambar_notifikasi'  => array(
                'name'          => "image_notifikasi",
                'type'          => "TEXT",   
                'default'       => NULL,         
                'null'          => TRUE,
            ),            
        );
        $this->dbforge->modify_column("notifikasi", $fields); 

        $this->dbforge->drop_column('notifikasi', 'jenis_notifikasi'); 
        
        $fields = array(
            'id_user'           => array(
                'type'          => "INT",
                'constraint'    => 11,       
                'default'       => NULL,         
                'null'          => TRUE,
                'after'         => "id_notifikasi"
            ),            
        );
        $this->dbforge->add_column("notifikasi", $fields);   

        $this->db->query('ALTER TABLE `notifikasi` ADD FOREIGN KEY(`id_user`) REFERENCES user(`id_user`)');
    }

    public function down()
    {
        //NOTHING TODO :P  
    }
}
