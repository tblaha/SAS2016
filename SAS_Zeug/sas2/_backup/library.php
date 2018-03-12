<?php

// Debugging
error_reporting(-1);
ini_set('display_errors', 'On');

include 'stellschrauben.php';

// General Variables:
$id_digits     = 5;					// Wie viele Stellen haben die ganzen ID's?
$categories    = array("users", "companies", "vips");	// Welche Kategorien gibt es?
$cat_id_offset = array(0, 10000, 90000);		// Wie sind die Id-Offsets dieser Kategorien?
$cat_id_range  = (pow(10, ($id_digits - 1)));		// Wie viele Id können pro Kategorie vergeben werden? --> 10^($id_digits -1). Bei $id_digits=5 --> 10000
$tax_user_id   = 90001;

$error[0]  = "Error 0  : Die ID ließ sich keinem Konto zuordnen";
$error[1]  = "Error 1  : Die ID ließ sich keinem Bereich zuordnen!";
$error[2]  = "Error 2  : Die ID ist keine Ganzzahl!";
$error[3]  = "Error 3  : Die Sender-ID ließ sich keinem Konto zuordnen";
$error[4]  = "Error 4  : Die Sender-ID ließ sich keinem Bereich zuordnen!";
$error[5]  = "Error 5  : Die Sender-ID ist keine Ganzzahl!";
$error[6]  = "Error 6  : Die Empfänger-ID ließ sich keinem Konto zuordnen";
$error[7]  = "Error 7  : Die Empfänger-ID ließ sich keinem Bereich zuordnen!";
$error[8]  = "Error 8  : Die Empfänger-ID ist keine Ganzzahl!";
$error[9]  = "Error 101: Es gibt keine Ergebnisse!";
$error[10] = "Error 102: Vorname oder Nachname nicht angegeben";
$error[11] = "Error 103: ID existiert nicht, zum Erstellen dieses Objektes, Feld leer lassen!";
$error[12] = "Error 9  : Datenbankfehler beim Anlegen oder Updaten des Kontos";
$error[13] = "Error 104: Kein Firmenname angegeben";
$error[14] = "Error 10 : Die CEO-ID ließ sich keinem Konto zuordnen";
$error[15] = "Error 11 : Kein Betrag spezifiziert!";
$error[16] = "Error 12 : Datenbankfehler beim Ausführen der Transaktion";
$error[17] = "Error 13 : Datenbankfehler beim Schreiben in den Transaktionslog";

function exists_id($id) {
  // Abstract: Takes an ID and looks it up in the tables.
  // Returns:  1 on success, 0 on failure
  // Depends:  Variables: $conn; Functions: get_category
  
  global $conn, $error;
  
  $cat = get_category($id);
  if ($cat == 1 || $cat == 2) {return 0;}

  $query = sprintf("SELECT 1 FROM " . $cat . " WHERE `id`='" . $id . "';");
  $result = $conn->query($query);
  if ($result->num_rows >= 1) {return 1;} 
  else                        {echo $error[0]; return 0;}
}

function get_category($id) {
  // Abstract: Takes an ID and returns the category as defined in $categories and $cat_offset.
  // Returns:  1 if ID is out of range and 2 if not an integer. Category String if succesful
  // Depends:  Variables: $id_digits, $categories, $cat_id_offset, $cat_id_range; Functions: NONE
  
  global $id_digits, $categories, $cat_id_offset, $cat_id_range, $error;
  
  //Checks the Value and assigns the category if passed
  if (is_int($id)) {
    if     ($id >= $cat_id_offset[0] && $id < $cat_id_offset[0]+$cat_id_range ) {$cat = $categories[0];}
    elseif ($id >= $cat_id_offset[1] && $id < $cat_id_offset[1]+$cat_id_range ) {$cat = $categories[1];} 
    elseif ($id >= $cat_id_offset[2] && $id < $cat_id_offset[2]+$cat_id_range ) {$cat = $categories[2];}
    else                                                                        {echo $error[1]; return 1;}
  } 
  else                                                                          {echo $error[2]; return 2;}
  
  return $cat;
}

function account_sum($id) {
  //Abstract: Sums up the account figure of the user or company with the specified ID and puts it into the database
  //Return  : 1 on success, 0 on failure
  //Depends : exists_id(), $conn
  
  global $conn, $error;
  
  if (!exists_id($id) || exists_id($id) === 0) {return 0;}
  
  $cat = get_category($id);
  if ($cat === 1 || $cat === 2) {return 0;}
  
  $query_ded = "SELECT account FROM transactions WHERE f_id=" . $id . ";";
  $query_add = "SELECT account FROM transactions WHERE t_id=" . $id . ";";
  
  $result_ded = $conn->query($query_ded);
  $account_ded = 0;
  if ($result_ded->error()) {return 0;}
  
  while ($row = $result_ded->fetch_assoc()) {
    $account_ded = $account_ded + $row["account"];
  }
  
  $result_add = $conn->query($query_add);
  $account_add = 0;
  if ($result_add->error()) {return 0;}
  
  while ($row = $result_add->fetch_assoc()) {
    $account_add = $account_add + $row["account"];
  }
  
  $account = $account_add - $account_ded;
  
  $query  = "UPDATE INTO " . $cat . " SET account=" . $account . " WHERE id=" . $id . ";";
  $result = $conn->query($query);
  
  if ($result->error()) {return 0;}
  else                  {return 1;}
}

function get_details($id, $first_name, $last_name, $display_name, $cat) {
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
  $criteria_raw = array("id" => $id, "f_name" => $first_name, "l_name" => $last_name, "display_name" => $display_name);
  $criteria = array();
  
  // Strips down the raw array to only the ones NOT NULL or only spaces
  foreach ($criteria_raw as $title => $crit) {
    if ($crit !== NULL && $crit !== "" && $crit !== 0) {
      $criteria[$title] = $crit;
    }
  }
  
  if (!count($criteria)) {
    $query = "SELECT id,display_name,c_id,(SELECT display_name FROM companies WHERE id=c_id) AS c_name,account FROM " . $cat . " ORDER BY id ASC;";
  } else {
    $query = "SELECT id,display_name,c_id,(SELECT display_name FROM companies WHERE id=c_id) AS c_name,account FROM " . $cat . "  WHERE ";
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
      $out_array[$i] = array("id" => $row["id"], "display_name" => $row["display_name"], "c_id" => $row["c_id"], "c_name" => $row["c_name"], "account" => $row["account"]);
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
  
  // Put the name criteria in array; initialise final criteria array
  $criteria_raw = array("id" => $id, "display_name" => $display_name);
  $criteria = array();
  
  // Strips down the raw array to only the ones NOT NULL or anything of the like
  foreach ($criteria_raw as $title => $crit) {
    if ($crit !== NULL && $crit !== "" && $crit !== 0) {
      $criteria[$title] = $crit;
    }
  }
  
  if (count($criteria) == 0) {
    $query = "SELECT id,display_name,ceo_id,(SELECT display_name FROM users WHERE id=ceo_id) AS ceo_name,account FROM companies ORDER BY id ASC;";
  } else {
    $query = "SELECT id,display_name,ceo_id,(SELECT display_name FROM users WHERE id=ceo_id) AS ceo_name,account FROM companies WHERE ";
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
      $out_array[$i] = array("id" => $row["id"], "display_name" => $row["display_name"], "ceo_id" => $row["ceo_id"], "ceo_name" => $row["ceo_name"], "account" => $row["account"]);
      $i++;
    }
  } else {
    echo $error[9];
  }
  return $out_array;
}

function generate_reciept($id, $limit) {
  // Abstract: Delivers all rows of the transaction log concerning the user with the specified id. $limit is optional and defines the amount of rows to print
  // Returns: 2D Array
  // Depends: $conn, exists_id, get_category
  
  global $conn, $error;
  
  switch (exists_id($id)) {
    case 0: return "Error 8: Der ID " . $from_id . " ließ sich kein Konto zuordnen"; break;
    case 10:
      switch (get_category($from_id)) {
	case 1: return "Error 9: Die ID " . $from_id . " ist keinem Bereich zuzuordnen!"; break;
	case 2: return "Error 10: Die ID " . $from_id . " ist keine Ganzzahl!"; break;
      }
      break;
  }
  
  $query = "SELECT id,f_id,t_id,amount,date,description FROM transactions WHERE f_id=" . $id ." OR t_id=" . $id ." ORDER BY id DESC";
  
  if ((int)$limit > 0) {
    $query .= " LIMIT " . (int)$limit;
  } 
  
  $query .= ";";
  $result = $conn->query($query);
  
  // Declare the out_array
  $out_array = array();
  
  // If rows are availible, then iterate over the rows and put them in as seperate subarrays aka 2D array
  if ($result->num_rows >= 1) {
    $i = 0;
    while ($row = $result->fetch_assoc()) {
      $f_cat = get_category((int)$row["f_id"]);
      $t_cat = get_category((int)$row["t_id"]);
      
      $query_f_name = "SELECT display_name FROM " . $f_cat . " WHERE id=" . $row["f_id"] . ";";
      $query_t_name = "SELECT display_name FROM " . $t_cat . " WHERE id=" . $row["t_id"] . ";";
      
      $result_f_name = $conn->query($query_f_name);
      $result_t_name = $conn->query($query_t_name);
      
      $f_name_arr = $result_f_name->fetch_assoc();
      $t_name_arr = $result_t_name->fetch_assoc();
      
      $f_name = $f_name_arr['display_name'];
      $t_name = $t_name_arr['display_name'];
      
      $out_array[$i] = array("id" => $row["id"], "f_id" => $row["f_id"], "f_name" => $f_name, "t_id" => $row["t_id"], "t_name" => $t_name, "amount" => $row["amount"], "date" => $row["date"], "description" => $row["description"]);
      $i++;
    }
  } else {
    echo $error[9];
  }
  return $out_array;
}

function add_user($id, $f_name, $m_name, $l_name, $c_id) {
  // Abstract: 
  // Return  : 1 on success, 0 on failure
  // Depends : $conn
  
  global $conn, $error;
  
  $args_raw = array("id" => (int)$id, "f_name" => $f_name, "m_name" => $m_name, "l_name" => $l_name, "c_id" => $c_id);
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
  
  if ($args['id'] === NULL) {
    if ($args['f_name'] === NULL || $args['l_name'] === NULL) {
      echo $error[10]; exit();
    }
    
    $query = "INSERT INTO users (`f_name`, `l_name`, `m_name`, `c_id`, `display_name`) VALUES ('" . $args['f_name'] . "', '" . $args['l_name'] . "', ";
    if ($args['m_name']) {
      $query .= "'" . $args['m_name'] . "', ";
      $display_name = $args['f_name'] . " " . $args['m_name'] . " " . $args['l_name'];
    } else {
      $query .= "NULL, ";
      $display_name = $args['f_name'] . " " . $args['l_name'];
    }
    if ($args['c_id']) {
      $query .= (int)$c_id . ", ";
    } else {
      $query .= "NULL, ";
    }
    $query .= "'" . $display_name . "');";
    $new = TRUE;
  } else {
    if ($args['f_name'] === NULL || $args['l_name'] === NULL) {
      echo $error[10]; exit();
    }
    if (!exists_id($args['id'])) {
      echo $error[11]; exit();
    }
    
    $query = "REPLACE INTO users (`id`, `f_name`, `l_name`, `m_name`, `c_id`, `display_name`) VALUES (" . (int)$args['id'] . ", '" . $args['f_name'] . "', '" . $args['l_name'] . "', ";
    if ($args['m_name']) {
      $query .= "'" . $args['m_name'] . "', ";
      $display_name = $args['f_name'] . " " . $args['m_name'] . " " . $args['l_name'];
    } else {
      $query .= "NULL, ";
      $display_name = $args['f_name'] . " " . $args['l_name'];
    }
    if ($args['c_id']) {
      $query .= (int)$c_id . ", ";
    } else {
      $query .= "NULL, ";
    }
    $query .= "'" . $display_name . "');";
  }
  
  $result = $conn->query($query);
  
  if (!$result) {
    echo $error[12]; exit();
  } else {
      $new_details = get_details(NULL, NULL, NULL, $display_name, "users");
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
      if (!exists_id($args['ceo_id'])) {
         echo $error[14]; exit();
      } 
  }
  
  if ($args['id'] === NULL) {
    $query = "INSERT INTO companies (`display_name`, `ceo_id`) VALUES ('" . $args['display_name'] . "', '" . $args['ceo_id'] . "');";
    $new === TRUE;
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
    foreach ($new_details as $row) {
    foreach ($row as $value) {
      $output .= $value . " ";
    }
    $output .= "<br>";
  }
    return "Erfolgreich Eingetragen!: " . $output;
  }
}

function del_user($id) {

}

function del_company($id) {

}

function transaction($from_id, $to_id, $amount, $description) {
  // Abstract: Manages a transaction by adding and deduction the amount under the consideration of taxes. Writes to the transaction log.
  // Returns: 1 if succesful, 0 on failure
  // Depends: Variables/Objects: $conn; Functions: get_category, exists_id
  
  global $conn, $error, $screws;
  
  $args_raw = array("from_id" => (int)$from_id, "to_id" => (int)$to_id, "amount" => $amount, "description" => $description);
  $args = array();
  
  if (!exists_id($args['from_id'])) {
    echo $error[5]; exit();
  } 
  if (!exists_id($args['to_id'])) {
    echo $error[7]; exit();
  } 
  
  foreach($args_raw as $index => $arg) {
    if ($arg === NULL || $arg === "" || $arg === " " || $arg === 0) {
      $args[$index] = NULL;
    } else {
      $args[$index] = $arg;
    }
  }
  
  if (!exists_id($args['from_id'])) {
    echo $error[3]; exit();
  } 
  if (!exists_id($args['to_id'])) {
    echo $error[6]; exit();
  } 
  
  $from_cat = get_category($from_id);
  $to_cat   = get_category($to_id);
  
  // Rundung, bzw. Expansion auf 3 Nachkommastellen
  $pamount = number_format((float)$args['amount'], 3, '.', ''); // Das hier versagt bei 1.00045 and  the like: gibt 1.000 aus, was falsch gerundet ist. Wird vernachlässigt...
  
  // appliance of vat taxes
  if($from_cat == "companies" && $to_cat == "companies") {
    $transamount = $pamount;
  } elseif ($from_cat != "companies" && $to_cat != "companies") {
    $transamount = $pamount;
  } else {
    $transamount = $pamount * (1/(1+$screws['taxes']['vat']));
  }
  $taxamount = $pamount - $transamount;
  
  if ($pamount == 0) {
      echo $error[15]; exit();
  }
  
  // Deduction the amount from senders account
  $query_ded  = sprintf("UPDATE " . $from_cat . " SET account=account-" . $pamount . " WHERE id=" . $from_id . ";");
  $result_ded = $conn->query($query_ded);
  
  // Adding the amount to recipients account
  $query_add  = sprintf("UPDATE " . $to_cat . " SET account=account+" . $transamount . " WHERE id=" . $to_id . ";");
  $result_add = $conn->query($query_add);

  // VAT Taxes
  if ($taxamount != 0) {
    $query_tax  = sprintf("UPDATE vips SET account=account+" . $taxamount . " WHERE id=" . $tax_id . ";");
    $result_tax = $conn->query($query_tax);
  }
  
  if (!$result_add || !$result_ded || !$result_tax) {
    echo $error[16]; exit();
  } else {
    // Writing to transaction log
    $query_log      = sprintf("INSERT INTO transactions (`f_id`, `t_id`, `amount`, `tax_amount`, `date`, `description`) VALUES (" . $from_id . ", " . $to_id . ", " . $transamount . "', " . $taxamount . ", NOW(), '" . $description . "');");
    $result_log = $conn->query($query_log);
  }
  if (!$result_log) {
    echo $error[17]; exit();
  } else {
    return 0;
  }
}

function pay_in_out($mode, $id) {

}


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

?>
