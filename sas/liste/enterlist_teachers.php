<!DOCTYPE html>

<meta charset="UTF-8"> 

<html>
<body>

<?php

include '../library.php';

if (($handle = fopen('Lehrer.csv', 'r')) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if (count($data) > 3) {
           $query = "INSERT INTO users (`f_name`, `l_name`, `id`, `c_id`) VALUES ('" . $data[1] . "', '" . $data[0] . "', " . substr($data[2], -4) . ", " . $data[3] . " );";
        } else {
           $query = "INSERT INTO users (`f_name`, `l_name`, `id`) VALUES ('" . $data[1] . "', '" . $data[0] . "', " . substr($data[2], -4) . " );";
        }
        echo $query; echo "<br>";
        $result = $conn->query($query);
        if (!$result) {
            echo "error...<br>";
        }
    }
    fclose($handle);
}

?>

</body>
</html> 
