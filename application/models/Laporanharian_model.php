<?php

class Laporanharian_model extends Custom_model
{
    public $table           = 'laporanharian';
    public $primary_key     = 'id_laporanharian';
    public $soft_deletes    = TRUE;
    public $timestamps      = TRUE;
    public $return_as       = "array";

    public function __construct()
    {
        parent::__construct();
        $this->has_one['user'] = array(
            'foreign_model'     => 'User_model',
            'foreign_table'     => 'user',
            'foreign_key'       => 'id_user',
            'local_key'         => 'id_user',            
        );
        $this->has_one['outlet'] = array(
            'foreign_model'     => 'Outlet_model',
            'foreign_table'     => 'outlet',
            'foreign_key'       => 'id_outlet',
            'local_key'         => 'id_outlet'
        );
    }
}
