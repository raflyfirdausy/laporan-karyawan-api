<?php

class Outlet_model extends Custom_model
{
    public $table           = 'outlet';
    public $primary_key     = 'id_outlet';
    public $soft_deletes    = TRUE;
    public $timestamps      = TRUE;
    public $return_as       = "array";

    public function __construct()
    {
        parent::__construct();
        $this->has_one['kota'] = array(
            'foreign_model'     => 'Kota_model',
            'foreign_table'     => 'kota',
            'foreign_key'       => 'id_kota',
            'local_key'         => 'id_kota'
        );        
    }
}
