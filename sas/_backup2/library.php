<?php

// Debugging
error_reporting(-1);
error_reporting(E_ALL);
ini_set('display_errors', 'On');

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
#if (!$_SERVER['REMOTE_USER']) {
#    echo $error[19]; exit();
#} else {
#    $user      = $_SERVER['REMOTE_USER'];
#}

//Adjuster Variables:
$adjusters= array();
$adjusters['mwst']      =(float)$conn->query("SELECT `value` FROM `adjusters` WHERE `name`='MwSt';")->fetch_assoc()['value'];
$adjusters['income_tax']=(float)$conn->query("SELECT `value` FROM `adjusters` WHERE `name`='Einkommenssteuer';")->fetch_assoc()['value'];
$adjusters['rate']      =(float)$conn->query("SELECT `value` FROM `adjusters` WHERE `name`='Wechselkurs';")->fetch_assoc()['value'];
$adjusters['toeurofee'] =(float)$conn->query("SELECT `value` FROM `adjusters` WHERE `name`='UmtauschZuEuro';")->fetch_assoc()['value'];
$adjusters['toparifee'] =(float)$conn->query("SELECT `value` FROM `adjusters` WHERE `name`='UmtauschZuPari';")->fetch_assoc()['value'];
$adjusters['foreigner'] =(float)$conn->query("SELECT `value` FROM `adjusters` WHERE `name`='FremdenWechselGebuehr';")->fetch_assoc()['value'];

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
  
  if ($args['f_name'] === NULL || $args['l_name'] === NULL) {
      echo $error[10]; exit();
  }

  if ($args['id'] === NULL) {
    $query = "INSERT INTO users (`f_name`, `l_name`, `c_id`) VALUES ('" . $args['f_name'] . "', '" . $args['l_name'] . "', ";
    
    if ($args['c_id']) {
      $query .= $c_id . " );";
    } else {
      $query .= "NULL );";
    }
    $new = TRUE;
    
  } else {
    if (!exists_id($args['id'])) {
      echo $error[11]; exit();
    }
    
    $query = "REPLACE INTO users (`id`, `f_name`, `l_name`, `c_id`) VALUES (" . $args['id'] . ", '" . $args['f_name'] . "', '" . $args['l_name'] . "', ";
    
    if ($args['c_id']) {
      $query .= $c_id . " );";
    } else {
      $query .= "NULL );";
    }
  }
  
  $result = $conn->query($query);
  
  if (!$result) {
    echo $error[12]; exit();
  } else {
      $new_details = get_details(NULL, $args['f_name'], $args['l_name'], "users");
      $output = "";
      foreach ($new_details as $row) {
        foreach ($row as $value) {
          $output .= $value . " ";
        }
          $output .= "<br>";
      }
      return "Erfolgreich Eingetragen!: " . $output;
  }
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
    if (!exists_id($args['id'])) {
      echo $error[11]; exit();
    }
    
    $query = "REPLACE INTO companies (`id`, `display_name`, `ceo_id`) VALUES ('" . $args['id'] . "', '" . $args['display_name'] . "', '" . $args['ceo_id'] . "');";
  }
  
  $result = $conn->query($query);
  
  if (!$result) {
      echo $error[12]; exit();
  } else {
    $new_details = get_details_company($id, $display_name);
    $output = "";
    foreach ($new_details as $row) {
    foreach ($row as $value) {
      $output .= $value . " ";
    }
    $output .= "<br>";
  }
    return "Erfolgreich Eingetragen!: " . $output;
  }
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

function generate_reciept($id, $cat, $limit) {
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
  
  $query_get = "SELECT `id`,`f_cat`,`f_id`,`f_name`,`t_cat`,`t_id`,`t_name`,`amount`,`transamount`,`tax_amount`,`tax_set`,`date`,`description`,`cashier`,`revoked` FROM transactions WHERE `f_id`=" . $id . " AND `f_cat`='" . $cat . "' OR `t_id`=" . $id . " AND `t_cat`='" . $cat . "';";
  $result_get = $conn->query($query_get);
  
  if ($result_get->num_rows >= 1) {
    $i = 0;
    while ($row = $result_get->fetch_assoc()) {
      $out_array['transactions'][$i] = array("id" => $row["id"], "f_cat" => $row['f_cat'], "f_id" => $row['f_id'], "f_name" => $row['f_name'], "t_cat" => $row['t_cat'], "t_id" => $row['t_id'], "t_name" => $row['t_name'], "amount" => $row['amount'], "transamount" => $row['transamount'], "tax_amount" => $row['tax_amount'], "tax_set" => $row['tax_set'], "date" => $row['date'], "description" => $row['description'], "cashier" => $row['cashier'], "revoked" => $row['revoked']);
      $i++;
      if ($limit != 0) {
        if ($i > $limit) {
          break;
        }
      }
    }
  } else {
    echo $error[28];
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
  
  $query_get = "SELECT `id`,`f_cat`,`f_id`,'" . $f_name . "' AS `f_name`,`t_cat`,`t_id`,'" . $t_name . "' AS `t_name`,`amount`,`transamount`,`tax_amount`,`tax_set`,`date`,`description`,`cashier`, `revoked` FROM transactions WHERE `f_cat`='" . $f_cat . "' AND `f_id`=" . $f_id . " AND `t_cat`='" . $t_cat . "' AND `t_id`=" . $t_id . ";";
  
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

function revoke_transaction($id) {

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
  $check_age = (int)$conn->query("SELECT IF(MINUTE(TIMEDIFF(NOW(), (SELECT `date` from `transactions` where `id`=" . $id . " ))) >= 2, 1, 0) AS too_old;")->fetch_assoc()['too_old'];
  if ($check_age) {
    echo $error[26]; exit();
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
  
  $query_get = "SELECT `id`,`f_cat`,`f_id`,'" . $f_name . "' AS `f_name`,`t_cat`,`t_id`,'" . $t_name . "' AS `t_name`,`amount`,`transamount`,`tax_amount`,`tax_set`,`date`,`description`,`cashier`, `revoked` FROM transactions WHERE `f_cat`='" . $data['f_cat'] . "' AND `f_id`=" . $data['f_id'] . " AND `t_cat`='" . $data['t_cat'] . "' AND `t_id`=" . $data['t_id'] . ";";
  
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

function pay_in_out($mode, $id, $cat, $name, $amount) {

    global $conn, $error, $adjusters, $user, $state_id;

    if ($cat === "visitor") {
    
    } else {
        if (!exists_id($id, $cat)) {
            echo $error[0]; exit();
        }
    
        if ($mode === "buy") {
            $payout      = (1 - $adjusters['toparifee']) * ($amount / $adjusters['rate']);  // In Pari
            $stateamount = $adjusters['toparifee'] * ($amount / $adjusters['rate']);        // In Pari 
            $pot         = $amount;                                                         // In Euro
            
            echo "<p><h4>@Sachbearbeiter: Vom Kunden " . $amount . "€ nehmen!</h4></p>";
        
            $query_user = "UPDATE `" . $cat . "` SET account=account+" . $payout . " WHERE id=" . $id . ";";
            $query_log  = "INSERT INTO `changes` (`t_id`,`mode`,`amount`,`nominal_rate`,`deviation`,`payout`,`stateamount`,`date`,`cashier`) VALUE (" . $id . ", '" . $mode . "', " . $amount . ", " . $adjusters['rate'] . ", " . $adjusters['toparifee'] . ", " . $payout . ", " . $stateamount . ", NOW(), '" . $user . "' );";
            $query_pot  = "UPDATE `euro_pot` SET pot=pot+" . $pot . " WHERE `id`=1;";
            
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
            
            echo "<p><h4>@Sachbearbeiter: Dem Kunden " . $payout . "€ auszahlen!</h4></p>";
        
            $prev_account=(float)$conn->query("SELECT `account` FROM `" . $cat . "` WHERE `id`=" . $id . ";")->fetch_assoc()['account'];
            if ($prev_account < $amount) {
                echo $error[30]; exit();
            }
            
            $query_user = "UPDATE `" . $cat . "` SET account=account-" . $amount . " WHERE id=" . $id . ";";
            $query_log  = "INSERT INTO `changes` (`t_id`,`mode`,`amount`,`nominal_rate`,`deviation`,`payout`,`stateamount`,`date`,`cashier`) VALUE (" . $id . ", '" . $mode . "', " . $amount . ", " . $adjusters['rate'] . ", " . $adjusters['toeurofee'] . ", " . $payout . ", " . $stateamount . ", NOW(), '" . $user . "' );";
            $query_pot  = "UPDATE `euro_pot` SET pot=pot-" . $pot . " WHERE `id`=1;";
            
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
        
        if ($cat == "users") {
            $log_name = $conn->query("SELECT f_name FROM users WHERE id=" . $id . " ;")->fetch_assoc()['f_name'] . " " . $conn->query("SELECT l_name FROM users WHERE id=" . $id . " ;")->fetch_assoc()['l_name'];
        } else if ($cat == "companies") {
            $log_name = $conn->query("SELECT display_name FROM companies WHERE id=" . $id . " ;")->fetch_assoc()['display_name'];
        }
        
        $query_get = "SELECT `id`,'" . $cat . "' AS `t_cat`,`t_id`,'" . $log_name . "' AS `t_name`,`mode`,`amount`,`nominal_rate`,`deviation`,`payout`,`stateamount`,`date`,`cashier` FROM changes WHERE `t_id`=" . $id . " ;";
  
        $result_get = $conn->query($query_get);
  
        if ($result_get->num_rows >= 1) {
            $i = 0;
            while ($row = $result_get->fetch_assoc()) {
                $out_array[$i] = array("id" => $row["id"], "t_cat" => $row['t_cat'], "t_id" => $row['t_id'], "t_name" => $row['t_name'], "mode" => $row['mode'], "amount" => $row['amount'], "nominal_rate" => $row['nominal_rate'], "deviation" => $row['deviation'], "payout" => $row['payout'], "stateamount" => $row['stateamount'], "date" => $row['date'], "cashier" => $row['cashier']);
                $i++;
            }
        } else {
            echo $error[22]; exit();
        }
        return $out_array;
    
    }
}

function presence($u_id, $presence) {

    global $conn, $error, $user;
    
    if (!exists_id($u_id, "users")) {
        echo $error[0]; exit;
    }
    
    $query = "INSERT INTO `presence` (`u_id`, `presence`, `date`, `cashier`) VALUE (" . $u_id . " , " . $presence . " , NOW() , '" . $user . "' );";

    $result = $conn->query($query);
  
    if (!$result) {
        echo $error[12]; echo $conn->error; exit();
    } else {
        $query_get = "SELECT `u_id`,(SELECT f_name FROM users WHERE id=u_id) AS f_name,(SELECT l_name FROM users WHERE id=u_id) AS l_name,`presence`,`date`,`cashier` FROM `presence` WHERE `u_id` = " . $u_id . ";";
        $result_get = $conn->query($query_get);
        
        if ($result_get->num_rows >= 1) {
            $i = 0;
            while ($row = $result_get->fetch_assoc()) {
                $out_array[$i] = array("u_id" => $row["u_id"], "f_name" => $row['f_name'], "l_name" => $row["l_name"], "presence" => $row["presence"], "date" => $row["date"], "cashier" => $row["cashier"]);
                $i++;
            }
        } else {
            echo $error[9];
        }
        return $out_array;
    }
    
}

?>
