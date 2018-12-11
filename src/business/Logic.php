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
    //program
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
    //semester
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

    //================================================================================================================
    //enquiry
    //================================================================================================================

    //create new enquiry
    public function createEnquiry(&$errors, $name, $mobile, $programCode, $semesterCode)
    {
        $newEnquiryId = 0;

        //checks
        //mobile is valid
        //mobile already exists
        //programCode is valid
        //semesterCode is valid
        if (!mobileOk($mobile)) {
            $errors[] = "Mobile number missing or invalid.";
            return;
        }

        if (!isset($name)) {
            $errors[] = "Name missing.";
            return;
        }

        if (!isset($programCode)) {
            $errors[] = "Program code missing.";
            return;
        }

        if (!isset($semesterCode)) {
            $errors[] = "Semester code missing.";
            return;
        }

        try {
            $conn = $this->db->connect();
            $existingEnquiryCount = $this->sql->selectCountFromEnquiryWhereMobileEquals($conn, $mobile);
            if (isset($existingEnquiryCount) && $existingEnquiryCount == 1) {
                $errors[] = "Mobile number already exists.";
                return;
            }

            $program = $this->sql->selectFromProgramWhereCodeEquals($conn, $programCode);
            if(!isset($program)){
                $errors[] = "Incorrect program code.";
                return;
            }

            $semester = $this->sql->selectFromSemesterWhereCodeEquals($conn, $semesterCode);
            if(!isset($semester)){
                $errors[] = "Incorrect semester code.";
                return;
            }

            $sessionId = 10;//hardcoded - session for which registrations are now open

            // $conn->beginTransaction();
            $submittedOn = time();
            $enquiryId = $this->sql->selectMaxIdFromTable($conn, 'enquiry');
            $enquiryId++;
            $this->sql->insertIntoEnquiry($conn, $enquiryId, $sessionId, $program->getId(), $semester->getId(), $name, $mobile, $submittedOn);
            $newEnquiryId = $enquiryId;
            // $conn->commit();
        } catch (Exception $e) {
            $errors[] = $e->errorInfo[2];
        }

        return $newEnquiryId;
    }

}
