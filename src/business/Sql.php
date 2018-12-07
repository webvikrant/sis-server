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
    public function selectFromProgramWhereCodeOrNameLike($conn, $filter, $limit, $offset)
    {
        $sql = "SELECT id, code, name
        FROM program
        WHERE code like ? or name like ? limit " . $limit . " offset " . $offset;

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

    //================================================================================================================
    //semester
    //================================================================================================================

    //select all
    public function selectFromSemesterWhereCodeOrNameLike($conn, $filter, $limit, $offset)
    {
        $sql = "SELECT id, code, name
        FROM semester
        WHERE code like ? or name like ? limit " . $limit . " offset " . $offset;

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
}
