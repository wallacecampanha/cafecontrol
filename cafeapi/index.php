<?php
ob_start();

require __DIR__ . "/../vendor/autoload.php";

/**
 * BOOTSTRAP
 */

use CoffeeCode\Router\Router;

/**
 * API ROUTES
 * index
 */
$route = new Router(url(), ":");
$route->namespace("Source\App\CafeApi");

//user
$route->group("/me");
$route->get("/", "Users:index");
$route->put("/", "Users:update");
$route->post("/photo", "Users:photo");

//invoice
$route->group("/invoices");
$route->get("/", "Invoices:index");
$route->post("/", "Invoices:create");
$route->get("/{invoice_id}", "Invoices:read");
$route->put("/{invoice_id}", "Invoices:update");
$route->delete("/{invoice_id}", "Invoices:delete");

//wallet
$route->group("/wallets");
$route->get("/", "Wallets:index");
$route->post("/", "Wallets:create");
$route->get("/{wallet_id}", "Wallets:read");
$route->put("/{wallet_id}", "Wallets:update");
$route->delete("/{wallet_id}", "Wallets:delete");

//subscription
$route->group("/subscription");
$route->get("/", "Subscriptions:index");
$route->post("/", "Subscriptions:create");
$route->get("/plans", "Subscriptions:read");
$route->put("/", "Subscriptions:update");

/**
 * ROUTE
 */
$route->dispatch();

/**
 * ERROR REDIRECT
 */
if ($route->error()) {
    header('Content-Type: application/json; charset=UTF-8');
    http_response_code(404);

    echo json_encode([
        "errors" => [
            "type " => "endpoint_not_found",
            "message" => "Não foi possível processar a requisição"
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

ob_end_flush();