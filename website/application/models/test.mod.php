<?php
    class testModel extends Model{

        public function __construct(){
            parent::__construct();
        }
        protected $dbconfig = 'test';
        protected $table = 'test';


    }
