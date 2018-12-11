<?php

use Slim\Http\Request;
use Slim\Http\Response;

// this is the endpoint for anyone to get information on programs.

$app->post("/api/academics/get-programs", function (Request $request, Response $response, array $args) {

    $this->logger->info("/api/academics/get-programs");

    //security check

    //retrieve parameters from request body
    $requestBody = json_decode($request->getBody(), true);

    $filter = $requestBody["filter"];
    $limit = $requestBody["limit"];
    $offset = $requestBody["offset"];

    $errors = null;

    $logic = new Logic();
    $programs = $logic->getAllPrograms($errors, $filter, $limit, $offset);

    if (isset($errors)) {
        $outputJson = [
            "ok" => false,
            "errors" => $errors,
        ];
    } else {
        $outputJson = [
            "ok" => true,
            "data" => $programs,
        ];
    }

    return $response->withJson($outputJson);

});
