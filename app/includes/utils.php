<?php 

function redirect_user($page = 'index.php') {
	$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
	$url = rtrim($url, '/\\');
	$url .= '/' . $page;

	header("Location: $url");
	exit();
} 

function calculate_sum($prices) {
  $sum = 0;
  foreach ($prices as $price) { 
    $sum += $price;
  }
	return $sum;
} 


?>