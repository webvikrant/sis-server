<?php
require_once 'entities/Program.php';
require_once 'entities/Semester.php';
class Sql
{
    public function __construct()
    {
    }

    //================================================================================================================
    //create object from row
    //================================================================================================================

    //program
    private function loadProgramFromRow($row)
    {
        $program = new Program($row['id'], $row['code'], $row['name']);
        return $program;
    }

    //semester
    private function loadSemesterFromRow($row)
    {
        $semester = new Semester($row['id'], $row['code'], $row['name']);
        return $semester;
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
    //program
    //================================================================================================================

    //select all
    public function selectFromProgram($conn)
    {
        $sql = "SELECT id, code, name
        FROM program";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $programs = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $program = $this->loadProgramFromRow($row);
            $programs[] = $program;
        }
        return $programs;
    }

    public function selectFromProgramWhereCodeLike($conn, $code)
    {
        $sql = "SELECT id, code, name
        FROM program
        WHERE code like ?";

        $stmt = $conn->prepare($sql);

        $code = "%" . $code . "%";
        $params = [$code];
        $stmt->execute($params);

        $programs = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $program = $this->loadProgramFromRow($row);
            $programs[] = $program;
        }
        return $programs;
    }

    // public function selectFromSlotWhereIdEquals($conn, $id)
    // {
    //     $sql = "SELECT slt.id as slt_id, slt.code as slt_code, slt.begins_at as slt_begins_at, slt.ends_at as slt_ends_at
    //     FROM slot slt
    //     WHERE slt.id = ?;";

    //     $stmt = $conn->prepare($sql);
    //     $params = [$id];
    //     $stmt->execute($params);

    //     $slot = null;
    //     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    //         $slot = $this->loadSlotFromRow($row);
    //     }
    //     return $slot;
    // }

    //================================================================================================================
    //semester
    //================================================================================================================

    //select all
    public function selectFromSemester($conn)
    {
        $sql = "SELECT id, code, name
        FROM semester";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $semesters = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $semester = $this->loadSemesterFromRow($row);
            $semesters[] = $semester;
        }
        return $semesters;
    }

}
