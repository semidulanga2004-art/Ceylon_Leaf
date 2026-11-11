<?php
session_start();
session_unset(); //removes all variables stored in the current session
session_destroy(); // destroys current session on the server
header("Location: index.php");
exit();
?>