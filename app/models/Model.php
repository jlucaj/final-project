<?php

namespace app\models;

abstract class Model {

    protected function connect() {
        $string = "mysql:host=" . DBHOST . ";port=" . DBPORT . ";dbname=" . DBNAME . ";charset=utf8mb4";
        try {
            $con = new \PDO($string, DBUSER, DBPASS);
            $con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $con;
        } catch (\PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
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

    public function findAll() {
        $query = "SELECT * FROM $this->table";
        return $this->query($query);
    }
}
