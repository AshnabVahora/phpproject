<?php

$cur = basename(__FILE__, '.php');
$page_title = 'Order Information';
include('includes/utils.php');
include('includes/header.html');

?>

<h1>Your Order</h1>
<h4>Your order has been comfirmed!</h4>
<?php
	require('../mysqli_connect.php');

	session_start();
	if (!isset($_SESSION['orderID']) || !isset($_SESSION['agent']) || ($_SESSION['agent'] != sha1($_SERVER['HTTP_USER_AGENT']) )) {
		// Unsafe access to the page! 
		redirect_user("index.php");
		return;
	}

	$orderID = $_SESSION['orderID'];
	$qOrder = "SELECT o.OrderDate, p.PaymentOptionName, o.CardNumber, o.CardExpiresIn, sum(od.QuotedPrice * od.QuantityOrdered) AS sumPrice FROM BookInventoryOrders o INNER JOIN BookInventoryOrder_Details od ON o.OrderID = od.OrderID INNER JOIN PaymentOptions p ON p.PaymentOptionID = o.PaymentOptionID WHERE o.OrderID  = '$orderID';";
	$rOrder = @mysqli_query($dbc, $qOrder); 

	echo '<div class="order-info"><ul class="">';
	while ($rowOrder = mysqli_fetch_array($rOrder, MYSQLI_ASSOC)) {
		echo '<li class="">
			<div class="">
				<p class="pd">Payment Information: ' . $rowOrder['PaymentOptionName'] . ' ' . $rowOrder['CardNumber'] . ' (' . $rowOrder['CardExpiresIn'] . ')</p>
				<p class="pd"><span class="sum">Total: ' . $rowOrder['sumPrice'] . '</span></p>
			</div>
		</li>';
	}

	// close ul 
	echo '</ul></div>';
?>
    
	<br/>
	<div class="fl">
		<a href="index.php" class="btn btn-primary">Home</a>
	</div>
			

<?php

include('includes/footer.html');
?>

