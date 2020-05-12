<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_relasi_di_table_transaksi extends CI_Migration
{

    public function up()
    {        
         //TODO : ADD FOREIGN KEY
         $this->db->query('ALTER TABLE `transaksi` ADD FOREIGN KEY(`id_user`) REFERENCES user(`id_user`)');
         $this->db->query('ALTER TABLE `transaksi` ADD FOREIGN KEY(`id_toko`) REFERENCES toko(`id_toko`)');
    }

    public function down()
    {
        
    }
}
