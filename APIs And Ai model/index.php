<?php
// index.php

header("Content-Type: application/json");
echo json_encode([
    "message" => "Welcome to the Parental Control System API!",
    "endpoints" => [
        "register_parent" => "/ParentalCo/register_parent.php",
        "add_child" => "/ParentalCo/add_child.php",
        "get_children" => "/ParentalCo/get_children.php",
        "create_app" => "/ParentalCo/create_app.php",
        "get_apps" => "/ParentalCo/get_apps.php",
        "update_app" => "/ParentalCo/update_app.php",
        "delete_app" => "/ParentalCo/delete_app.php",
        "create_schedule" => "/ParentalCo/create_schedule.php",
        "get_schedules" => "/ParentalCo/get_schedules.php",
        "log_usage" => "/ParentalCo/log_usage.php",
        "get_usage" => "/ParentalCo/get_usage.php"
    ]
]);
?>