<?php

use Slim\Http\Request;
use Slim\Http\Response;

require_once __DIR__ . '/../../util.php';
require_once __DIR__ . '/../../business/Logic.php';

// this is the endpoint for anyone to get information on semesters.

$app->post("/api/academics/get-all-semesters", function (Request $request, Response $response, array $args) {

    $this->logger->info("/api/academics/get-all-semesters");

    //security check

    //retrieve parameters from request body
    $requestBody = json_decode($request->getBody(), true);

    $filter = $requestBody["filter"];
    $limit = $requestBody["limit"];
    $offset = $requestBody["offset"];

    $errors = null;

    if (!limitOk($errors, $limit) || !offsetOk($errors, $offset)) {
        $outputJson = [
            "ok" => false,
            "data" => $errors,
        ];
        return $response->withJson($outputJson);
    }

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
