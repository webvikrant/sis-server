<?php
class Employee implements JsonSerializable{
    private $id = 0;
    private $name = null;

    function __construct($id, $name){
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(){
        return $this->id;
    }
    public function getName(){
        return $this->name;
    }
    
    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'name'=> $this->name
        ];
    }
}