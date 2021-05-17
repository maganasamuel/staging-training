<?php
/**
 DBClass.php

 @author: Gio
 @desc: Base class for database manipulation
*/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Pacific/Auckland');

class DB {
    var $host = ""; // db server
    var $dbName = ""; // database name
    var $username = ""; // db server username
    var $password = ""; // db server password

    var $mysqli; // connection

    /**
        @desc: init the class
    */
    public function __construct () {
        $conf_array = parse_ini_file("conf/conf.ini");
		$this->host = $conf_array["host"];
		$this->username = $conf_array["username"];
		$this->password = $conf_array["password"];
		$this->dbName = $conf_array["database"];
		
        $this->connect();
    }

    /**
        @desc: Activate database connection
    */
    public function connect() {
        $this->mysqli = new mysqli($this->host,
                    $this->username,
                    $this->password,
                    $this->dbName) or die (error_log("[init] Database connection failed: ", 0));
        $this->mysqli->set_charset("utf8");
    }

    /**
        @desc: Returns the prepared statement
    */
    public function prepare (
        $query
    ) {
        //error_log("[STATEMENT] $query", 0);
        $statement = $this->mysqli->prepare($query) or die("[prepare error]" . $this->mysqli->error);
        return $statement;
    }

    /**
        @desc: Execute the given query
    */
    public function execute (
        $statement
    ) {
        $statement->execute() or die("[execute error]" . $this->mysqli->error);
        $dataset = $statement->get_result();
        $statement->close();
        return $dataset;
    }


    /**
        @desc: returns the formatted message array if there's
        no data return from the statement executed
    */
    public function ifEmpty (
        $message
    ) {
        return array("message"=> $message);
    }


    /**
        @desc: Clean data input to prevent SQL injection
    */
    public function clean (
        $input
    ) {
        $cleanInput = Array();
        if (is_array($input)) {
            foreach ($input as $k => $v) {
                $cleanInput[$k] = $this->clean($v);
            }
        } else {
            $cleanInput = $this->mysqli->real_escape_string($input);
        }
        return $cleanInput;
    }
}

?>