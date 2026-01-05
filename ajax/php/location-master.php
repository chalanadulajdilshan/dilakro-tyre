<?php

include '../../class/include.php';
header('Content-Type: application/json; charset=UTF-8');

/*
|--------------------------------------------------------------------------
| CREATE Location
|--------------------------------------------------------------------------
*/
if (isset($_POST['create'])) {

    $LOCATION = new LocationMaster(NULL);

    $LOCATION->code = $_POST['code'];
    $LOCATION->name = $_POST['name'];
    $LOCATION->is_active = isset($_POST['is_active']) ? 1 : 0;

    $res = $LOCATION->create();

    if ($res) {
        echo json_encode([
            "status" => "success"
        ]);
    } else {
        echo json_encode([
            "status" => "error"
        ]);
    }
    exit();
}

/*
|--------------------------------------------------------------------------
| UPDATE Location
|--------------------------------------------------------------------------
*/
if (isset($_POST['update'])) {

    $LOCATION = new LocationMaster($_POST['id']);

    $LOCATION->code = $_POST['code'];
    $LOCATION->name = $_POST['name'];
    $LOCATION->is_active = isset($_POST['is_active']) ? 1 : 0;

    $res = $LOCATION->update();

    if ($res) {
        echo json_encode([
            "status" => "success"
        ]);
    } else {
        echo json_encode([
            "status" => "error"
        ]);
    }
    exit();
}

/*
|--------------------------------------------------------------------------
| DELETE Location
|--------------------------------------------------------------------------
*/
if (isset($_POST['delete']) && isset($_POST['id'])) {

    $LOCATION = new LocationMaster($_POST['id']);
    $res = $LOCATION->delete();

    if ($res) {
        echo json_encode([
            "status" => "success"
        ]);
    } else {
        echo json_encode([
            "status" => "error"
        ]);
    }
    exit();
}

?>
