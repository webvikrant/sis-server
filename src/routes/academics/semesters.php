<?php

use Slim\Http\Request;
use Slim\Http\Response;

require_once __DIR__ . '/../../business/Logic.php';

// this is the endpoint for students to register themseleves for taking admission into
// various programs of the college.

//attributes required for successful registration
//name, mobile, session, program, semester

$app->post("/api/academics/get-all-semesters", function (Request $request, Response $response, array $args) {

    $requestBody = json_decode($request->getBody(), true);
    $this->logger->info("POST /api/academics/get-all-semesters");

    $logic = new Logic();
    $outputJson = $logic->getAllSemesters(null, $requestBody);

    return $response->withJson($outputJson);

});
