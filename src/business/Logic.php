<?php
require_once '../Database.php';
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
    public function getAllPrograms($user, $requestBody)
    {
        $outputJson = null;

        //no need to verify user, any body can call this function

        $programs = null;
        try {
            $conn = $this->db->connect();
            $programs = $this->sql->selectFromProgram($conn);
            $outputJson = [
                "ok" => true,
                "data" => $programs,
            ];
        } catch (Exception $e) {
            $outputJson = [
                "ok" => false,
                "error" => "Exception",
                "description" => $e,
            ];
        }
        return $outputJson;
    }

    public function getProgramsWhereCodeLike($user, $requestBody)
    {
        $outputJson = null;

        //no need to verify user, any body can call this function

        if(!isset($requestBody)){
            $outputJson = [
                "ok" => false,
                "error" => "Missing body",
                "description" => "Request body is empty.",
            ];
            return $outputJson;
        }

        $code = $requestBody["code"];
        if(!isset($code)){
            $outputJson = [
                "ok" => false,
                "error" => "Missing attribute",
                "description" => "Attribute 'code' is missing in request body.",
            ];
            return $outputJson;
        }

        $programs = null;
        try {
            $conn = $this->db->connect();
            $programs = $this->sql->selectFromProgramWhereCodeLike($conn, $code);
            $outputJson = [
                "ok" => true,
                "data" => $programs,
            ];
        } catch (Exception $e) {
            $outputJson = [
                "ok" => false,
                "error" => "Exception",
                "description" => $e,
            ];
        }
        return $outputJson;
    }

    //================================================================================================================
    //semesters
    //================================================================================================================

    //get all semesters
    public function getAllSemesters($user, $requestBody)
    {
        $outputJson = null;

        //no need to verify user, any body can call this function

        $semesters = null;
        try {
            $conn = $this->db->connect();
            $semesters = $this->sql->selectFromSemester($conn);
            $outputJson = [
                "ok" => true,
                "data" => $semesters,
            ];
        } catch (Exception $e) {
            $outputJson = [
                "ok" => false,
                "error" => "Exception",
                "description" => $e,
            ];
        }
        return $outputJson;
    }

}
