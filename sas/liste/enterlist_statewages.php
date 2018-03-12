<!DOCTYPE html>

<meta charset="UTF-8"> 

<html>
<body>

<?php

include '../library.php';

if (($handle = fopen('GehaelterStaat.csv', 'r')) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if ($data[3]) {
            if ($data[6] != 1) {
                echo $data[3] . "<br>";
                transaction('companies', 1, 'users', $data[3], $data[2], 'none', 'Lohnauszahlung gemäß Lohnliste für Mittwoch');
            }
        }
    }
    fclose($handle);
}

?>

</body>
</html> 
