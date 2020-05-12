<?php

class User_model extends Custom_model
{
    public $table           = 'user';
    public $primary_key     = 'id_user';
    public $soft_deletes    = TRUE;
    public $timestamps      = TRUE;
    public $return_as       = "object";

    public function __construct()
    {            
        parent::__construct();
        $this->has_one['key'] = array(
            'foreign_model'     => 'Rest_keys_model',
            'foreign_table'     => 'rest_keys',
            'foreign_key'       => 'id_user',
            'local_key'         => 'id'
        );
    }
}
