<?php
require_once '../src/Database.php';
require_once 'Sql.php';

class Logic
{

    private $db;
    private $sql;

    public function __construct()
    {
        $this->db = new Database(DB_HOST, DB_SCHEMA, DB_USER, DB_PASSWORD);
        $this->sql = new Sql();
    }

    //================================================================================================================
    //programs
    //================================================================================================================

    //get all programs
    public function getAllPrograms(&$errors, $filter, $limit, $offset)
    {
        $programs = null;

        if (!limitOk($errors, $limit) || !offsetOk($errors, $offset)) {
            return;
        }

        if (!isset($filter)) {
            $filter = "";
        }

        $filter = "%" . $filter . "%";

        try {
            $conn = $this->db->connect();
            $programs = $this->sql->selectFromProgramWhereCodeOrNameLike($conn, $filter, $limit, $offset);
        } catch (Exception $e) {
            $errors[] = $e->errorInfo[2];
        }

        return $programs;
    }

    //================================================================================================================
    //semesters
    //================================================================================================================

    //get all semesters
    public function getAllSemesters(&$errors, $filter, $limit, $offset)
    {
        $semesters = null;

        if (!limitOk($errors, $limit) || !offsetOk($errors, $offset)) {
            return;
        }

        if (!isset($filter)) {
            $filter = "";
        }

        $filter = "%" . $filter . "%";

        try {
            $conn = $this->db->connect();
            $semesters = $this->sql->selectFromSemesterWhereCodeOrNameLike($conn, $filter, $limit, $offset);
        } catch (Exception $e) {
            $errors[] = $e->errorInfo[2];
        }

        return $semesters;
    }

}
