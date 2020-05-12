<?php

class Kota_model extends Custom_model
{
    public $table           = 'kota';
    public $primary_key     = 'id_kota';
    public $soft_deletes    = TRUE;
    public $timestamps      = TRUE;
    public $return_as       = "array";

    public function __construct()
    {            
        parent::__construct();       
    }
}
