<?php

echo '<a href="/">Zur Startseite</a><br>';

// Debugging
error_reporting(-1);
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 'On');

// String parsing for german decimals
$german = numfmt_create( 'de_DE', NumberFormatter::DECIMAL );

####################
# MySQL Connection #
####################

// Credentials
$myservername = "localhost";
$myusername   = "sas";
$mypassword   = "4QPXypXjYsP7mttz";
$mydb         = "sas";

// Create connection as object
$conn = new mysqli($myservername, $myusername, $mypassword, $mydb);

// General Variables:
$id_digits     = 5;					// Wie viele Stellen haben die ganzen ID's?
$categories    = array("users", "companies", "vips");	// Welche Kategorien gibt es?
$cat_id_offset = array(0, 10000, 90000);		// Wie sind die Id-Offsets dieser Kategorien?
$cat_id_range  = (pow(10, ($id_digits - 1)));		// Wie viele Id können pro Kategorie vergeben werden? --> 10^($id_digits -1). Bei $id_digits=5 --> 10000
$state_id      = 1;

$now = time(); // or your date as well
$your_date = strtotime("2016-07-10");
$datediff = $now - $your_date;
$day = floor($datediff/(60*60*24));

//Adjuster Variables:
$adjusters= array();
$adjusters['mwst']            =(float)$conn->query("SELECT `value` FROM `adjusters` WHERE `name`='MwSt';")->fetch_assoc()['value'];
$adjusters['income_tax']      =(float)$conn->query("SELECT `value` FROM `adjusters` WHERE `name`='income_tax';")->fetch_assoc()['value'];
$adjusters['rate']            =(float)$conn->query("SELECT `rate`  FROM `rates`     WHERE `day` =" . $day . " ;")->fetch_assoc()['rate'];
$adjusters['toparifee']       =(float)$conn->query("SELECT `value` FROM `adjusters` WHERE `name`='toparifee';")->fetch_assoc()['value'];
$adjusters['toeurofee']       =(float)$conn->query("SELECT `value` FROM `adjusters` WHERE `name`='toeurofee';")->fetch_assoc()['value'];
$adjusters['foreignertopari'] =(float)$conn->query("SELECT `value` FROM `adjusters` WHERE `name`='foreignertoparifee';")->fetch_assoc()['value'];
$adjusters['foreignertoeuro'] =(float)$conn->query("SELECT `value` FROM `adjusters` WHERE `name`='foreignertoeurofee';")->fetch_assoc()['value'];
$adjusters['cash_limit']      =(float)$conn->query("SELECT `value` FROM `adjusters` WHERE `name`='upper_limit';")->fetch_assoc()['value'];

$error[0]  = "Error 0  : Die ID ließ sich keinem Konto zuordnen<br>";
$error[1]  = "Error 1  : Die ID ließ sich keinem Bereich zuordnen!<br>";
$error[2]  = "Error 2  : Die ID ist keine Ganzzahl!<br>";
$error[3]  = "Error 3  : Die Sender-ID ließ sich keinem Konto zuordnen<br>";
$error[4]  = "Error 4  : Die Sender-ID ließ sich keinem Bereich zuordnen!<br>";
$error[5]  = "Error 5  : Die Sender-ID ist keine Ganzzahl!<br>";
$error[6]  = "Error 6  : Die Empfänger-ID ließ sich keinem Konto zuordnen<br>";
$error[7]  = "Error 7  : Die Empfänger-ID ließ sich keinem Bereich zuordnen!<br>";
$error[8]  = "Error 8  : Die Empfänger-ID ist keine Ganzzahl!<br>";
$error[9]  = "Error 101: Es gibt keine Ergebnisse!<br>";
$error[10] = "Error 102: Vorname oder Nachname nicht angegeben<br>";
$error[11] = "Error 103: ID existiert nicht, zum Erstellen dieses Objektes, Feld leer lassen!<br>";
$error[12] = "Error 9  : Datenbankfehler beim Anlegen oder Updaten des Kontos<br>";
$error[13] = "Error 104: Kein Firmenname angegeben<br>";
$error[14] = "Error 10 : Die CEO-ID ließ sich keinem Konto zuordnen<br>";
$error[15] = "Error 11 : Kein Betrag spezifiziert!<br>";
$error[16] = "Error 12 : Datenbankfehler beim Ausführen der Transaktion<br>";
$error[17] = "Error 13 : Datenbankfehler beim Überschreiben der Steuern<br>";
$error[18] = "Error 14 : Datenbankfehler beim Schreiben in den Transaktionslog<br>";
$error[19] = "Error 666: Unbefugter Zugriff auf Bereiche des Servers: FINANZMINISTER KONTAKTIEREN!<br>";
$error[20] = "Error 66666666666666: Hol sofort Till. Jetzt. LAUF! MAAAAAAAAN!!!111!1!<br>";
$error[21] = "Error 15 : Bitte Überschreibungszweck angeben!<br>";
$error[22] = "Error 16 : Datenbankfehler beim Holen des Transaktionslogs<br>";
$error[23] = "Error 17 : Die Transaktions ID ist nicht gültig.<br>";
$error[24] = "Error 18 : Diese Transaktion ist bereits Rückgängig gemacht worden!<br>";
$error[25] = "Error 19 : Datenbankfehler beim Ausführen der Revokation<br>";
$error[26] = "Error 20 : Die Transaktion ist schon zu lange her um zurückgezogen zu werden. Bitte Finanzminister kontaktiern.<br>";
$error[27] = "Error 21 : Überweisung ist größer als der Inhalt des Sender-Kontos. Transaktion wurde nicht durchgeführt<br>";
$error[28] = "Error 22 : Der Transaktionslog ist leer!<br>";
$error[29] = "Error 23 : Datenbankfehler beim Ausführen des Wechselns. Finanzminister kontaktieren!<br>";
$error[30] = "Error 24 : Betrag ist größer als der Inhalt des Kontos. Wechseln nicht ausführen!<br>";
$error[31] = "Error 25 : Kein Name angegeben!<br>";
$error[32] = "Error 26 : Datenbankfehler beim Schreiben der Ein/Auszahlung. Finanzminister kontaktieren!<br>";
$error[33] = "Error 27 : Datenbankfehler beim Holen des Einzahlen/Abheben Logs.<br>";
$error[34] = "Error 28 : Limit für Barauszahlungen überschritten: Finanzminister kontaktieren!<br>";
$error[35] = "Error 29 : Für Auszahlungen an Firmen, Finanzminister kontaktieren!<br>";
$error[36] = "Error 30 : Betrag ist größer als der Inhalt des Kontos. Auszahlung nicht durchführen!<br>";
$error[37] = "Error 31 : Fehler beim Schreiben in den Changes Log. Finanzminister kontaktieren!<br>";
$error[38] = "Error 32 : Der Wechsel ist schon zu lange her um zurückgezogen zu werden. Bitte Finanzminister kontaktiern.<br>";
$error[39] = "Error 33 : Datenbankfehler beim Überschreiben der Steuern!<br>";
$error[40] = "Error 34 : Sie sind (noch) nicht as Anwesend markiert. Bitte den Kunden als Anwesend markieren!<br>";

if (!$no_user) {
    if (!$_SERVER['REMOTE_USER']) {
        echo $error[19]; exit();
    } else {
        $user      = $_SERVER['REMOTE_USER'];
    }
}

function exists_id($id, $cat) {
  // Abstract: Takes an ID and looks it up in the tables.
  // Returns:  1 on success, 0 on failure
  // Depends:  Variables: $conn; Functions: get_category
  
  global $conn, $error;

  $query = sprintf("SELECT 1 FROM " . $cat . " WHERE `id`='" . $id . "';");
  $result = $conn->query($query);
  if ($result->num_rows >= 1) {return 1;} 
  else                        {return 0;}
}

function add_user($id, $f_name, $l_name, $c_id) {
  // Abstract: 
  // Return  : 1 on success, 0 on failure
  // Depends : $conn
  
  global $conn, $error;
  
  $args_raw = array("id" => (int)$id, "f_name" => $f_name, "l_name" => $l_name, "c_id" => (int)$c_id);
  $args = array();
  
  foreach($args_raw as $index => $arg) {
    if ($arg === NULL || $arg === "" || $arg === " " || $arg === 0) {
      $args[$index] = NULL;
    } else {
      $args[$index] = $arg;
    }
  }  

  if ($args['id'] === NULL) {
    if ($args['f_name'] === NULL || $args['l_name'] === NULL) {
        echo $error[10]; exit();
    }
    $query = "INSERT INTO users (`f_name`, `l_name`, `c_id`) VALUES ('" . $args['f_name'] . "', '" . $args['l_name'] . "', ";
    
    if ($args['c_id']) {
      $query .= $c_id . " );";
    } else {
      $query .= "NULL );";
    }
    $new = TRUE;
    
  } else {
    if (!exists_id($args['id'], "users")) {
      echo $error[11]; exit();
    }
    
    if ($args['f_name'] !== NULL && $args['l_name'] !== NULL) {
        $query = "REPLACE INTO users (`id`, `f_name`, `l_name`, `c_id`) VALUES (" . $args['id'] . ", '" . $args['f_name'] . "', '" . $args['l_name'] . "', ";
    
        if ($args['c_id']) {
            $query .= $args['c_id'] . " );";
        } else {
            $query .= "NULL );";
        }
    } else {
        if ($args['c_id']) {
            $query = "UPDATE users SET `c_id`=" . $args['c_id'] . " WHERE `id`=" . $args['id'] . " ;";
        } else {
            exit();
        }
    }
}
        
  
  $result = $conn->query($query);
  
  if (!$result) {
    echo $error[12]; exit();
  }
  
  return get_details($id, $f_name, $l_name, NULL);
}

function add_company($id, $display_name, $ceo_id) {
  // Abstract: 
  // Depends: $conn
  
  global $conn, $error;
  
  $args_raw = array("id" => (int)$id, "display_name" => $display_name, "ceo_id" => $ceo_id);
  $args = array();
  
  if (!is_int($args_raw['id'])) {
    echo $error[9]; exit();
  }
  
  foreach($args_raw as $index => $arg) {
    if ($arg === NULL || $arg === "" || $arg === " " || $arg === 0) {
      $args[$index] = NULL;
    } else {
      $args[$index] = $arg;
    }
  }  
  
  if ($args['display_name'] === NULL) {
      echo $error[13]; exit();
  }
  if ($args['ceo_id'] !== NULL) {
      if (!exists_id($args['ceo_id'], "users")) {
         echo $error[14]; exit();
      } 
  }
  
  if ($args['id'] === NULL) {
    $query = "INSERT INTO companies (`display_name`, `ceo_id`) VALUES ('" . $args['display_name'] . "', '" . $args['ceo_id'] . "');";
  } else {
    if (!exists_id($args['id'], "companies")) {
      echo $error[11]; exit();
    }
    
    $query = "REPLACE INTO companies (`id`, `display_name`, `ceo_id`) VALUES ('" . $args['id'] . "', '" . $args['display_name'] . "', '" . $args['ceo_id'] . "');";
  }
  
  $result = $conn->query($query);
  
  if (!$result) {
      echo $error[12]; exit();
  } 
  
  return get_details_company(NULL, $display_name);
}

function get_details($id, $first_name, $last_name, $c_id) {
  // Abstract: Gibt eine Liste von möglichen Usern aus, basierend auf dem Suchkriterium.
  //           Das Argument $cat ist mandatory. $display_name ist standardmäßig: Vorname + " " + MittelName + " " + Nachname
  //           Wenn Suchkriterien ausgelassen werden sollen, müssen sie NULL, "" (leerer String) oder 0 (integer) sein.
  /* Returns: 2D-Array: id und display_name werden als Strings ausgegeben.
  +-------+-------+----------------+------------+-----------------+----------+
  |  Row  |  id   |  display_name  | company_id |  company_name   |  account |
  +-------+-------+----------------+------------+-----------------+----------+
  |   0   | 00001 |   Till Blaha   |   10000    |      Shoes      |    100   |
  +-------+-------+----------------+------------+-----------------+----------+
  |  ...  |  ...  |      ...       |     ...    |       ...       |    ...   |
  +-------+-------+----------------+------------+-----------------+----------+
  
  Beispiel:
    $result = get_id(NULL, "Blaha", NULL, "users");
    echo $result[0][display_name] . "<br>" . $result[0][id];
  Ausgabe:
    Till Blaha
    10000
  
  Beispiel-Implementierung:
    $result = get_id("Till", NULL, NULL, "users");
    foreach ($result as $row) {
      foreach ($row as $value) {
	echo $value . " ";
      }
      echo "<br>";
    }
  */
  
  // Depends: $conn
  
  global $conn, $error;
  
  // Put the name criteria in array; initialise final criteria array
  $criteria_raw = array("f_name" => $first_name, "l_name" => $last_name);
  $criteria = array();
  
  // Strips down the raw array to only the ones NOT NULL or only spaces
  foreach ($criteria_raw as $title => $crit) {
    if ($crit !== NULL && $crit !== "" && $crit !== 0) {
      $criteria[$title] = $crit;
    }
  }
  
  if (!count($criteria) && ($id === NULL || $id === "" || $id === " " || $id === 0) && ($c_id === NULL || $c_id === "" || $c_id === " " || $c_id === 0)) {
    $query = "SELECT id,f_name,l_name,c_id,(SELECT display_name FROM companies WHERE id=c_id) AS c_name,account FROM users ORDER BY id ASC;";
  } else {
    $query = "SELECT id,f_name,l_name,c_id,(SELECT display_name FROM companies WHERE id=c_id) AS c_name,account FROM users WHERE ";
    if ($id !== NULL && $id !== "" && $id !== " " && $id !== 0) {
      $query .= "`id` = " . $id . " AND ";
    }
    if ($c_id !== NULL && $c_id !== "" && $c_id !== " " && $c_id !== 0) {
      $query .= "`c_id` = " . $c_id . " AND ";
    }
    foreach ($criteria as $title => $crit) {
      $query .= "`" . $title . "` LIKE '%" . $crit . "%'";
      $query .= " AND ";
    }
    $query  = substr($query, 0, -5);
    $query .= " ORDER BY id ASC;";
  }
  
  // Execute
  $result = $conn->query($query);
  
  // Declare the out_array
  $out_array = array();
  
  // If rows are availible, then iterate over the rows and put them in as seperate subarrays aka 2D array
  if ($result->num_rows >= 1) {
    $i = 0;
    while ($row = $result->fetch_assoc()) {
      $out_array[$i] = array("id" => $row["id"], "f_name" => $row['f_name'], "l_name" => $row["l_name"], "c_id" => $row["c_id"], "c_name" => $row["c_name"], "account" => $row["account"]);
      $i++;
    }
  } else {
    echo $error[9];
  }
  return $out_array;
}

function get_details_company($id, $display_name) {
  // Abstract: Siehe get_details
  // Depends: $conn
  
  global $conn, $error;
  
  if (($display_name === NULL || $display_name === "" || $display_name === " " || $display_name === 0) && ($id === NULL || $id === "" || $id === " " || $id === 0))  {
    $query = "SELECT id,display_name,ceo_id,(SELECT f_name FROM users WHERE id=ceo_id) AS ceo_f_name,(SELECT l_name FROM users WHERE id=ceo_id) AS ceo_l_name,account FROM companies ORDER BY id ASC;";
  } else {
    $query = "SELECT id,display_name,ceo_id,(SELECT f_name FROM users WHERE id=ceo_id) AS ceo_f_name,(SELECT l_name FROM users WHERE id=ceo_id) AS ceo_l_name,account FROM companies WHERE ";
    if ($id !== NULL && $id !== "" && $id !== " " && $id !== 0) {
      $query .= "`id` = " . $id . " AND ";
    }
    if ($display_name !== NULL && $display_name !== "" && $display_name !== " " && $display_name !== 0) {
      $query .= "`display_name` LIKE '%" . $display_name . "%'";
      $query .= " AND ";
    }
    $query  = substr($query, 0, -5);
    $query .= " ORDER BY id ASC;";
  }

  // Execute
  $result = $conn->query($query);
  
  // Declare the out_array
  $out_array = array();
  
  // If rows are availible, then iterate over the rows and put them in as seperate subarrays aka 2D array
  if ($result->num_rows >= 1) {
    $i = 0;
    while ($row = $result->fetch_assoc()) {
      $out_array[$i] = array("id" => $row["id"], "display_name" => $row["display_name"], "ceo_id" => $row["ceo_id"], "ceo_f_name" => $row["ceo_f_name"], "ceo_l_name" => $row["ceo_l_name"], "account" => $row["account"]);
      $i++;
    }
  } else {
    echo $error[9];
  }
  return $out_array;
}

function generate_receipt($id, $cat, $limit) {
  // Abstract: Delivers all rows of the transaction log concerning the user with the specified id. $limit is optional and defines the amount of rows to print
  // Returns: 3D Array
  // Depends: $conn, exists_id, get_category
  
  global $conn, $error, $adjusters, $user, $state_id;
  
  if (!exists_id($id, $cat)) {
    echo $error[0]; exit();
  }
  
  $query_acc = "SELECT `account` FROM `" . $cat . "` WHERE `id`=" . $id . ";";
  $result_acc = $conn->query($query_acc);
  $account = (float)$result_acc->fetch_assoc()['account']; 
  
  $out_array = array("account" => $account, "transactions" => array(), "changes" => array());
  
  #----
  
  $query_get = "SELECT `id`,`f_cat`,`f_id`,`f_name`,`t_cat`,`t_id`,`t_name`,`amount`,`transamount`,`tax_amount`,`tax_set`,`date`,`description`,`cashier`,`revoked` FROM transactions WHERE `f_id`=" . $id . " AND `f_cat`='" . $cat . "' OR `t_id`=" . $id . " AND `t_cat`='" . $cat . "' ORDER BY `id` DESC;";
  $result_get = $conn->query($query_get);
  
  if ($result_get->num_rows >= 1) {
    $i = 0;
    while ($row = $result_get->fetch_assoc()) {
      $out_array['transactions'][$i] = array("id" => $row["id"], "f_cat" => $row['f_cat'], "f_id" => $row['f_id'], "f_name" => $row['f_name'], "t_cat" => $row['t_cat'], "t_id" => $row['t_id'], "t_name" => $row['t_name'], "amount" => $row['amount'], "transamount" => $row['transamount'], "tax_amount" => $row['tax_amount'], "tax_set" => $row['tax_set'], "date" => $row['date'], "description" => $row['description'], "cashier" => $row['cashier'], "revoked" => $row['revoked']);
      $i++;
      if ($limit != 0) {
        if ($i >= $limit) {
          break;
        }
      }
    }
  } else {
    echo $error[28];
  }
  
  
  if ($cat == "users") {
    $log_name = $conn->query("SELECT f_name FROM users WHERE id=" . $id . " ;")->fetch_assoc()['f_name'] . " " . $conn->query("SELECT l_name FROM users WHERE id=" . $id . " ;")->fetch_assoc()['l_name'];
  } else if ($cat == "companies") {
    $log_name = $conn->query("SELECT display_name FROM companies WHERE id=" . $id . " ;")->fetch_assoc()['display_name'];
  }
  
  $query_get = "SELECT `id`,`t_cat`,`t_id`,'" . $log_name . "' AS `t_name`,`mode`,`amount`,`date`,`cashier` FROM pay_in_out WHERE `t_id`=" . $id . " AND `t_cat`='" . $cat . "' ORDER BY `id` DESC;";
  
  $result_get = $conn->query($query_get);
  
  if ($result_get->num_rows >= 1) {
    $i = 0;
    while ($row = $result_get->fetch_assoc()) {
      $out_array['pay_in_out'][$i] = array("id" => $row["id"], "t_cat" => $row['t_cat'], "t_id" => $row['t_id'], "t_name" => $row['t_name'], "mode" => $row['mode'], "amount" => $row['amount'], "date" => $row['date'], "cashier" => $row['cashier']);
      $i++;
      if ($limit != 0) {
        if ($i >= $limit) {
          break;
        }
      }
    }
  } else {
    echo $error[22];
  }
  
  $query_get = "SELECT `id`,'" . $cat . "' AS `t_cat`,`t_id`,'" . $log_name . "' AS `t_name`,`mode`,`amount`,`nominal_rate`,`deviation`,`payout`,`stateamount`,`date`,`cashier`,`revoked` FROM changes WHERE `t_id`=" . $id . "  ORDER BY `id` DESC;";
  
  $result_get = $conn->query($query_get);
  
  if ($result_get->num_rows >= 1) {
    $i = 0;
    while ($row = $result_get->fetch_assoc()) {
      $out_array['changes'][$i] = array("id" => $row["id"], "t_cat" => $row['t_cat'], "t_id" => $row['t_id'], "t_name" => $row['t_name'], "mode" => $row['mode'], "amount" => $row['amount'], "nominal_rate" => $row['nominal_rate'], "deviation" => $row['deviation'], "payout" => $row['payout'], "stateamount" => $row['stateamount'], "date" => $row['date'], "cashier" => $row['cashier'], "revoked" => $row['revoked']);
      $i++;
    }
  } else {
    echo $error[22];
  }
  
  return $out_array;
}

function transaction($f_cat, $f_id, $t_cat, $t_id, $amount, $tax, $description) {
  // Abstract: Manages a transaction by adding and deduction the amount under the consideration of taxes. Writes to the transaction log.
  // Returns: A 2d report of all transactions made between the 2 parties including the latest.
  // Depends: Variables/Objects: $conn; Functions: get_category, exists_id

  global $conn, $error, $adjusters, $user, $state_id;
  
  $args_raw = array("f_cat" => $f_cat, "f_id" => (int)$f_id, "t_cat" => $t_cat, "t_id" => (int)$t_id, "amount" => $amount, "tax" => $tax, "description" => $description);
  $args = array();
  
  if (!exists_id($args_raw['f_id'], $args_raw['f_cat'])) {
    echo $error[3]; exit();
  }
  if (!exists_id($args_raw['t_id'], $args_raw['t_cat'])) {
    echo $error[6]; exit();
  }
  if (!$description) {
    echo $error[21]; exit();
  }
  
  foreach($args_raw as $index => $arg) {
    if ($arg === NULL || $arg === "" || $arg === " " || $arg === 0) {
      $args[$index] = NULL;
    } else {
      $args[$index] = $arg;
    }
  }
  
  // Rundung, bzw. Expansion auf 3 Nachkommastellen
  $pamount = number_format((float)$args['amount'], 3, '.', ''); 
  
  if ($pamount == 0) {
      echo $error[15]; exit();
  }
  $last_query =$conn->query("SELECT (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`date`)) AS `date` FROM `transactions` WHERE `t_id`='" . $t_id . "' ORDER BY id DESC LIMIT 1;")->fetch_assoc()['date'];
echo $last_query;
    if ($last_query < 15) {
	if ($last_query) {
        echo "Letzte Transaktionen mit diesem Empfänger ID ist weniger als 15 Sekunden her. Es ist möglich, dass diese Aktion 2 Mal an den Server verschickt wurde. Bitte Kontoauszug kontrollieren, ob Geld verschoben wurde!"; exit();
}
    }
  
  $prev_account=(float)$conn->query("SELECT `account` FROM `" . $f_cat . "` WHERE `id`=" . $f_id . ";")->fetch_assoc()['account'];
  if ($prev_account < $pamount) {
    echo $error[27]; exit();
  }
  
  // appliance of income tax
  if($tax == "Einkommenssteuer") {
    $transamount = $pamount * ( 1 - $adjusters['income_tax'] );
    $stateamount = $pamount - $transamount;
    $tax_set = $adjusters['income_tax'];
  } else {
    $transamount = $pamount;
    $stateamount = 0;
    $tax_set = 0;
  }
  
  // Deduction the amount from senders account
  $query_ded  = sprintf("UPDATE " . $f_cat . " SET account=account-" . $pamount . " WHERE id=" . $f_id . ";");
  $result_ded = $conn->query($query_ded);
  
  // Adding the amount to recipients account
  $query_add  = sprintf("UPDATE " . $t_cat . " SET account=account+" . $transamount . " WHERE id=" . $t_id . ";");
  $result_add = $conn->query($query_add);
  
  if (!$result_add || !$result_ded) {
    echo $error[16]; exit();
  }

  // Taxes
  if ($stateamount) {
    $query_tax  = sprintf("UPDATE companies SET account=account+" . $stateamount . " WHERE id=" . $state_id . " ;");
    $result_tax = $conn->query($query_tax);
  
    if (!$result_tax) {
        echo $error[17]; exit();
    }
  }
  
  // IMPORTANT CHECK-UP
  if ( abs(($stateamount + $transamount) - $pamount) > 0.1 ) {
    echo $error[20]; exit();
  }
  
  // Writing to transaction log
  if ($f_cat == "users") {
    $f_name = $conn->query("SELECT f_name FROM users WHERE id=" . $f_id . " ;")->fetch_assoc()['f_name'] . " " . $conn->query("SELECT l_name FROM users WHERE id=" . $f_id . " ;")->fetch_assoc()['l_name'];
  } else if ($f_cat == "companies") {
    $f_name = $conn->query("SELECT display_name FROM companies WHERE id=" . $f_id . " ;")->fetch_assoc()['display_name'];
  }
  
  if ($t_cat == "users") {
    $t_name = $conn->query("SELECT f_name FROM users WHERE id=" . $t_id . " ;")->fetch_assoc()['f_name'] . " " . $conn->query("SELECT l_name FROM users WHERE id=" . $t_id . " ;")->fetch_assoc()['l_name'];
  } else if ($t_cat == "companies") {
    $t_name = $conn->query("SELECT display_name FROM companies WHERE id=" . $t_id . " ;")->fetch_assoc()['display_name'];
  }
  
  $query_log  = sprintf("INSERT INTO transactions (`f_id`, `f_cat`, `f_name`, `t_id`, `t_cat`, `t_name`, `amount`, `transamount`, `tax_set`, `tax_amount`, `date`, `description`, `cashier`,`revoked`) VALUES (" . $f_id . ", '" . $f_cat . "', '" . $f_name . "', " . $t_id . ", '" . $t_cat . "', '" . $t_name . "', " . $pamount . ", " . $transamount . ", " . $tax_set . ", " . $stateamount . ", NOW(), '" . $description . "', '" . $user . "', 0 );");
  $result_log = $conn->query($query_log);

  if (!$result_log) {
    echo $error[18]; exit();
  }
  
  ///// Spucke Log aus:
  
  $query_get = "SELECT `id`,`f_cat`,`f_id`,'" . $f_name . "' AS `f_name`,`t_cat`,`t_id`,'" . $t_name . "' AS `t_name`,`amount`,`transamount`,`tax_amount`,`tax_set`,`date`,`description`,`cashier`, `revoked` FROM transactions WHERE `f_cat`='" . $f_cat . "' AND `f_id`=" . $f_id . " AND `t_cat`='" . $t_cat . "' AND `t_id`=" . $t_id . " ORDER BY `id` DESC;";
  
  $result_get = $conn->query($query_get);
  
  if ($result_get->num_rows >= 1) {
    $i = 0;
    while ($row = $result_get->fetch_assoc()) {
      $out_array[$i] = array("id" => $row["id"], "f_cat" => $row['f_cat'], "f_id" => $row['f_id'], "f_name" => $row['f_name'], "t_cat" => $row['t_cat'], "t_id" => $row['t_id'], "t_name" => $row['t_name'], "amount" => $row['amount'], "transamount" => $row['transamount'], "tax_amount" => $row['tax_amount'], "tax_set" => $row['tax_set'], "date" => $row['date'], "description" => $row['description'], "cashier" => $row['cashier'], "revoked" => $row['revoked']);
      $i++;
    }
  } else {
    echo $error[22]; exit();
  }
  return $out_array;
}

function batch_transaction($f_cat, $f_id, $t_cat, $t_id, $amount, $tax, $description) {
  // Abstract: Manages a transaction by adding and deduction the amount under the consideration of taxes. Writes to the transaction log.
  // Returns: A 2d report of all transactions made between the 2 parties including the latest.
  // Depends: Variables/Objects: $conn; Functions: get_category, exists_id

  global $conn, $error, $adjusters, $user, $state_id;
  $i = 0;
  
  foreach ($t_id as $t_id) {
  
  $args_raw = array("f_cat" => $f_cat, "f_id" => (int)$f_id, "t_cat" => $t_cat, "t_id" => (int)$t_id, "amount" => $amount, "tax" => $tax, "description" => $description);
  $args = array();
  
  if (!exists_id($args_raw['f_id'], $args_raw['f_cat'])) {
    echo $error[3]; exit();
  }
  if (!exists_id($args_raw['t_id'], $args_raw['t_cat'])) {
    echo $error[6]; exit();
  }
  if (!$description) {
    echo $error[21]; exit();
  }
  
  foreach($args_raw as $index => $arg) {
    if ($arg === NULL || $arg === "" || $arg === " " || $arg === 0) {
      $args[$index] = NULL;
    } else {
      $args[$index] = $arg;
    }
  }
  
  // Rundung, bzw. Expansion auf 3 Nachkommastellen
  $pamount = number_format((float)$args['amount'], 3, '.', ''); 
  
  if ($pamount == 0) {
      echo $error[15]; exit();
  }
  
  $prev_account=(float)$conn->query("SELECT `account` FROM `" . $f_cat . "` WHERE `id`=" . $f_id . ";")->fetch_assoc()['account'];
  if ($prev_account < $pamount) {
    echo "Bei Empfänger id " . $t_id . ": " . $error[27]; exit();
  }
  
  // appliance of income tax
  if($tax == "Einkommenssteuer") {
    $transamount = $pamount * ( 1 - $adjusters['income_tax'] );
    $stateamount = $pamount - $transamount;
    $tax_set = $adjusters['income_tax'];
  } else {
    $transamount = $pamount;
    $stateamount = 0;
    $tax_set = 0;
  }
  
  // Deduction the amount from senders account
  $query_ded  = sprintf("UPDATE " . $f_cat . " SET account=account-" . $pamount . " WHERE id=" . $f_id . ";");
  $result_ded = $conn->query($query_ded);
  
  // Adding the amount to recipients account
  $query_add  = sprintf("UPDATE " . $t_cat . " SET account=account+" . $transamount . " WHERE id=" . $t_id . ";");
  $result_add = $conn->query($query_add);
  
  if (!$result_add || !$result_ded) {
    echo $error[16]; exit();
  }

  // Taxes
  if ($stateamount) {
    $query_tax  = sprintf("UPDATE companies SET account=account+" . $stateamount . " WHERE id=" . $state_id . " ;");
    $result_tax = $conn->query($query_tax);
  
    if (!$result_tax) {
        echo $error[17]; exit();
    }
  }
  
  // IMPORTANT CHECK-UP
  if ( abs(($stateamount + $transamount) - $pamount) > 0.1 ) {
    echo $error[20]; exit();
  }
  
  // Writing to transaction log
  if ($f_cat == "users") {
    $f_name = $conn->query("SELECT f_name FROM users WHERE id=" . $f_id . " ;")->fetch_assoc()['f_name'] . " " . $conn->query("SELECT l_name FROM users WHERE id=" . $f_id . " ;")->fetch_assoc()['l_name'];
  } else if ($f_cat == "companies") {
    $f_name = $conn->query("SELECT display_name FROM companies WHERE id=" . $f_id . " ;")->fetch_assoc()['display_name'];
  }
  
  if ($t_cat == "users") {
    $t_name = $conn->query("SELECT f_name FROM users WHERE id=" . $t_id . " ;")->fetch_assoc()['f_name'] . " " . $conn->query("SELECT l_name FROM users WHERE id=" . $t_id . " ;")->fetch_assoc()['l_name'];
  } else if ($t_cat == "companies") {
    $t_name = $conn->query("SELECT display_name FROM companies WHERE id=" . $t_id . " ;")->fetch_assoc()['display_name'];
  }
  
  $query_log  = sprintf("INSERT INTO transactions (`f_id`, `f_cat`, `f_name`, `t_id`, `t_cat`, `t_name`, `amount`, `transamount`, `tax_set`, `tax_amount`, `date`, `description`, `cashier`,`revoked`) VALUES (" . $f_id . ", '" . $f_cat . "', '" . $f_name . "', " . $t_id . ", '" . $t_cat . "', '" . $t_name . "', " . $pamount . ", " . $transamount . ", " . $tax_set . ", " . $stateamount . ", NOW(), '" . $description . "', '" . $user . "', 0 );");
  $result_log = $conn->query($query_log);

  if (!$result_log) {
    echo $error[18]; exit();
  }
  
  ///// Spucke Log aus:
  $query_get = "SELECT `id`,`f_cat`,`f_id`,'" . $f_name . "' AS `f_name`,`t_cat`,`t_id`,'" . $t_name . "' AS `t_name`,`amount`,`transamount`,`tax_amount`,`tax_set`,`date`,`description`,`cashier`, `revoked` FROM transactions WHERE `t_cat`='" . $t_cat . "' AND `t_id`=" . $t_id . " ORDER BY `id` DESC LIMIT 1;";
  
  $result_get = $conn->query($query_get);
  
  if ($result_get->num_rows >= 1) {
    while ($row = $result_get->fetch_assoc()) {
      $out_array[$i] = array("id" => $row["id"], "f_cat" => $row['f_cat'], "f_id" => $row['f_id'], "f_name" => $row['f_name'], "t_cat" => $row['t_cat'], "t_id" => $row['t_id'], "t_name" => $row['t_name'], "amount" => $row['amount'], "transamount" => $row['transamount'], "tax_amount" => $row['tax_amount'], "tax_set" => $row['tax_set'], "date" => $row['date'], "description" => $row['description'], "cashier" => $row['cashier'], "revoked" => $row['revoked']);
      $i++;
    }
  } else {
    echo "Bei Empfänger id " . $t_id . ": " . $error[22] . ". Die Transaktion an diesen Empfänger ging nicht durch!";
  }
  }
  return $out_array;
}

function revoke_transaction($id, $su) {

  global $conn, $error, $adjusters, $user, $state_id;
  
  $result_check = $conn->query(sprintf("SELECT 1 FROM transactions WHERE `id`='" . $id . "';"));
  if ($result_check->num_rows !== 1) { echo $error[23]; exit(); }
  

  
  $query = "SELECT `f_id`,`f_cat`,`t_id`,`t_cat`,`amount`,`tax_amount`,`revoked` FROM transactions WHERE `id`=" . $id . ";";
  $result = $conn->query($query);
  $data = $result->fetch_assoc();
  
  if ($data['revoked'] === 1) {
    echo $error[24]; exit();
  }
  

  // Check if older than 2 Minutes
  if (!$su) {
    $check_age = (int)$conn->query("SELECT IF(MINUTE(TIMEDIFF(NOW(), (SELECT `date` from `transactions` where `id`=" . $id . " ))) >= 2, 1, 0) AS too_old;")->fetch_assoc()['too_old'];
    if ($check_age) {
        echo $error[26]; exit();
    }
  }
  
  $query_to     = "UPDATE " . $data['t_cat'] . " SET account=account-" . ($data['amount'] - $data['tax_amount']) . " WHERE id=" . $data['t_id'] . ";";
  $query_from   = "UPDATE " . $data['f_cat'] . " SET account=account+" .  $data['amount'] . "                        WHERE id=" . $data['f_id'] . ";";
  $query_state  = "UPDATE companies              SET account=account-" .  $data['tax_amount'] . "                    WHERE id=1            ;";
  $query_revoke = "UPDATE transactions           SET revoked=1                                                       WHERE id=" . $id  . ";";

  $result_to     = $conn->query($query_to);
  $result_from   = $conn->query($query_from);
  $result_state  = $conn->query($query_state);
  $result_revoke = $conn->query($query_revoke);
  
  if (!$result_to || !$result_from || !$result_state || !$result_revoke) {
    echo $error[25]; exit();
  }

  
  
  if ($data['f_cat'] == "users") {
    $f_name = $conn->query("SELECT f_name FROM users WHERE id=" . $data['f_id'] . " ;")->fetch_assoc()['f_name'] . " " . $conn->query("SELECT l_name FROM users WHERE id=" . $data['f_id'] . " ;")->fetch_assoc()['l_name'];
  } else if ($data['f_cat'] == "companies") {
    $f_name = $conn->query("SELECT display_name FROM companies WHERE id=" . $data['f_id'] . " ;")->fetch_assoc()['display_name'];
  }
  
  if ($data['t_cat'] == "users") {
    $t_name = $conn->query("SELECT f_name FROM users WHERE id=" . $data['t_id'] . " ;")->fetch_assoc()['f_name'] . " " . $conn->query("SELECT l_name FROM users WHERE id=" . $data['t_id'] . " ;")->fetch_assoc()['l_name'];
  } else if ($data['t_cat'] == "companies") {
    $t_name = $conn->query("SELECT display_name FROM companies WHERE id=" . $data['t_id'] . " ;")->fetch_assoc()['display_name'];
  }
  
  $query_get = "SELECT `id`,`f_cat`,`f_id`,'" . $f_name . "' AS `f_name`,`t_cat`,`t_id`,'" . $t_name . "' AS `t_name`,`amount`,`transamount`,`tax_amount`,`tax_set`,`date`,`description`,`cashier`, `revoked` FROM transactions WHERE `f_cat`='" . $data['f_cat'] . "' AND `f_id`=" . $data['f_id'] . " AND `t_cat`='" . $data['t_cat'] . "' AND `t_id`=" . $data['t_id'] . " ORDER BY `id` DESC;";
  
  $result_get = $conn->query($query_get);
  
  if ($result_get->num_rows >= 1) {
    $i = 0;
    while ($row = $result_get->fetch_assoc()) {
      $out_array[$i] = array("id" => $row["id"], "f_cat" => $row['f_cat'], "f_id" => $row['f_id'], "f_name" => $row['f_name'], "t_cat" => $row['t_cat'], "t_id" => $row['t_id'], "t_name" => $row['t_name'], "amount" => $row['amount'], "transamount" => $row['transamount'], "tax_amount" => $row['tax_amount'], "tax_set" => $row['tax_set'], "date" => $row['date'], "description" => $row['description'], "cashier" => $row['cashier'], "revoked" => $row['revoked']);
      $i++;
    }
  } else {
    echo $error[22]; exit();
  }
  return $out_array;
  
}

function change($mode, $id, $cat, $name, $amount) {

    global $conn, $error, $adjusters, $user, $state_id;

    if ($cat === "visitors") {
        if ($name == "") {
            echo $error[30]; exit();
        }
        $log_name = $name;
        $last_query =$conn->query("SELECT (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`date`)) AS `date` FROM `changes` WHERE `t_name`='" . $log_name . "' ORDER BY id DESC LIMIT 1;")->fetch_assoc()['date'];
            if ($last_query < 15 ) {
	if ($last_query) {
                echo "Letzte Wechslung mit diesem Kunden ist weniger als 15 Sekunden her. Falls nicht ist es möglich, dass diese Aktion 2 Mal auf einmal an den Server verschickt wurde. Unbedenklich: Aktion wurde ausführt!"; exit();
}
            }
        if ($mode === "buy") {
            $payout      = (1 - $adjusters['foreignertopari']) * ($amount / $adjusters['rate']);  // In Pari
            $stateamount = $adjusters['foreignertopari'] * ($amount / $adjusters['rate']);        // In Pari 
            $pot         = $amount;                                                               // In Euro
            
            echo "<p><h4>@Sachbearbeiter: Vom Kunden " . $amount . "€ nehmen!</h4></p>";                                                             // In Euro
            echo "<p><h4>@Sachbearbeiter: Dem Kunden " . $payout . "Ꝓ auszahlen!</h4></p>";   
            
            $query_log  = "INSERT INTO `changes` (`t_cat`,`t_name`,`mode`,`amount`,`nominal_rate`,`deviation`,`payout`,`stateamount`,`date`,`cashier`) VALUE ('" . $cat . "', '" . $log_name . "', '" . $mode . "', " . $amount . ", " . $adjusters['rate'] . ", " . $adjusters['foreignertopari'] . ", " . $payout . ", " . $stateamount . ", NOW(), '" . $user . "' );";
            $query_pot  = "UPDATE `pots` SET pot=pot+" . $pot . " WHERE `name`='euro_pot';";
            $query_pot_cash = "UPDATE `pots` SET pot=pot+" . $payout . " WHERE `name`='cash_pot';";
            
           
            
            $result_log       = $conn->query($query_log);
            $result_pot       = $conn->query($query_pot);
            $result_pot_cash  = $conn->query($query_pot_cash);
            
            if (!$result_log || !$result_pot || !$result_pot_cash) {
                echo $error[29]; exit();
            }
            
            if ($stateamount > 0) {
                $query_state = "UPDATE companies SET account=account+" . $stateamount . " WHERE id=1;";
                $result_state = $conn->query($query_state);
                if (!$result_state) {
                    echo $error[29]; exit();
                }
            }
            
        } else if ($mode === "sell") {
            $payout      = (1 - $adjusters['foreignertoeuro']) * ($amount * $adjusters['rate']);  // In Euro
            $stateamount = $adjusters['foreignertoeuro'] * $amount;                               // In Pari
            $pot         = $payout;                                                               // In Euro
              
            echo "<p><h4>@Sachbearbeiter: Vom Kunden " . $amount . "Ꝓ nehmen!</h4></p>";                                                        // In Euro
            echo "<p><h4>@Sachbearbeiter: Dem Kunden " . $payout . "€ auszahlen!</h4></p>"; 
            
            $query_log  = "INSERT INTO `changes` (`t_cat`,`t_name`,`mode`,`amount`,`nominal_rate`,`deviation`,`payout`,`stateamount`,`date`,`cashier`) VALUE ('" . $cat . "', '" . $log_name . "', '" . $mode . "', " . $amount . ", " . $adjusters['rate'] . ", " . $adjusters['foreignertoeuro'] . ", " . $payout . ", " . $stateamount . ", NOW(), '" . $user . "' );";
            $query_pot  = "UPDATE `pots` SET pot=pot-" . $pot . " WHERE `name`='euro_pot';";
            $query_pot_cash = "UPDATE `pots` SET pot=pot-" . $amount . " WHERE `name`='cash_pot';";
            
            $result_log       = $conn->query($query_log);
            $result_pot       = $conn->query($query_pot);
            $result_pot_cash  = $conn->query($query_pot_cash);
            
            if (!$result_log || !$result_pot || !$result_pot_cash) {
                echo $error[29]; exit();
            }
            
            if ($stateamount > 0) {
                $query_state = "UPDATE companies SET account=account+" . $stateamount . " WHERE id=1;";
                $result_state = $conn->query($query_state);
                if (!$result_state) {
                    echo $error[29]; exit();
                }
            }
        }
                
        $query_get = "SELECT `id`,`t_cat`,`t_name`,`mode`,`amount`,`nominal_rate`,`deviation`,`payout`,`stateamount`,`date`,`cashier`,`revoked` FROM changes WHERE `t_name`='" . $log_name . "'  ORDER BY `id` DESC;";
  
        $result_get = $conn->query($query_get);
  
        if ($result_get->num_rows >= 1) {
            $i = 0;
            while ($row = $result_get->fetch_assoc()) {
                $out_array[$i] = array("id" => $row["id"], "t_cat" => $row['t_cat'], "t_id" => '', "t_name" => $row['t_name'], "mode" => $row['mode'], "amount" => $row['amount'], "nominal_rate" => $row['nominal_rate'], "deviation" => $row['deviation'], "payout" => $row['payout'], "stateamount" => $row['stateamount'], "date" => $row['date'], "cashier" => $row['cashier'], "revoked" => $row['revoked']);
                $i++;
            }
        } else {
            echo $error[22]; exit();
        }
        return $out_array;
    } else {
            
        if ($cat == "users") {
            $log_name = $conn->query("SELECT f_name FROM users WHERE id=" . $id . " ;")->fetch_assoc()['f_name'] . " " . $conn->query("SELECT l_name FROM users WHERE id=" . $id . " ;")->fetch_assoc()['l_name'];
        } else if ($cat == "companies") {
            $log_name = $conn->query("SELECT display_name FROM companies WHERE id=" . $id . " ;")->fetch_assoc()['display_name'];
        }
        
         
        $last_query =$conn->query("SELECT (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`date`)) AS `date` FROM `changes` WHERE `t_name`='" . $log_name . "' ORDER BY id DESC LIMIT 1;")->fetch_assoc()['date'];
            if ($last_query < 15 ) {
	if ($last_query) {
                echo "Letzte Wechslung mit diesem Kunden ist weniger als 15 Sekunden her. Falls nicht ist es möglich, dass diese Aktion 2 Mal auf einmal an den Server verschickt wurde. Bitte Kontoauszug kontrollieren, ob Aktion bereits ausgeführt und NICHT NOCH EINMAL versuchen!"; exit();
}
            }
        
        if (!exists_id($id, $cat)) {
            echo $error[0]; exit();
        }
    
        if ($mode === "buy") {
            $payout      = (1 - $adjusters['toparifee']) * ($amount / $adjusters['rate']);  // In Pari
            $stateamount = $adjusters['toparifee'] * ($amount / $adjusters['rate']);        // In Pari 
            $pot         = $amount;                                                         // In Euro
            
            echo "<p><h4>@Sachbearbeiter: Vom Kunden " . round($amount, 2) . "€ nehmen!</h4></p>";
        
            $query_user = "UPDATE `" . $cat . "` SET account=account+" . $payout . " WHERE id=" . $id . ";";
            $query_log  = "INSERT INTO `changes` (`t_id`,`t_cat`,`t_name`,`mode`,`amount`,`nominal_rate`,`deviation`,`payout`,`stateamount`,`date`,`cashier`) VALUE (" . $id . ", '" . $cat . "', '" . $log_name . "', '" . $mode . "', " . $amount . ", " . $adjusters['rate'] . ", " . $adjusters['toparifee'] . ", " . $payout . ", " . $stateamount . ", NOW(), '" . $user . "' );";
            $query_pot  = "UPDATE `pots` SET pot=pot+" . $pot . " WHERE `name`='euro_pot';";
            
            $result_user = $conn->query($query_user);
            $result_log  = $conn->query($query_log);
            $result_pot  = $conn->query($query_pot);

            if (!$result_user || !$result_log || !$result_pot) {
                echo $error[29]; exit();
            }
            
            if ($stateamount > 0) {
                $query_state = "UPDATE companies SET account=account+" . $stateamount . " WHERE id=1;";
                $result_state = $conn->query($query_state);
                if (!$result_state) {
                    echo $error[29]; exit();
                }
            }
        } else if ($mode === "sell") {
            $payout      = (1 - $adjusters['toeurofee']) * ($amount * $adjusters['rate']);  // In Euro
            $stateamount = $adjusters['toeurofee'] * $amount;                               // In Pari
            $pot         = $payout;                                                         // In Euro
            
            echo "<p><h4>@Sachbearbeiter: Dem Kunden " . round($payout, 2) . "€ auszahlen!</h4></p>";
        
            $prev_account=(float)$conn->query("SELECT `account` FROM `" . $cat . "` WHERE `id`=" . $id . ";")->fetch_assoc()['account'];
            if ($prev_account < $amount) {
                echo $error[30]; exit();
            }
            
            $query_user = "UPDATE `" . $cat . "` SET account=account-" . $amount . " WHERE id=" . $id . ";";
            $query_log  = "INSERT INTO `changes` (`t_id`,`t_cat`,`t_name`,`mode`,`amount`,`nominal_rate`,`deviation`,`payout`,`stateamount`,`date`,`cashier`) VALUE (" . $id . ", '" . $cat . "', '" . $log_name . "', '" . $mode . "', " . $amount . ", " . $adjusters['rate'] . ", " . $adjusters['toeurofee'] . ", " . $payout . ", " . $stateamount . ", NOW(), '" . $user . "' );";
            $query_pot  = "UPDATE `pots` SET pot=pot-" . $pot . " WHERE `name`='euro_pot';";
            
            $result_user = $conn->query($query_user);
            $result_log  = $conn->query($query_log);
            $result_pot  = $conn->query($query_pot);

            if (!$result_user || !$result_log || !$result_pot) {
                echo $error[29]; exit();
            }
            
            if ($stateamount > 0) {
                $query_state = "UPDATE companies SET account=account+" . $stateamount . " WHERE id=1;";
                $result_state = $conn->query($query_state);
                if (!$result_state) {
                    echo $error[29]; exit();
                }
            }
        }
        
        
        $query_get = "SELECT `id`,`t_cat`,`t_id`,`t_name`,`mode`,`amount`,`nominal_rate`,`deviation`,`payout`,`stateamount`,`date`,`cashier`,`revoked` FROM changes WHERE `t_id`=" . $id . " ORDER BY `id` DESC;";
  
        $result_get = $conn->query($query_get);
  
        if ($result_get->num_rows >= 1) {
            $i = 0;
            while ($row = $result_get->fetch_assoc()) {
                $out_array[$i] = array("id" => $row["id"], "t_cat" => $row['t_cat'], "t_id" => $row['t_id'], "t_name" => $row['t_name'], "mode" => $row['mode'], "amount" => $row['amount'], "nominal_rate" => $row['nominal_rate'], "deviation" => $row['deviation'], "payout" => $row['payout'], "stateamount" => $row['stateamount'], "date" => $row['date'], "cashier" => $row['cashier'], "revoked" => $row['revoked']);
                $i++;
            }
        } else {
            echo $error[22]; exit();
        }
        return $out_array;
    }
}

function revoke_change($id, $su) {

    global $conn, $error, $adjusters, $user, $state_id;
    
    if (!$su) {
        $check_age = (int)$conn->query("SELECT IF(MINUTE(TIMEDIFF(NOW(), (SELECT `date` from `changes` where `id`=" . $id . " ))) >= 2, 1, 0) AS too_old;")->fetch_assoc()['too_old'];
        if ($check_age) {
            echo $error[26]; exit();
        }
    }
    
    $query_get = "SELECT `id`,`t_cat`,`t_id`,`t_name`,`mode`,`amount`,`nominal_rate`,`deviation`,`payout`,`stateamount`,`date`,`cashier` FROM changes WHERE `id`=" . $id . ";";
  
    $result_get = $conn->query($query_get);
  
    if ($result_get->num_rows >= 1) {
        $i = 0;
        while ($row = $result_get->fetch_assoc()) {
            $data = array("id" => $row["id"], "t_cat" => $row['t_cat'], "t_id" => $row['t_id'], "t_name" => $row['t_name'], "mode" => $row['mode'], "amount" => $row['amount'], "nominal_rate" => $row['nominal_rate'], "deviation" => $row['deviation'], "payout" => $row['payout'], "stateamount" => $row['stateamount'], "date" => $row['date'], "cashier" => $row['cashier']);
            $i++;
        }
    } else {
        echo $error[22]; exit();
    }

    
    if ($data['mode'] == "buy") {
        $query_user = "UPDATE `" . $data['t_cat'] . "` SET account=account-" . $data['payout'] . " WHERE id=" . $data['t_id'] . " ;";
        $query_pot  = "UPDATE `pots` SET `pot`=`pot`-" . $data['amount'] . " WHERE `name`='euro';"; 
        
        $result_user = $conn->query($query_user);
        $result_pot  = $conn->query($query_pot);
        
        if (!$result_user || !$result_pot) {
            echo $error[29]; exit();
        }
        
        echo "<p><h4>@Sachbearbeiter: Dem Kunden " . round($data['amount'], 2) . "€ aushändigen!</p><br>";
        
        $query_revoke = "UPDATE `changes` SET `revoked`=1 WHERE `id`=" . $id . " ;";
        $result_revoke = $conn->query($query_revoke);
        
        if (!$result_revoke) {
            echo $error[37]; exit();
        }
    } else if ($data['mode'] == "sell") {
        $query_user = "UPDATE `" . $data['t_cat'] . "` SET account=account+" . $data['amount'] . " WHERE id=" . $data['t_id'] . " ;";
        $query_pot  = "UPDATE `pots` SET `pot`=`pot`-" . $data['payout'] . " WHERE id=1;"; 
        
        $return_user = $conn->query($query_user);
        $return_pot  = $conn->query($query_pot);
        
        if (!$result_user || !$result_pot) {
            echo $error[29]; exit();
        }
        
        echo "<p><h4>@Sachbearbeiter: Vom Kunden " . round($data['payout'], 2) . "€ verlangen!</p><br>";
        
        $query_revoke = "UPDATE `changes` SET `revoked`=1 WHERE `id`=" . $id . " ;";
        $result_revoke = $conn->query($query_revoke);
        
        if (!$result_revoke) {
            echo $error[37]; exit();
        }
    }
        
    if ($data['stateamount'] > 0) {
        $query_state = "UPDATE companies SET account=account-" . $data['stateamount'] . " WHERE id=1;";
        $result_state = $conn->query($query_state);
        if (!$result_state) {
            echo $error[29]; exit();
        }
    }
    
    $query_get = "SELECT `id`,`t_cat`,`t_id`,`t_name`,`mode`,`amount`,`nominal_rate`,`deviation`,`payout`,`stateamount`,`date`,`cashier`,`revoked` FROM changes WHERE `t_id`=" . $data['t_id'] . " ORDER BY `id` DESC;";
  
    $result_get = $conn->query($query_get);
  
    if ($result_get->num_rows >= 1) {
        $i = 0;
        while ($row = $result_get->fetch_assoc()) {
            $out_array[$i] = array("id" => $row["id"], "t_cat" => $row['t_cat'], "t_id" => $row['t_id'], "t_name" => $row['t_name'], "mode" => $row['mode'], "amount" => $row['amount'], "nominal_rate" => $row['nominal_rate'], "deviation" => $row['deviation'], "payout" => $row['payout'], "stateamount" => $row['stateamount'], "date" => $row['date'], "cashier" => $row['cashier'], "revoked" => $row['revoked']);
            $i++;
        }
    } else {
        echo $error[22]; exit();
    }
    return $out_array;
}

function pay_in_out($mode, $id, $cat, $amount, $tax, $su) {

    global $conn, $error, $adjusters, $user, $state_id;

    
    $registered=(int)$conn->query("SELECT `presence` FROM `users` WHERE `id`=" . $id . " ;")->fetch_assoc()['presence'];    

    if (!$registered && $cat === "users") {
	echo $error[40]; exit();
    }
    if (!exists_id($id, $cat)) {
        echo $error[0]; exit();
    }
    
    // Superuser Checkups
    if (!$su){
        #if ($cat === "users" && $mode === "in" &&  $amount > 300 ) {
        #    echo $error[34]; exit();
        #}
        if ($cat === "companies" && $mode === "out") {
            echo $error[35]; exit();
        }
    }
    $last_query =$conn->query("SELECT (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`date`)) AS `date` FROM `pay_in_out` WHERE `t_id`=" . $id . " ORDER BY id DESC LIMIT 1;")->fetch_assoc()['date'];
    if ($last_query < 15 ) {
	if ($last_query) {
        echo "Letzte Ein-/Auszahlung dieser ID ist weniger als 15 Sekunden her. Aus Angst vor Fehlern in der Datenbank wird die Aktion nicht durchgeführt. Bitte vorherige Aktion überprüfen, falls unsicher."; exit();
}
    }
    
    // Einzahlung
    if ($mode === "in") {
        if ($tax === "MwSt" && $cat === "users") {
            echo "Beim Einzahlen auf Bürgerkonto kann keine Steuer anfallen! Transaktion wird nicht durchgeführt!"; exit();
        }
        if ($tax === "MwSt") {
            $pamount     = $amount * (1/(1+$adjusters['mwst']));
            $stateamount = $amount - $pamount;
            $query_state = "UPDATE `companies` SET `account`=account+" . $stateamount . " WHERE `id`=1 ;";
        } else {
            $pamount     = $amount;
            $stateamount = 0;
        }
        
        $query_user = "UPDATE `" . $cat . "` SET account=account+" . $pamount . " WHERE `id`=" . $id . ";";
        $query_pot  = "UPDATE `pots` SET `pot`=pot-" . $amount . " WHERE `name`='cash_pot';";
    // Auszahlung
    } else if ($mode === "out") {
        $pamount = $amount;
	$stateamount = 0;
        if ($tax === "MwSt") {
            echo "Beim Auszahlen kann keine Steuer anfallen! Transaktion wird nicht durchgeführt!"; exit();
        }
        $prev_account=(float)$conn->query("SELECT `account` FROM `" . $cat . "` WHERE `id`=" . $id . ";")->fetch_assoc()['account'];
        if ($prev_account < $pamount) {
            echo $error[36]; exit();
        }
        $query_user = "UPDATE `" . $cat . "` SET account=account-" . $amount . " WHERE `id`=" . $id . ";";
        $query_pot  = "UPDATE `pots` SET `pot`=pot+" . $amount . " WHERE `name`= 'cash_pot';";
    }
    $query_log = "INSERT INTO `pay_in_out` (`t_id`,`t_cat`,`mode`,`amount`,`transamount`,`tax_set`,`tax_amount`,`date`,`cashier`) VALUE (" . $id . ", '" . $cat . "', '" . $mode . "', " . $amount . ", " . $pamount . ", " . (1-(1/(1+$adjusters['mwst']))) . ", " . $stateamount . ", NOW(), '" . $user . "');";
    
    
    
    $result_user = $conn->query($query_user);
    if (!$result_user) {
	echo $error[32]; exit();
    }
    $result_log  = $conn->query($query_log);
    if (!$result_log) {
	echo $error[32]; exit();
    }
    $result_pot  = $conn->query($query_pot);
    if (!$result_pot) {
	echo $error[32];  exit();
    }
    
    if ($tax === "MwSt") {
        $result_state  = $conn->query($query_state);
        if (!$result_state) {
            echo $error[39]; echo $conn->error; exit;
        }
    }

    if ($cat == "users") {
        $name = $conn->query("SELECT f_name FROM users WHERE id=" . $id . " ;")->fetch_assoc()['f_name'] . " " . $conn->query("SELECT l_name FROM users WHERE id=" . $id . " ;")->fetch_assoc()['l_name'];
    } else if ($cat == "companies") {
        $name = $conn->query("SELECT display_name FROM companies WHERE id=" . $id . " ;")->fetch_assoc()['display_name'];
    }
    
    $query_get = "SELECT `id`,`t_cat`,`t_id`,'" . $name . "' AS `t_name`,`mode`,`amount`,`transamount`,`tax_set`,`tax_amount`,`date`,`cashier` FROM pay_in_out WHERE `t_id`=" . $id . " AND `t_cat`='" . $cat . "' ORDER BY `id` DESC;";
  
    $result_get = $conn->query($query_get);
  
  if ($result_get->num_rows >= 1) {
    $i = 0;
    while ($row = $result_get->fetch_assoc()) {
      $out_array[$i] = array("id" => $row["id"], "t_cat" => $row['t_cat'], "t_id" => $row['t_id'], "t_name" => $row['t_name'], "mode" => $row['mode'], "amount" => $row['amount'], "transamount" => $row['transamount'], "tax_set" => $row['tax_set'], "tax_amount" => $row['tax_amount'], "date" => $row['date'], "cashier" => $row['cashier']);
      $i++;
    }
  } else {
    echo $error[22]; exit();
  }
  return $out_array;
}
    
function presence($u_id, $presence, $search, $missing) {

    global $conn, $error, $user;
    
    if ($missing) {
        $query = "SELECT `id`,`f_name`,`l_name`,`presence` FROM `users` WHERE `presence`=0 ORDER BY `id` ASC;";
        $result      = $conn->query($query);
        $out_array = "";
        if ($result->num_rows >= 1) {
            $i = 0;
            while ($row = $result->fetch_assoc()) {
                $out_array[$i] = array("id" => $row["id"], "f_name" => $row['f_name'], "l_name" => $row["l_name"], "presence" => $row["presence"]);
                $i++;
            }
        } else {
            echo $error[9];
        }
        return $out_array;
    }
    
    if ($presence >= 0) {
        if (!exists_id($u_id, "users")) {
            echo $error[0]; exit;
        }
        
        $query = "INSERT INTO `presence` (`u_id`, `presence`, `date`, `cashier`) VALUE (" . $u_id . " , " . $presence . " , NOW() , '" . $user . "' );";
        $query_user = "UPDATE `users` SET `presence`= ". $presence . " WHERE `id`=" . $u_id . " ;";

        $result      = $conn->query($query);
        $result_user = $conn->query($query_user);
    
        if (!$result || !$result_user) {
            echo $error[12]; exit();
        }
    }
 
    if ($u_id) { 
        $query_get = "SELECT `id`,`u_id`,(SELECT f_name FROM users WHERE id=u_id) AS f_name,(SELECT l_name FROM users WHERE id=u_id) AS l_name,`presence`,`date`,`cashier` FROM `presence` WHERE `u_id` = " . $u_id .   " ORDER BY `id` DESC;";
    } else {
        $query_get = "SELECT `id`,`u_id`,(SELECT f_name FROM users WHERE id=u_id) AS f_name,(SELECT l_name FROM users WHERE id=u_id) AS l_name,`presence`,`date`,`cashier` FROM `presence` ORDER BY `id` DESC;";
    }
    $result_get = $conn->query($query_get);
    $out_array = "";
    if ($result_get->num_rows >= 1) {
        $i = 0;
        while ($row = $result_get->fetch_assoc()) {
            $out_array[$i] = array("id" => $row["id"], "u_id" => $row["u_id"], "f_name" => $row['f_name'], "l_name" => $row["l_name"], "presence" => $row["presence"], "date" => $row["date"], "cashier" => $row["cashier"]);
            $i++;
        }
    } else {
        echo $error[9];
    }
    
    return $out_array;
    
}


#---- Superuser Stats ----#

function count_all_money() {
    global $conn, $error, $user;
        
    $query_users     = "SELECT `account` FROM users;";
    $query_companies = "SELECT `account` FROM companies WHERE id != 1;";
    $query_state     = "SELECT `account` FROM companies WHERE id = 1;";
        
    $result_users     = $conn->query($query_users);
    $result_companies = $conn->query($query_companies);
    $result_state     = $conn->query($query_state);

    $data = array( "absolute" => array(), "percentage" => array() );
    
    if ($result_users->num_rows >= 1) {
        $data['absolute']['users'] = 0;
        while ($row = $result_users->fetch_assoc()) {
            $data['absolute']['users'] += (float)$row["account"];
        }
    } else {
        echo $error[9];
    }
    if ($result_companies->num_rows >= 1) {
        $data['absolute']['companies'] = 0;
        while ($row = $result_companies->fetch_assoc()) {
            $data['absolute']['companies'] += (float)$row["account"];
        }
    } else {
        echo $error[9];
    }
    if ($result_state->num_rows >= 1) {
        $data['absolute']['state'] = 0;
        while ($row = $result_state->fetch_assoc()) {
            $data['absolute']['state'] += (float)$row["account"];
        }
    } else {
        echo $error[9];
    }
    
    $data['absolute']['cash']   = (float)$conn->query("SELECT `pot` FROM `pots` WHERE `name`='cash_pot';")->fetch_assoc()['pot'];
    $data['absolute']['total']  = $data['absolute']['users'] + $data['absolute']['companies'] + $data['absolute']['state'] + $data['absolute']['cash'];
    $data['percentage']['users'] = 100 * ($data['absolute']['users']/$data['absolute']['total']);
    $data['percentage']['companies'] = 100 * ($data['absolute']['companies']/$data['absolute']['total']);
    $data['percentage']['state'] = 100 * ($data['absolute']['state']/$data['absolute']['total']);
    $data['percentage']['cash'] = 100 * ($data['absolute']['cash']/$data['absolute']['total']);
    $data['percentage']['total']= 100;
    

    return $data;
}
    
function calculate_rate() {
    global $conn, $error, $user;
    
    $euro_pot=(float)$conn->query("SELECT `pot` FROM `pots` WHERE `name`='euro_pot';")->fetch_assoc()['pot'];
    
    $total_amount = count_all_money()['absolute']['total'];
    
    $rate=$euro_pot / $total_amount;
    
    return $rate;
    
}

function calculate_inflation($startday, $stopday) {
    global $conn, $error, $user;
    
    $start_rate=(float)$conn->query("SELECT `rate` FROM `rates` WHERE `day`=" . $startday . " ;")->fetch_assoc()['rate'];
    $stop_rate =(float)$conn->query("SELECT `rate` FROM `rates` WHERE `day`=" . $stopday . " ;")->fetch_assoc()['rate'];
    
    $inflation = 1 - ($start_rate/$stop_rate);
    
    return $inflation;
    
}
    
    
    
    
    
    
    
    
    
    
        
?>
