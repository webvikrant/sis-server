<?php
class Enquiry implements JsonSerializable{
    private $id = 0;
    private $session = null;
    private $program = null;
    private $semester = null;
    private $name = null;
    private $mobile = null;
    private $submittedOn = 0;

    function __construct($id, $session, $program, $semester, $name, $mobile, $submittedOn){
        $this->id = $id;
        $this->session = $session;
        $this->program = $program;
        $this->semester = $semester;
        $this->name = $name;
        $this->mobile = $mobile;
        $this->submittedOn = $submittedOn;
    }

    public function getId(){
        return $this->id;
    }

    public function getSession(){
        return $this->session;
    }

    public function getProgram(){
        return $this->program;
    }

    public function getSemester(){
        return $this->semester;
    }

    public function getName(){
        return $this->name;
    }

    public function getMobile(){
        return $this->mobile;
    }

    public function getSubmittedOn(){
        return $this->submittedOn;
    }
    
    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'session' => $this->session,
            'program' => $this->program,
            'semester' => $this->semester,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'submittedOn'=> $this->submittedOn
        ];
    }
}