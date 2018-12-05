<?php

use Slim\Handlers\Error;
use Slim\Http\Request;
use Slim\Http\Response;

// this is the endpoint for students to register themseleves for taking admission into
// various programs of the college.

//requires auth - No

//attributes required for successful registration
//name, mobile, session, program, semester

$app->post("/api/admission/register", function (Request $request, Response $response, array $args) {

    $this->logger->info("POST /api/admission/register");

    $body = json_decode($request->getBody(), true);

    if(!isset($body)){
        $error = new ErrorMessage(EMPTY_REQUEST_BODY);
        $result = [
            "success" => false,
            "error" => $error,
            "data" => "",
        ];

        return $response->withJson($result);
    }

    $name = $body["name"];
    if (!isset($name)) {
        $result = [
            "success" => false,
            "error" => new Error(EMPTY_NAME),
            "data" => "",
        ];

        return $response->withJson($result);

    }

    $result = [
        "success" => true,
        "error" => "",
        "data" => [
            registrationId => 123,
        ],
    ];

    return $response->withJson($result);

});
