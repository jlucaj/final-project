<?php

namespace app\models;

abstract class Model {

    public function findAll() {
        $query = "select * from $this->table";
        return $this->query($query);
    }

    private function connect() {
        $string = "mysql:hostname=" . DBHOST . ";dbname=" . DBNAME;
        $con = new \PDO($string, DBUSER, DBPASS);
        return $con;
    }

    public function query($query, $data = []) {
        try {
            $con = $this->connect();
            $stm = $con->prepare($query);
            $check = $stm->execute($data);
    
            if (!$check) {
                $error = $stm->errorInfo();
                return ['error' => $error];
            }
    
            $result = $stm->fetchAll(\PDO::FETCH_ASSOC);
            return count($result) ? $result : true;
        } catch (\PDOException $e) {
            return ['exception' => $e->getMessage()];
        }
    }
    
    

}
