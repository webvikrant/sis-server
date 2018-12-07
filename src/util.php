<?php

function limitOk(&$errors, $limit)
{
    if (!isset($limit) || !is_int($limit)) {
        $errors[] = "Attribute 'limit' must be present and must be a positive integer.";
        return false;
    }

    if ($limit > 100) {
        $errors[] = "Attribute 'limit' must be a positive integer less than or equal to 100.";
        return false;
    }

    return true;
}

function offsetOk(&$errors, $offset)
{
    if (!isset($offset) || !is_int($offset)) {
        $errors[] = "Attribute 'offset' must a non-negative integer.";
        return false;
    }

    return true;
}
