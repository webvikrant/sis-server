<?php

use Slim\Http\Request;
use Slim\Http\Response;

// this is the endpoint for students to register themseleves for taking admission into
// various programs of the college for the designated academic session.

//attributes required for successful registration
//name, mobile, program, semester

$app->post("/api/admissions/register-new-enquiry", function (Request $request, Response $response, array $args) {

    $this->logger->info("/api/admissions/register-new-enquiry");

    //security check

    //retrieve parameters from request body
    $requestBody = json_decode($request->getBody(), true);

    $name = $requestBody["name"];
    $mobile = $requestBody["mobile"];
    $programCode = $requestBody["programCode"];
    $semesterCode = $requestBody["semesterCode"];

    $errors = null;

    $logic = new Logic();
    $enquiryId = $logic->createEnquiry($errors, $name, $mobile, $programCode, $semesterCode);

    if (isset($errors)) {
        $outputJson = [
            "ok" => false,
            "errors" => $errors,
        ];
    } else {
        $outputJson = [
            "ok" => true,
            "data" => [
                "enquiryId" => $enquiryId,
                "message" => "Enquiry recorded successfully.",
            ],
        ];
    }

    return $response->withJson($outputJson);

});

$app->post("/api/admissions/update-enquiry", function (Request $request, Response $response, array $args) {

    $this->logger->info("/api/admissions/update-enquiry");

    //retrieve parameters from request body
    $requestBody = json_decode($request->getBody(), true);

    $jwt = $requestBody["jwt"];
    $programCode = $requestBody["programCode"];
    
    $errors = null;

    $logic = new Logic();
    $enquiry = $logic->updateEnquiry($errors, $jwt, $programCode);

    if (isset($errors)) {
        $outputJson = [
            "ok" => false,
            "errors" => $errors,
        ];
    } else {
        $outputJson = [
            "ok" => true,
            "data" => [
                "message" => "Enquiry updated successfully.",
                "enquiryId" => $enquiry->getId(),
                "mobile" => $enquiry->getMobile(),
                "programCode" => $enquiry->getProgram()->getCode(),
                "semesterCode" => $enquiry->getSemester()->getCode()
            ],
        ];
    }

    return $response->withJson($outputJson);

});
