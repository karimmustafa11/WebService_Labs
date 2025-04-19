<?php

require_once 'vendor/autoload.php';
require_once 'config.php';

use Model\MySQLHandler;

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

$segments = explode('/', trim(parse_url($requestUri, PHP_URL_PATH), '/'));

$scriptName = basename($_SERVER['SCRIPT_NAME']);
$scriptIndex = array_search($scriptName, $segments);
$segments = array_slice($segments, $scriptIndex);

if (!isset($segments[1]) || $segments[1] !== 'items') {
    http_response_code(404);
    echo json_encode(["error" => "Resource doesn't exist"]);
    exit;
}

$db = new MySQLHandler("products");

if (!$db) {
    http_response_code(500);
    echo json_encode(["error" => "Internal server error"]);
    exit;
}

switch ($method) {
    case 'GET':
        if (isset($segments[2])) {
            $id = $segments[2];
            $result = $db->get_record_by_id($id);
            if ($result && count($result) > 0) {
                echo json_encode($result[0]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Resource doesn't exist"]);
            }
        } else {
            $result = $db->get_data();
            echo json_encode($result);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['id']) && isset($data['product_name'])) {
            $inserted = $db->save($data);
            if ($inserted) {
                http_response_code(201);
                echo json_encode(["status" => "Resource was added successfully!"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to insert resource"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Bad request"]);
        }
        break;

    case 'PUT':
        if (!isset($segments[2])) {
            http_response_code(400);
            echo json_encode(["error" => "Bad request"]);
            exit;
        }

        $id = $segments[2];
        $data = json_decode(file_get_contents("php://input"), true);

        $existing = $db->get_record_by_id($id);
        if (!$existing || count($existing) == 0) {
            http_response_code(404);
            echo json_encode(["error" => "Resource not found!"]);
            exit;
        }

        if (!isset($data['product_name'])) {
            http_response_code(400);
            echo json_encode(["error" => "Bad request"]);
            exit;
        }

        $updated = $db->update($data, $id);
        if ($updated) {
            $newData = $db->get_record_by_id($id);
            echo json_encode($newData[0]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to update resource"]);
        }
        break;

    case 'DELETE':
        if (!isset($segments[2])) {
            http_response_code(400);
            echo json_encode(["error" => "Bad request"]);
            exit;
        }

        $id = $segments[2];
        $deleted = $db->delete($id);
        if ($deleted) {
            echo json_encode(["status" => "Resource was deleted successfully!"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Resource not found!"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed!"]);
}
