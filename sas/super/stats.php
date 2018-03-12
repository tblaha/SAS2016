<!DOCTYPE html>

<meta charset="UTF-8"> 

<html>

<head>
<link rel="stylesheet" type="text/css" href="/style.css">
</head>

<body>

<?php
include '../library.php';
?>

<br><br><h2>Aktuelle Wirtschaftsdaten</h2>

<h4>Pari im Umlauf</h4>
<?php
$result = count_all_money();

echo "<table>";
echo "<tr>";
echo "<th>Bürger</th>";
echo "<th>Firmen</th>";
echo "<th>Staat</th>";
echo "<th>Cash</th>";
echo "<th>Gesamt</th>";
echo "</tr>";
foreach ($result as $row) {
  $i = 0;
  echo "<tr>";
  foreach ($row as $value) {
    if ($i > 4){ break; }
    echo "<td>";
    echo $value . " ";
    echo "</td>";
    $i++;
  }
  echo "</tr>";
}
echo "</table><br><br>";
?>

<h4>Eurotopf</h4>
<?php
$euro_pot = $euro_pot=(float)$conn->query("SELECT `pot` FROM `pots` WHERE `name`='euro_pot';")->fetch_assoc()['pot'];

echo $euro_pot;

?>

<h4>Errechnete Umtauschrate</h4>
<?php
$actual_rate = calculate_rate();

echo $actual_rate . " bzw. " . 1/$actual_rate;

?>






</body>
<br><br>Das Bankensystem für Schule als Staat an der iDSB 2016. Geschrieben von Till Blaha
</html>












