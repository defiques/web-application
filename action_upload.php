<?php
require 'connection.php';

// Create connection
$conn = new mysqli($host, $user, $password, $database);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_POST['method' == 'svm']) {
    for ($i = 0; $i <= (count($links) - 1); $i++) {
        $sql = "INSERT INTO companies (link, class, com_name) VALUES (' " . $links[$i] . " ',$classes2[$i],
    ' " . $names[$i] . " ')";
        if ($conn->query($sql) === TRUE) {
            $last_id = $conn->insert_id;
            echo 'Загрузка в БД успешно завершена!';
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
elseif ($_POST['method'] == 'kmeans') {
    for ($i = 0; $i <= (count($links) - 1); $i++) {
        $sql = "INSERT INTO companies (link, class, com_name) VALUES (' " . $links[$i] . " ',$classes3[$i],
    ' " . $names[$i] . " ')";
        if ($conn->query($sql) === TRUE) {
            $last_id = $conn->insert_id;
            echo 'Загрузка в БД успешно завершена!';
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
elseif ($_POST['method'] == 'bayes') {
    for ($i = 0; $i <= (count($links) - 1); $i++) {
        $sql = "INSERT INTO companies (link, class, com_name) VALUES (' " . $links[$i] . " ',$classes1[$i],
    ' " . $names[$i] . " ')";
        if ($conn->query($sql) === TRUE) {
            $last_id = $conn->insert_id;
            echo 'Загрузка в БД успешно завершена!';
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}



$conn->close();
