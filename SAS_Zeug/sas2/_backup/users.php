<!DOCTYPE html>

<meta charset="UTF-8"> 

<html>
<body>

<?php

include 'library.php';

if ($_GET['add_user'] !== NULL) {

  echo add_user($_GET['id'], $_GET['f_name'], $_GET['m_name'], $_GET['l_name'], $_GET['c_id']);
  
} elseif ($_GET['add_company'] !== NULL) {

  echo add_company($_GET['id'], $_GET['display_name'], $_GET['ceo_id']);
  
}

?>

</body>
</html> 
