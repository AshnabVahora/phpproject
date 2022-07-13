<?php

include('includes/utils.php');

$bookID = $_GET["bookID"];
if (!isset($bookID)) {
	redirect_user("store.php");
	return;
}

session_start();

if (isset($_SESSION['cart'])) {
	// cart exsits in session

	if (!isset($_SESSION['agent']) || ($_SESSION['agent'] != sha1($_SERVER['HTTP_USER_AGENT']) )) {

		// Unsafe cart session data. use new one. 
		$cart = array(strip_tags($bookID));
	
	}else{
		
		$cart = $_SESSION['cart'];
		if (in_array($bookID, $cart))
		{
			// nothing. the book is already in cart.
		}
		else
		{
			array_push($cart, $bookID);
		}
	}
	

} else {
	// cart NOT exsits in session
	$cart = array(strip_tags($bookID));
}

$_SESSION['cart'] = $cart;
$_SESSION['agent'] = sha1($_SERVER['HTTP_USER_AGENT']);	

// move to checkout page.
redirect_user("checkout.php");
return;
?>
