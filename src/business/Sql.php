<?php
require_once 'entities/Program.php';
require_once 'entities/Semester.php';
require_once 'entities/Enquiry.php';
require_once 'entities/Session.php';

class Sql
{
    public function __construct()
    {
    }

    //================================================================================================================
    //unique column aliases for each table
    //================================================================================================================
    private function getColumns($table)
    {
        $columns = null;

        switch ($table) {
            case "session":
                $columns = ["id", "name"];
                break;

            case "program":
                $columns = ["id", "code", "name"];
                break;

            case "semester":
                $columns = ["id", "code", "name"];
                break;

            case "enquiry":
                $columns = ["id", "session_id", "program_id", "semester_id", "name", "mobile", "submitted_on"];
                break;

            default:
                $columns = [];
                break;
        }

        $aliases = $this->generateAliases($table, $columns);
        return $aliases;
    }

    private function generateAliases($table, $columns)
    {
        $aliases = "";
        $count = 0;
        foreach ($columns as $column) {
            $count++;
            $alias = " " . $table . "." . $column . " as " . $this->generateAlias($table, $column) . " ";
            if ($count == 1) {
                $aliases = $alias;
            } else {
                $aliases = $aliases . "," . $alias;
            }
        }
        return $aliases;
    }

    private function generateAlias($table, $column)
    {
        return $table . _ . $column;
    }
    //================================================================================================================
    //create object from row
    //================================================================================================================

    //session
    private function loadSessionFromRow($row)
    {
        $table = "session";
        $session = new Session(
            $row[$this->generateAlias($table, "id")],
            $row[$this->generateAlias($table, "name")]);

        return $session;
    }

    //program
    private function loadProgramFromRow($row)
    {
        $table = "program";
        $program = new Program(
            $row[$this->generateAlias($table, "id")],
            $row[$this->generateAlias($table, "code")],
            $row[$this->generateAlias($table, "name")]);

        return $program;
    }

    //semester
    private function loadSemesterFromRow($row)
    {
        $table = "semester";
        $semester = new Semester(
            $row[$this->generateAlias($table, "id")],
            $row[$this->generateAlias($table, "code")],
            $row[$this->generateAlias($table, "name")]);

        return $semester;
    }

    //enquiry
    private function loadEnquiryFromRow($row)
    {
        $session = $this->loadSessionFromRow($row);
        $program = $this->loadProgramFromRow($row);
        $semester = $this->loadSemesterFromRow($row);

        $table = "enquiry";

        $enquiry = new Enquiry(
            $row[$this->generateAlias($table, "id")],
            $session,
            $program,
            $semester,
            $row[$this->generateAlias($table, "name")],
            $row[$this->generateAlias($table, "mobile")],
            $row[$this->generateAlias($table, "submitted_on")]
        );

        return $enquiry;
    }

    //================================================================================================================
    //max id in a table
    //================================================================================================================

    //tables- department, slot, category, task, employee, post

    public function selectMaxIdFromTable($conn, $table)
    {
        $sql = "SELECT max(id) FROM $table;";

        $stmt = $conn->prepare($sql);

        $maxId = 0;
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $maxId = $row[0];
        }

        if (!isset($maxId)) {
            $maxId = 0;
        }
        return $maxId;
    }

    //================================================================================================================
    //session
    //================================================================================================================

    //select one
    public function selectFromSessionWhereIdEquals($conn, $id)
    {
        $columnList = $this->getColumns("session");

        $sql = "SELECT $columnList
        FROM session
        WHERE id = ?";

        $stmt = $conn->prepare($sql);

        $params = [$id];
        $stmt->execute($params);

        $session = null;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $session = $this->loadSessionFromRow($row);
        }
        return $session;
    }

    //================================================================================================================
    //program
    //================================================================================================================

    //select
    public function selectFromProgramWhereCodeOrNameLike($conn, $filter, $limit, $offset)
    {
        $columnList = $this->getColumns("program");

        $sql = "SELECT $columnList
        FROM program
        WHERE program.code like ? or program.name like ? limit " . $limit . " offset " . $offset;

        $stmt = $conn->prepare($sql);

        $params = [$filter, $filter];
        $stmt->execute($params);

        $programs = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $program = $this->loadProgramFromRow($row);
            $programs[] = $program;
        }
        return $programs;
    }

    public function selectFromProgramWhereCodeEquals($conn, $code)
    {
        $columnList = $this->getColumns("program");

        $sql = "SELECT $columnList
        FROM program
        WHERE program.code = ?";

        $stmt = $conn->prepare($sql);

        $params = [$code];
        $stmt->execute($params);

        $program = null;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $program = $this->loadProgramFromRow($row);
        }
        return $program;
    }

    //================================================================================================================
    //semester
    //================================================================================================================

    //select
    public function selectFromSemesterWhereCodeOrNameLike($conn, $filter, $limit, $offset)
    {
        $columnList = $this->getColumns("semester");

        $sql = "SELECT $columnList
        FROM semester
        WHERE semester.code like ? or name like ? limit " . $limit . " offset " . $offset;

        $stmt = $conn->prepare($sql);

        $params = [$filter, $filter];
        $stmt->execute($params);

        $semesters = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $semester = $this->loadSemesterFromRow($row);
            $semesters[] = $semester;
        }
        return $semesters;
    }

    public function selectFromSemesterWhereCodeEquals($conn, $code)
    {
        $columnList = $this->getColumns("semester");

        $sql = "SELECT $columnList
        FROM semester
        WHERE semester.code = ?";

        $stmt = $conn->prepare($sql);

        $params = [$code];
        $stmt->execute($params);

        $semester = null;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $semester = $this->loadSemesterFromRow($row);
        }
        return $semester;
    }

    //================================================================================================================
    //enquiry
    //================================================================================================================

    //insert
    public function insertIntoEnquiry($conn, $id, $sessionId, $programId, $semesterId, $name, $mobile, $submittedOn)
    {
        //convert numeric dates to string dates
        $sql = "INSERT INTO enquiry (id, session_id, program_id, semester_id,  name, mobile, submitted_on)
        VALUES (?,?,?,?,?,?,?);";

        $stmt = $conn->prepare($sql);
        $params = [$id, $sessionId, $programId, $semesterId, $name, $mobile, $submittedOn];
        $stmt->execute($params);
    }

    //select one
    public function selectCountFromEnquiryWhereMobileEquals($conn, $mobile)
    {
        $sql = "SELECT id
        FROM enquiry
        WHERE mobile = ?";

        $stmt = $conn->prepare($sql);

        $params = [$mobile];
        $stmt->execute($params);

        $count = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count++;
        }
        return $count;
    }

    //select one
    public function selectFromEnquiryWhereIdMobileEquals($conn, $id, $mobile)
    {
        $sessionColumnList = $this->getColumns("session");
        $programColumnList = $this->getColumns("program");
        $semesterColumnList = $this->getColumns("semester");
        $enquiryColumnList = $this->getColumns("enquiry");

        $sql = "SELECT $sessionColumnList, $programColumnList, $semesterColumnList, $enquiryColumnList
        FROM enquiry
        LEFT JOIN session on enquiry.session_id = session.id
        LEFT JOIN program on enquiry.program_id = program.id
        LEFT JOIN semester on enquiry.semester_id = semester.id
        WHERE enquiry.id = ? and enquiry.mobile = ?";

        $stmt = $conn->prepare($sql);

        $params = [$id, $mobile];
        $stmt->execute($params);

        $enquiry = null;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $enquiry = $this->loadEnquiryFromRow($row);
        }
        return $enquiry;
    }

    //select one
    public function selectFromEnquiryWhereIdEquals($conn, $id)
    {
        $sessionColumnList = $this->getColumns("session");
        $programColumnList = $this->getColumns("program");
        $semesterColumnList = $this->getColumns("semester");
        $enquiryColumnList = $this->getColumns("enquiry");

        $sql = "SELECT $sessionColumnList, $programColumnList, $semesterColumnList, $enquiryColumnList
        FROM enquiry
        LEFT JOIN session on enquiry.session_id = session.id
        LEFT JOIN program on enquiry.program_id = program.id
        LEFT JOIN semester on enquiry.semester_id = semester.id
        WHERE enquiry.id = ?";

        $stmt = $conn->prepare($sql);

        $params = [$id];
        $stmt->execute($params);

        $enquiry = null;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $enquiry = $this->loadEnquiryFromRow($row);
        }
        return $enquiry;
    }

    //update
    public function updateEnquirySetProgram($conn, $id, $programId)
    {
        //convert numeric dates to string dates
        $sql = "update enquiry set program_id = ? where id = ?";

        $stmt = $conn->prepare($sql);
        $params = [$programId, $id];
        $stmt->execute($params);
    }
}
