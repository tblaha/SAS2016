<!DOCTYPE html>

<meta charset="UTF-8"> 

<html>
<?php
$no_user= 1;
include 'library.php';
?>

<body>
<h2>Was wollen Sie tun?</h2>

<a href="users_see/search.html">Nutzer/Firmen suchen</a><br>
<a href="users_change/users.html">Nutzer/Firmen erstellen/ändern</a><br>
<a href="users_see/receipt.html">Kontoauszüge generieren</a><br>
<a href="trans/transactions.html">Transaktionen tätigen</a><br>
<a href="trans/pay_in_out.html">Geld Ein-/Auszahlen</a><br>
<a href="trans/change.html">Geld wechseln</a><br>
<a href="presence/presence.html">Anwesenheit bestätigen</a><br>
<a href="super/">Superuser sein</a><br>


<br><br><h2>Tagesaktuelle Wirtschaftsdaten</h2>

<p>Bitte beachten, dass diese Rate nur für das Kaufen von Pari von Staatsbürgern gilt. Für das Verkaufen, sowie für das Kaufen für nicht-Bürger, fallen Gebühren an, die sich nach der Umtauschmenge richten.</p>

<?php

echo "<br>Aktueller Wechselkurs:<br>";
$rate = 1/(float)$conn->query("SELECT `rate` FROM `rates` WHERE `day`=" . $day . " ORDER BY `id` DESC LIMIT 1;")->fetch_assoc()['rate'];
echo "1€ kauft " . $rate . "Ꝓ<br>";

echo "<br>Aktuelle Inflationsrate:<br>";
$inflation = calculate_inflation(($day-1), $day) * 100;
echo $inflation;

?>


<br><br><h2>Schnelle Umrechnungen</h2>


<h3>Mehrwertsteuer (MwSt)</h3>
<p>Die Mehrwertsteuer wird auf den Preis jedes Produktes addiert. So muss ein Geschäftsleiter, der für sein Produkt 100 Pari haben will, dieses für 120 Pari verkaufen, wenn die MwSt 20% beträgt. Die Firmen nehmen also in ihren Einnahmen auch die MwSt ein, die sie am Ende jeder Schicht an den Staat abführen. Dazu gehen die Geschäftsleiter zur Bank und unterbreiten ihre Buchhaltung. Aus den gesamten Einnahmen wird die Mehrwertsteuer abgezogen und der Betrag errechnet, der an den Staat überwiesen werden muss. Das Unternehmen kann dann Bargeld aus der Schicht einzahlen und diese Summe begleichen sowie die Mitarbeiter dieser Schicht bezahlen.</p>

<h4>MwSt auf Preis drauf rechnen:</h4>
<form method='GET'>
Erwünschter Preis: <input name="price"><input type="submit" name="MwSt" value="Senden">
<?php if ($_GET['MwSt']) { echo $_GET['price'] . "* (100% + " .$adjusters['mwst'] * 100 . "%) = ". ($_GET['price'] * (1 + $adjusters['mwst'])); } ?>
</form>

<h4>Abzuführender MwSt-Satz:</h4>
<p>Hier zählt, wie gesagt, nicht der Profit (Einnahmen - Ausgaben) sondern die reinen Einnahmen, die das Unternehmen gemacht hat, auch Umsatz genannt.</p>
<form method='GET'>
Einnahmen (nicht Profit): <input name="revenue"><input type="submit" name="netrevenue" value="Senden">
<?php if ($_GET['netrevenue']) { echo $_GET['revenue'] . " - (" . $_GET['revenue'] . "/ (100% + " . $adjusters['mwst']*100 . "%)) = " . ($_GET['revenue'] * (1 - (1/(1 + $adjusters['mwst'])))); } ?>
</form>


<br><h3>Einkommenssteuer</h3>
<p>Der Lohn, der mit dem Arbeitgeber ausgehandelt wurde, versteht sich "brutto". Das bedeutet das davon noch die Einkommenssteuer abgezogen wird. Dies geschieht automatisch bei Auszahlung des Lohnes nach der Schicht auf der Bank</p>

<h4>Netto-Gehalt:</h4>
<form method='GET'>
Brutto: <input name="brutto"><input type="submit" name="netincome" value="Senden">
<?php if ($_GET['netincome']) { echo $_GET['brutto'] . "* (100% - " . $adjusters['income_tax'] * 100 . "%) = ". ($_GET['brutto'] * (1 - $adjusters['income_tax'])); } ?>
</form>





</body>
<br><br>Das Bankensystem für Schule als Staat an der iDSB 2016. Geschrieben von Till Blaha
</html>












