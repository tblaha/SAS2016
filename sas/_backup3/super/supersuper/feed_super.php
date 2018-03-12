<!DOCTYPE html>

<meta charset="UTF-8"> 

<html>
<head>
<link rel="stylesheet" type="text/css" href="/style.css">
</head>
<body>

<?php

include '../../library.php';

$amount   = numfmt_parse($german, $_GET['amount']);

if ($_GET['feed']) {
    $query  = "UPDATE companies SET account=account+" . $amount . " WHERE id=1 ;";
    $result = $conn->query($query);
} 

?>

</body>
<br><br>Das Bankensystem für Schule als Staat an der iDSB 2016. Geschrieben von Till Blaha
</html> 

