<?php

include('includes/utils.php');

session_start();
unset($_SESSION['cart']);

redirect_user("store.php");
return;


?>