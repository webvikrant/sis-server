<?php

class ErrorMessage implements JsonSerializable
{
    private $code = 0;
    private $description = null;

    public function __construct($code)
    {
        $this->code = $code;
        $this->description = $this->generateDescription($code);
    }

    public function getCode()
    {
        return $this->code;
    }
    public function getDescription()
    {
        return $this->description;
    }

    public function jsonSerialize()
    {
        return [
            'code' => $this->code,
            'description' => $this->description,
        ];
    }

    private function generateDescription($code)
    {
        $description = null;
        switch ($code) {
            case EMPTY_REQUEST_BODY:
                $description = "Request body is empty. Attributes required: name, mobile, session, program, semester";
                break;

            case EMPTY_NAME:
                $description = "Name is blank";
                break;

            default:
                $description = "Unknown error";
                break;
        }
        return $description;
    }
}
