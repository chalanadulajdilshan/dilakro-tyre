<?php
$_SERVER['SERVER_NAME'] = 'localhost';
include 'class/include.php';
$db = Database::getInstance();
$res3 = $db->readQuery("SELECT page FROM non_permission_pages WHERE page LIKE '%dag%'");
while($row = mysqli_fetch_assoc($res3)) {
    echo "NP: " . $row['page'] . "\n";
}
?>
