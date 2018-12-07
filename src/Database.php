<?php

class Database{
    private $db_host = NULL;
    private $db_schema = NULL;
    private $db_user = NULL;
    private $db_password = NULL;

    function __construct($db_host, $db_schema, $db_user, $db_password){
        //todo
        //initialize member data using provided paramaters
        $this->db_host = $db_host;
        $this->db_schema = $db_schema;
        $this->db_user = $db_user;
        $this->db_password = $db_password;
    }

    function connect(){
        $conn = new PDO("mysql:host=$this->db_host;dbname=$this->db_schema", $this->db_user, $this->db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $conn;
    }
}