<?php

use Slim\Http\Request;
use Slim\Http\Response;

require_once __DIR__ . '/../../business/Logic.php';

// this is the endpoint for students to register themseleves for taking admission into
// various programs of the college.

//attributes required for successful registration
//name, mobile, session, program, semester

$app->post("/api/academics/get-all-programs", function (Request $request, Response $response, array $args) {

    $requestBody = json_decode($request->getBody(), true);
    $this->logger->info("POST /api/academics/get-all-programs");

    $logic = new Logic();
    $outputJson = $logic->getAllPrograms(null, $requestBody);

    return $response->withJson($outputJson);

});

$app->post("/api/academics/get-programs-with-code", function (Request $request, Response $response, array $args) {

    $requestBody = json_decode($request->getBody(), true);
    $this->logger->info("POST /api/academics/get-programs-with-code");

    $logic = new Logic();
    $outputJson = $logic->getProgramsWhereCodeLike(null, $requestBody);

    return $response->withJson($outputJson);

});
