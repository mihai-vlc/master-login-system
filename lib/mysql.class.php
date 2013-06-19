<?php

/**
* Simple Mysqli class
* @author Mihai Ionut Vilcu (ionutvmi@gmail.com)
*
* ChangeLog
* 9-May-2013 - added mysql compatibility
*/
if(extension_loaded('mysqli')) {
    class dbConn {
        /**
         * @var $link stores the connection to the MySQL Server
         */
        var $link = null;
        /**
         * Makes the connection to the db
         * @param string $db_host 
         * @param string $db_user 
         * @param string $db_pass 
         * @param string $db_name 
         * @return bool
         */
        function __construct($db_host,$db_user,$db_pass,$db_name){
            
            $this->link = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
            
            if (!$this->link) die('Connect Error (' . mysqli_connect_errno() . ') '.mysqli_connect_error());
            
            mysqli_select_db($this->link, $db_name) or die(mysqli_error($this->link));
            
            return true;
        }
        /**
         * Adds the result of the query in an array
         * @param string $q the query to be executed
         * @return array
         */
        function select($q){
            $arr = false;
            $result = mysqli_query($this->link,$q);

            if(mysqli_num_rows($result) > 0)

                while($res = mysqli_fetch_object($result))
                
                    $arr[] = $res;
                
            if($arr) return $arr;
            
            return false;
        }
        /**
         * Returns an array with one row of the result
         * @param string $q the query to be executed
         * @return array
         */
        function get_row($q){
            $arr = false;
            $result = mysqli_query($this->link,$q);

            if(mysqli_num_rows($result) == 1)

            $arr = mysqli_fetch_object($result);
                            
            if($arr) return $arr;
            
            return false;
        }
        /**
         * Gets the number of rows in a result
         * @param string $q the query to be executed 
         * @return integer
         */
        function count($q){
            $result = mysqli_query($this->link,$q);

            return mysqli_num_rows($result);

        }
        /**
         * Executes the query
         * @param string $q the query to be executed
         * @return resource
         */
        function query($q){

            return mysqli_query($this->link,$q);

        }
        /**
         * Escapes the string
         * @param string $str 
         * @return string
         */
        function escape($str){

            return mysqli_real_escape_string($this->link,$str);

        }
        /**
         * Runs the query and returns the inserted id
         * @param string $q the query to be executed 
         * @return integer
         */
        function insert($q){

            if(mysqli_query($this->link,$q))
                return mysqli_insert_id($this->link); 
            return false;
        }
        /**
         * Runs an insert query which is formed based on the array
         * @param string $table - the name of the table where the data will be inserted
         * @param array $array - multidimentional array of the to build the insert query
         * @return type
         */
        function insert_array($table,$array){
            $q = "INSERT INTO `$table`";
            $q .=" (`".implode("`,`",array_keys($array))."`) ";
            $q .=" VALUES ('".implode("','",array_values($array))."') ";

            if(mysqli_query($this->link,$q))
                return mysqli_insert_id($this->link);
            return false;
        }
        /**
         * returns the error
         * @return string
         */
        function err() {
            return mysqli_error($this->link);
        }
    }
} else { // we use the old mysql
    class dbConn {
        var $link = null;

        function __construct($db_host,$db_user,$db_pass,$db_name){
            
            $this->link = @mysql_connect($db_host, $db_user, $db_pass);
            
            if (!$this->link) die('Connect Error (' . mysql_errno() . ') '.mysql_error());
            
            mysql_select_db($db_name, $this->link) or die(mysql_error($this->link));
            
            return true;
        }
        
        function select($q){
        
            $result = mysql_query($q, $this->link);

            if(mysql_num_rows($result) > 0)

                while($res = mysql_fetch_object($result))
                
                    $arr[] = $res;
                
            if($arr) return $arr;
            
            return false;
        }
        function get_row($q){
            $result = mysql_query($q, $this->link);

            if(mysql_num_rows($result) == 1)

            $arr = mysql_fetch_object($result);
                            
            if($arr) return $arr;
            
            return false;
        }
        function count($q){
            $result = mysql_query($q, $this->link);

            return mysql_num_rows($result);

        }

        function query($q){

            return mysql_query($q, $this->link);

        }

        function escape($str){

            return mysql_real_escape_string($str, $this->link);

        }
        function insert($q){

            if(mysql_query($q, $this->link))
                return mysql_insert_id($this->link);
            return false;
        }
        function insert_array($table,$array){
            $q = "INSERT INTO `$table`";
            $q .=" (`".implode("`,`",array_keys($array))."`) ";
            $q .=" VALUES ('".implode("','",array_values($array))."') ";

            if(mysql_query($q, $this->link))
                return mysql_insert_id($this->link);
            return false;
        }
        function err() {
            return mysql_error();
        }
    }


}