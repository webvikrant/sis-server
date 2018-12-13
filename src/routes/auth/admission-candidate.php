<?php

use Slim\Http\Request;
use Slim\Http\Response;

// this is the endpoint for students to register themseleves for taking admission into
// various programs of the college for the designated academic session.

//attributes required for successful registration
//name, mobile, program, semester

$app->post("/api/auth/admission-candidate", function (Request $request, Response $response, array $args) {

    $this->logger->info("/api/auth/admission-candidate");

    //security check

    //retrieve parameters from request body
    $requestBody = json_decode($request->getBody(), true);

    $enquiryId = $requestBody["enquiryId"];
    $mobile = $requestBody["mobile"];

    $errors = null;

    $logic = new Logic();
    $jwt = $logic->authenticateAdmissionCandidate($errors, $enquiryId, $mobile);

    if (isset($errors)) {
        $outputJson = [
            "ok" => false,
            "errors" => $errors,
        ];
    } else {
        $outputJson = [
            "ok" => true,
            "data" => [
                "jwt" => $jwt,
            ],
        ];
    }

    return $response->withJson($outputJson);

});
