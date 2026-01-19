<?php
$_SERVER['SERVER_NAME'] = 'localhost';
include 'class/include.php';
$db = Database::getInstance();

$pages = ['dag-view.php', 'dag-receipt-print.php'];

foreach ($pages as $page) {
    $check = $db->readQuery("SELECT id FROM non_permission_pages WHERE page = '$page'");
    if (mysqli_num_rows($check) == 0) {
        $db->readQuery("INSERT INTO `non_permission_pages` (`page`, `is_active`) VALUES ('$page', 1)");
        echo "Added $page to non_permission_pages\n";
    } else {
        echo "$page already exists in non_permission_pages\n";
    }
}
?>
