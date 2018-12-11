<?php

use Slim\Http\Request;
use Slim\Http\Response;

// this is the endpoint for anyone to get information on semesters.

$app->post("/api/academics/get-semesters", function (Request $request, Response $response, array $args) {

    $this->logger->info("/api/academics/get-semesters");

    //security check

    //retrieve parameters from request body
    $requestBody = json_decode($request->getBody(), true);

    $filter = $requestBody["filter"];
    $limit = $requestBody["limit"];
    $offset = $requestBody["offset"];

    $errors = null;

    $logic = new Logic();
    $semesters = $logic->getAllSemesters($errors, $filter, $limit, $offset);

    if (isset($errors)) {
        $outputJson = [
            "ok" => false,
            "errors" => $errors,
        ];
    } else {
        $outputJson = [
            "ok" => true,
            "data" => $semesters,
        ];
    }

    return $response->withJson($outputJson);

});
