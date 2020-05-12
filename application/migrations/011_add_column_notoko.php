<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_column_notoko extends CI_Migration
{

    public function up()
    {        
        $fields = array(
            'no_toko'    => array(
                'type'          => "VARCHAR",
                'constraint'    => 20,                
                'default'       => NULL,         
                'null'          => TRUE,
                'after'         => "alamat_toko"
            ),            
        );
        $this->dbforge->add_column("toko", $fields);        
    }

    public function down()
    {
        $this->dbforge->drop_column('toko', 'no_toko');        
    }
}
