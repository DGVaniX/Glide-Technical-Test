<?php
    class Dbh {
        private $host = "127.0.0.1";
        private $user = "root";
        private $pwd = "";
        private $dbName = "site";

        protected function connect(){
            $con = "mysql:host=".$this->host.";dbname=".$this->dbName;
            $pdo = new PDO($con, $this->user, $this->pwd);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            return $pdo;
        }

        public function countRecords(){
            $sql = "SELECT COUNT(*) AS count FROM data";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        }

        protected function insertData($data){
            $sql = "INSERT INTO data (applicableDate, area, value) VALUES (STR_TO_DATE(:applicableDate,'%d/%m/%Y'), :area, :value)";
            $stmt = $this->connect()->prepare($sql);
            
            $area = preg_replace("/[^\(]*\((.*)\)[^\)]*/", "$1", $data[2]);

            $stmt->execute([":applicableDate" => $data[1], ":area" => $area, ":value" => (float) $data[3]]);
        }

        protected function emptyData(){
            $sql = "TRUNCATE TABLE data";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
        }
    }
?>