<?php

namespace VanillaAuth\Core;

use PDO;

class MysqlModel
{

    protected $db;
    protected $table;
    protected $primaryKey;
    protected $fetchType;
    public function __construct($dbConnection, $table, $primaryKey = "id", $fetchType = "FETCH_OBJ")
    {
        $this->db = $dbConnection;
        $this->table = $table;
        $this->primaryKey = $primaryKey;
        $this->fetchType = constant("PDO::$fetchType");
    }
    public function insert($data)
    {
        $keys = [];
        $values = [];
        //create custom columns and values strings for dynamic insertion
        foreach ($data as $key => $value) {
            $keys[] = $key;
            $values[] = "'" . $value . "'";
        }
        $keys =  implode(",", $keys);
        $values =  implode(",", $values);
        $query = "INSERT INTO " . $this->table . " (" . $keys . ") VALUES (" . $values . ") ;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $this->db->lastInsertId();
    }
    public function update($id, $data)
    {
        $set = "";
        //create custom set string for dynamic update
        foreach ($data as $key => $value) {
            $set .= $key . "='" . $value . "',";
        }
        $set = substr($set, 0, -1);
        $query = "UPDATE " . $this->table . " SET " . $set . " WHERE id=" . $id . " ;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
    }
    public function getOne($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id=" . $id . " and deleted!=1 ;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch($this->fetchType);
        return $result;
    }
    public function getAll()
    {
        $query = "SELECT * FROM " . $this->table . " ;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll($this->fetchType);
    }
    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id=" . $id . ";";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
    }

    protected function execute($query)
    {
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    protected function fetchAll($query)
    {
        $stmt = $this->execute($query);
        return $stmt->fetchAll($this->fetchType);
    }
    protected function fetchOne($query)
    {
        $stmt = $this->execute($query);
        $result = $stmt->fetchAll($this->fetchType);
        if (count($result) > 0) {
            return $result[0];
        }
        return false;
    }
}
