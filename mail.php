<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: *");
include_once 'dbConnect.php';

class Mail {
    private $db;
    private $db_table = "users";

    public function __construct(){
        $this->db = new DbConnect();
    }

}
?>