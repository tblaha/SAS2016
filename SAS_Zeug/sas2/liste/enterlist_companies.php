<!DOCTYPE html>

<meta charset="UTF-8"> 

<html>
<body>

<?php

include '../library.php';

if (($handle = fopen('Unternehmen.csv', 'r')) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
          $query = "INSERT INTO companies (`display_name`) VALUES ('" . $data[0] . "');";
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
