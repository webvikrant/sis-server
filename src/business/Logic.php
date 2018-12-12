<?php
require_once '../src/Database.php';
require_once 'Sql.php';
use \Firebase\JWT\JWT as JWT;

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
    //auth
    //================================================================================================================

    //admission candidate
    public function authenticateEnquiry(&$errors, $enquiryId, $mobile)
    {
        $jwt = null;

        if (!isset($enquiryId)) {
            $errors[] = "Enquiry Id missing.";
            return;
        }

        if (!mobileOk($mobile)) {
            $errors[] = "Mobile number missing or invalid.";
            return;
        }

        try {
            $conn = $this->db->connect();
            $enquiry = $this->sql->selectFromEnquiryWhereIdMobileEquals($conn, $enquiryId, $mobile);
            if (!isset($enquiry)) {
                $errors[] = "Incorrect enquiry id or mobile";
            } else {
                //generate a JWT
                $issuedAt = time();
                $expirationTime = $issuedAt + (7 * 24 * 3600); // jwt valid for 1 week from the issued time
                $payload = [
                    "userType" => USER_TYPE_ADMISSION_CANDIDATE,
                    "enquiryId" => $enquiry->getId(),
                    "iat" => $issuedAt,
                    "exp" => $expirationTime,
                ];
                $key = JWT_SECRET;
                $alg = "HS256";
                $jwt = JWT::encode($payload, $key, $alg);

            }
        } catch (Exception $e) {
            $errors[] = $e->errorInfo[2];
        }

        return $jwt;
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
            if (!isset($program)) {
                $errors[] = "Incorrect program code.";
                return;
            }

            $semester = $this->sql->selectFromSemesterWhereCodeEquals($conn, $semesterCode);
            if (!isset($semester)) {
                $errors[] = "Incorrect semester code.";
                return;
            }

            $sessionId = 10; //hardcoded - session for which registrations are now open

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

    //update enquiry
    public function updateEnquiry(&$errors, $jwt, $programCode)
    {
        $enquiry = null;

        //checks
        //security check
        if (!isset($jwt)) {
            $errors[] = "JWT missing";
            return;
        }

        //programCode is valid
        if (!isset($programCode)) {
            $errors[] = "Program code missing.";
            return;
        }

        try {
            $conn = $this->db->connect();

            $key = JWT_SECRET;
            $decoded = JWT::decode($jwt, $key, array("HS256"));

            $program = $this->sql->selectFromProgramWhereCodeEquals($conn, $programCode);
            if (!isset($program)) {
                $errors[] = "Incorrect program code.";
                return;
            }

            // $conn->beginTransaction();
            $submittedOn = time();
            $this->sql->updateEnquirySetProgram($conn, $enquiryId, $program->getId());
            $enquiry = $this->sql->selectFromEnquiryWhereIdEquals($conn, $enquiryId);
            // $conn->commit();
        } catch (Exception $e) {
            $errors[] = $e->errorInfo[2];
        }

        return $enquiry;
    }

}
