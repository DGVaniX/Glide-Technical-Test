<?php
    require "libs/simple_html_dom.php";

    class Script extends Dbh {
        public function __construct(){
            $this->connect();
        }
        
        public function fetchData(){
            $date = date('Y-m-d');

            $POSIds = "PUBOBJ1660,PUBOB4507,PUBOB4508,PUBOB4510,PUBOB4509,PUBOB4511,PUBOB4512,PUBOB4513,PUBOB4514,PUBOB4515,PUBOB4516,PUBOB4517,PUBOB4518,PUBOB4519,PUBOB4521,PUBOB4520,PUBOB4522,PUBOBJ1661,PUBOBJ1662";
            $dataUrl = "http://mip-prd-web.azurewebsites.net/CustomDataDownload?LatestValue=false&Applicable=applicableFor&FromUtcDatetime=2022-07-30T00:00:00.000Z&ToUtcDateTime=".$date."T00:00:00.000Z&PublicationObjectStagingIds=".$POSIds;
            $html = file_get_html($dataUrl);
            $this->emptyData();

            foreach($html->find("tbody tr") as $tr){
                $row = array();
                foreach($tr->find("td") as $td){
                    $row[] = $td->plaintext;
                }
                $this->insertData($row);
            }
            return true;
        }

        public function totalRecords(){
            $numOfRecords = $this->countRecords();
            return $numOfRecords;
        }

        public function getData(){
            $sql = "SELECT * FROM data";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll();
            return $data;
        }
        
        public function getCalValue($id){
            $sql = "SELECT * FROM data WHERE id = :id";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([":id" => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        }

        public function getValueByDateRange($start, $end){
            $sql = "SELECT * FROM data WHERE applicableDate BETWEEN :start AND :end";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([":start" => $start, ":end" => $end]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        }
        
        public function getAverageValueByDateRange($start, $end){
            $sql = "SELECT AVG(value) AS average FROM data WHERE applicableDate BETWEEN :start AND :end";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([":start" => $start, ":end" => $end]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['average'];
        }

        public function updateData($id, $date, $area, $value){
            $sql = "UPDATE data SET applicableDate = :date, area = :area, value = :value WHERE id = :id";
            $stmt = $this->connect()->prepare($sql);
            $stmt->execute([":id" => $id, ":date" => $date, ":area" => $area, ":value" => $value]);         
        }

        public function removeData($id){
            $stmt1 = $this->connect()->prepare('INSERT INTO deleted_data SELECT *, :deletionDate AS deletion_date FROM data WHERE id=:id');
            $stmt2 = $this->connect()->prepare('DELETE FROM data WHERE id=:id');
            $result = $stmt1->execute([':id' => $id, ':deletionDate' => date('Y-m-d H:i:s')]) && $stmt2->execute([':id' => $id]);
            
            return $result;
        }

        public function restoreData($id){
            $stmt1 = $this->connect()->prepare('INSERT INTO data SELECT * FROM deleted_data WHERE id=:id');
            $stmt2 = $this->connect()->prepare('DELETE FROM deleted_data WHERE id=:id');
            $result = $stmt1->execute([':id' => $id]) && $stmt2->execute([':id' => $id]);
            
            return $result;
        }
    }
?>
