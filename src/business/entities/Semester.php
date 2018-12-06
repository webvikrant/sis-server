<?php
class Semester implements JsonSerializable{
    private $id = 0;
    private $code = null;
    private $name = null;

    function __construct($id, $code, $name){
        $this->id = $id;
        $this->code = $code;
        $this->name = $name;
    }

    public function getId(){
        return $this->id;
    }

    public function getCode(){
        return $this->code;
    }

    public function getName(){
        return $this->name;
    }
    
    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name'=> $this->name
        ];
    }
}