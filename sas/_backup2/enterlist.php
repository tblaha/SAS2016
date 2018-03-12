<!DOCTYPE html>

<meta charset="UTF-8"> 

<html>
<body>

<?php

include 'library.php';

$file = fopen('liste/Schuelerliste.csv', 'r');
$dump = fgetcsv($file, 0);

$twoDarray = array();
if (($handle = fopen('liste/Schuelerliste.csv', 'r')) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if (count($data) > 2) {
          $query = "INSERT INTO users (`f_name`, `l_name`, `c_id`) VALUES ('" . $data[1] . "', '" . $data[0] . "', " . $data[2] . " );";
        } else {
          $query = "INSERT INTO users (`f_name`, `l_name`) VALUES ('" . $data[1] . "', '" . $data[0] . "' );";
        }
        echo $query; echo "<br>";
        $result = $conn->query($query);
        if (!$result) {
            echo "error..."; exit();
        }
    }
    fclose($handle);
}


?>

</body>
</html> 
