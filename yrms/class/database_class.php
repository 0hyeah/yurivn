<?php
class Database
{
    protected $_vbulletin;
    protected $_tableName;

    function __construct()
    {
        global $vbulletin;
        $this->_vbulletin = $vbulletin;
    }

    public function setTable($tableName)
    {
        $this->_tableName = $tableName;
        return $this;
    }

    public function fetchAll($query)
    {
        $result = $this->_vbulletin->db->query_read($query);

        if(!empty($result)) {
            $data = array();
            while($row = $this->_vbulletin->db->fetch_array($result)){
                $data[] = $row;
            }
        }

        return $data;
    }

    public function fetchOnce($query)
    {
        $result = $this->_vbulletin->db->query_read($query);

        $data = array();
        if(!empty($result)) {
            $data = $this->_vbulletin->db->fetch_array($result);
        }

        return $data;
    }
    public function getTotal($condition = '') {
        $result = $this->_vbulletin->db->query_read("SELECT COUNT(*) FROM $this->_tableName $condition");
        $row = $this->_vbulletin->db->fetch_row($result);
        return $row[0];
    }

    public function query($query)
    {
        $this->_vbulletin->db->query_write($query);
    }

    public function insert($data)
    {
        $table_field = array();
        $table_value = array();
        foreach($data as $field => $value){
            $table_field[] = "`$field`";
            $table_value[] = "'$value'";
        }
        $table_field = implode(",", $table_field);
        $table_value = implode(",", $table_value);
        $query= "INSERT INTO `$this->_tableName`($table_field) VALUES($table_value)";
        $this->query($query);
    }

    public function update($data, $condition)
    {
        $table_newinfo = array();
        foreach($data as $field => $value){
            $table_newinfo[]="`$field` = '$value'";
        }
        $table_newinfo = implode(",", $table_newinfo);
        $query= "UPDATE `$this->_tableName` SET $table_newinfo WHERE $condition";
        $this->query($query);
    }

    public function insert_id()
    {
        return $this->_vbulletin->db->insert_id();
    }
}