<!DOCTYPE html>

<meta charset="UTF-8"> 

<html>
<head>
<link rel="stylesheet" type="text/css" href="/style.css">
</head>
<body>

<?php

include '../library.php';

$trans_id = (int)$_GET['trans_id'];
$f_id     = (int)$_GET['f_id'];
$t_id     = (int)$_GET['t_id'];
$amount   = (int)$_GET['amount'];

if ($_GET['transaction']) {
    $result = transaction($_GET['f_cat'], $f_id, $_GET['t_cat'], $t_id, $amount, $_GET['tax'], $_GET['description']);
} else {
    $result = revoke_transaction($trans_id);
}

  echo "Aktion Erfolgreich!<br>";
  echo "Folgende Überschreibungen fanden bisher von dem Sender zum Empfänger statt:<br>";
  echo "<table>";
  echo "<tr>";
  echo "<th>TransaktionID</th>";
  echo "<th>Sender Art</th>";
  echo "<th>Sender ID</th>";
  echo "<th>Sender Name</th>";
  echo "<th>Empfänger Art</th>";
  echo "<th>Empfänger ID</th>";
  echo "<th>Empfänger Name</th>";
  echo "<th>Betrag</th>";
  echo "<th>Überschriebener Betrag</th>";
  echo "<th>Steuerbetrag</th>";
  echo "<th>Steuersatz</th>";
  echo "<th>Zeitstempel</th>";
  echo "<th>Zweck</th>";
  echo "<th>Sachbearbeiter</th>";
  echo "<th>Rückgängig gemacht?</th>";
  echo "</tr>";
  foreach ($result as $row) {
    $i = 0;
    echo "<tr>";
    foreach ($row as $value) {
      if ($i > 14){ break; }
      echo "<td>";
      echo $value . " ";
      echo "</td>";
      $i++;
    }
    echo "</tr>";
  }

  
?>

</body>
</html>
