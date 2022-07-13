<?php
require('../mysqli_connect.php');
include('includes/utils.php');

function executeCommand($dbc, $stmt) {
	mysqli_stmt_execute($stmt);
	
	if (mysqli_stmt_affected_rows($stmt) == 1) {
		
	} else {
		// Debugging message
		// $error = mysqli_stmt_error($stmt);
   	// print("--------------------------------------<br/>");
   	// print("Error: ".$error);
		// print("<br/>--------------------------------------<br/>");
		throw new Exception("System Error");
	}
}

$hasError = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// POST - place order 

	// validate form value 
	$valid = true;

	if (empty($_POST['CustFirstName'])) {
		$custFirstNameError = 'First Name should be filled';
		$valid = false;

	} else {
		$custFirstName = strip_tags($_POST['CustFirstName']);
	}

	if (empty($_POST['CustLastName'])) {
		$custLastNameError = 'Last Name should be filled';
		$valid = false;

	} else {
		$custLastName = strip_tags($_POST['CustLastName']);
	}

	$number_pattern = "/^\\d+$/";
	if (empty($_POST['CardNumber'])) {
		$cardNumberError = 'Card number should be filled';
		$valid = false;

	} else if (preg_match($number_pattern, $_POST['CardNumber']) == 0){
		$cardNumberError = 'Invalid card number';
		$valid = false;

	} else if (strlen($_POST['CardNumber']) != 16){
		$cardNumberError = 'Invalid card number';
		$valid = false;

	} else {
		$cardNumber = strip_tags($_POST['CardNumber']);
	}

	if (empty($_POST['CardExpiresIn'])) {
		$cardExpiresInError = 'Card expire date should be filled';
		$valid = false;

	} else {
		$cardExpiresIn = strip_tags($_POST['CardExpiresIn']);
	}


	if ($valid){

		try {
			$bookIds = $_POST['bookID'];
			
			// autocommit false for transaction and rollback
			mysqli_autocommit($dbc, FALSE);


			// Register Customer
			$qCustomer = 'INSERT INTO Customers (CustFirstName, CustLastName) VALUES (?, ?)';
			$stmt = mysqli_prepare($dbc, $qCustomer);
			mysqli_stmt_bind_param($stmt, 'ss', $custFirstName, $custLastName);
			executeCommand($dbc, $stmt);
			$customerID = mysqli_insert_id($dbc);

			// Insert Order
			$qOrder = 'INSERT INTO BookInventoryOrders (CustomerID, OrderDate, PaymentOptionID, CardNumber, CardExpiresIn) VALUES (?, NOW(), ?, ?, ?)';
			$stmt = mysqli_prepare($dbc, $qOrder);
			mysqli_stmt_bind_param($stmt, 'iiss', $customerID, $paymentOptionID, $cardNumber, $cardExpiresIn);

			$paymentOptionID = (int) $_POST['PaymentOptionID'];

			executeCommand($dbc, $stmt);
			$orderID = mysqli_insert_id($dbc);

			$quantity = 1;  // Always 1 currently.

			foreach ($bookIds as $bookID) {

				$quantity = 1;
				// Check if we have books
				$qBook = "SELECT * FROM BookInventories WHERE BookID = '$bookID' LIMIT 1;";
				$rBook = @mysqli_query ($dbc, $qBook); 
				$price = 0;
				while ($rowBook = mysqli_fetch_array($rBook, MYSQLI_ASSOC)) {
					$price = $rowBook['RetailPrice'];
					if (($rowBook['Quantity'] - $quantity) < 0){
						throw new Exception("The book is out of stock!");
					}
				}

				// SELECT `OrderID`, `BookID`, `QuotedPrice`, `QuantityOrdered` FROM `BookInventoryOrder_Details` WHERE 1
				// Insert Order Details 
				$qOrderDetail = 'INSERT INTO `BookInventoryOrder_Details`(`OrderID`, `BookID`, `QuotedPrice`, `QuantityOrdered`) VALUES (?,?,?,?);';
				$stmt = mysqli_prepare($dbc, $qOrderDetail);
				mysqli_stmt_bind_param($stmt, 'iiii', $orderID, $bookID, $price, $quantity);
				executeCommand($dbc, $stmt);

				// Update inventory quantity
				$qInventoryUpdate = "UPDATE BookInventories SET Quantity = (Quantity - 1) WHERE BookID = ?;";
				$stmt = mysqli_prepare($dbc, $qInventoryUpdate);
				mysqli_stmt_bind_param($stmt, 'i', $bookID);
				executeCommand($dbc, $stmt);

				// Check out of stock one more time 
				$qInventory = "SELECT * FROM BookInventories WHERE BookID = '$bookID' LIMIT 1;";
				$rInventory = @mysqli_query ($dbc, $qInventory); 
				while ($rowInventory = mysqli_fetch_array($rInventory, MYSQLI_ASSOC)) {
					if ($rowInventory['Quantity'] < 0){
						throw new Exception("The book is out of stock!");
					}
				}
			}


			// Order successfully placed
			// Commit Transaction
			mysqli_commit($dbc);
			
			session_start();
			// remove items from cart
			unset($_SESSION['cart']);
		
			// set customer id for order info page	
			$_SESSION['orderID'] = $orderID;
			$_SESSION['agent'] = sha1($_SERVER['HTTP_USER_AGENT']);	
		
			redirect_user("orderinfo.php");
			return;

		} catch(Exception $e) {
			mysqli_rollback($dbc);
			echo '<p style="font-weight: bold; color: #C00">Your Order could not be made due to a system error. We apologize for any inconvenience.</p>'; 
			echo '<p style="font-weight: bold; color: #C00">Error: ' . $e->getMessage() . '</p>'; 
			echo '<a href="index.php">Home</a>';
			$hasError = true;

		} finally {
			mysqli_stmt_close($stmt);
			mysqli_close($dbc);
		}


	} else {
		// validation failed. 
		$cur = basename(__FILE__, '.php');
		$page_title = 'Checkout - what name';


		include('header.html');

		session_start();

		if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
			// cart is empty.
			redirect_user("store.php");
			return;
		} 

		$cart = $_SESSION['cart'];

	}

} else {

	# GET - checkout page 
	$cur = basename(__FILE__, '.php');
	$page_title = 'Checkout - what name';


	include('includes/header.html');

	session_start();

	if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
		// cart is empty.
		redirect_user("store.php");
		return;
	} 

	$cart = $_SESSION['cart'];
}

if (!$hasError){

?>

<h1>Checkout</h1>
<form action="checkout.php" method="post">
<fieldset>
  <legend>Order Summary</legend>
  <div class="grid-2">
    <div>
			<div>
				Books
    	</div>
			<br/><br/>
			<div class="sm">
				<a href="empty_cart.php">Empty the cart</a>
    	</div>
    </div>
    <div>
<?php

	// Render books in cart 
	$bookIds = join("','",$cart);   
	$qBooks = "SELECT * FROM BookInventories WHERE BookID IN ('$bookIds')";
	$rBooks = @mysqli_query ($dbc, $qBooks); 

	$prices = array();

	echo '<div class="field"><ul class="">';
	while ($rowBook = mysqli_fetch_array($rBooks, MYSQLI_ASSOC)) {
		array_push($prices, $rowBook['RetailPrice']);
		echo '<li class="fl">
			<input type="hidden" name="bookID[]" value="' . $rowBook['BookID'] . '" />
			<div class="img-wrapper">
			<img src='. "images/{$rowBook['BookImgUrl']} " .' />
			</div>
			<div class="desc field">
				<h4>' . $rowBook['BookName'] . '</h4>
				<p>' . $rowBook['BookAuthor'] . '</p>
				<p><span class="price">$' . $rowBook['RetailPrice'] . '</span></p>
				<p><span class="sm">Only ' . $rowBook['Quantity'] . ' left in stock</span></p>
			</div>
		</li>';
	}

	// sum
	echo '<li class="fl sum">
			<div>
				Total
			</div>
			<div class="desc field">
				<p><span class="">$' . calculate_sum($prices) . '</span></p>
				</div>
		</li>';


	// close ul 
	echo '</ul></div>';
?>
    </div>
  </div>
	</fieldset>
	<fieldset>
	<legend>Payment</legend>
  <div class="grid-2">
    <div>
			First Name:
    </div>
    <div>
		<div class="field">
				<input type="text" name="CustFirstName" placeholder="Your Firstname" size="20" maxlength="60" value="<?php if (isset($_POST['CustFirstName'])) echo $_POST['CustFirstName']; ?>"/>
				<?php if (isset($custFirstNameError)) echo '<span class="error">' . $custFirstNameError . '</span>' ?>
			</div>
    </div>
  </div>
  <div class="grid-2">
    <div>
		Last Name:
    </div>
    <div class="field">
			<input type="text" name="CustLastName" placeholder="Your Lastname" size="20" maxlength="60" value="<?php if (isset($_POST['CustLastName'])) echo $_POST['CustLastName']; ?>"/>
			<?php if (isset($custLastNameError)) echo '<span class="error">' . $custLastNameError . '</span>' ?>
    </div>
  </div>
  <div class="grid-2">
    <div>
			Payment Detail
    </div>
    <div class="fl">
			<div class="field fl">
				<div class="fl1">
			
<?php

// render Payment Options from database
$qPaymentOption = "SELECT * FROM PaymentOptions;";
$rPaymentOptions = @mysqli_query ($dbc, $qPaymentOption); 

echo '<select name="PaymentOptionID">';
while ($rowPaymentOption = mysqli_fetch_array($rPaymentOptions, MYSQLI_ASSOC)) {
	echo '<option value="' . $rowPaymentOption['PaymentOptionID'] . '">' . $rowPaymentOption['PaymentOptionName'] . '</option>\n';
}
echo '</select>';
echo '<span class="">&nbsp;</span>';

?>

				</div>
				<div class="fl1">
					<input type="text" name="CardNumber" placeholder="Enter only numbers" size="20" maxlength="60" autocomplete="off" value="<?php if (isset($_POST['CardNumber'])) echo $_POST['CardNumber']; ?>"/>
					<?php 
						if (isset($cardNumberError)) {
							echo '<span class="error">' . $cardNumberError . '</span>';
						} else {
							echo '<span class="">&nbsp;</span>';
						} ?>
				</div>
				<div class="fl1">
					<input type="text" name="CardExpiresIn" placeholder="2023/11" size="4" maxlength="7" autocomplete="off" value="<?php if (isset($_POST['CardExpiresIn'])) echo $_POST['CardExpiresIn']; ?>"/>
					<?php 
						if (isset($cardExpiresInError)) {
							echo '<span class="error">' . $cardExpiresInError . '</span>';
						} else {
							echo '<span class="">&nbsp;</span>';
						} ?>

				</div>
			</div>
		</div>
	</div>

	<div class="grid-2">
		<div>
		</div>
		<div class="fl">
			<div class="fl1">
				<input type="submit" name="submit" value="Place Order" />
			</div>
			<div class="fl1">
				&nbsp;
			</div>
			<div class="fl1">
				&nbsp;	
			</div>
		</div>
	</div>		
	</fieldset>
</form>

<?php

	include('includes/footer.html');

} // End of hasError false

?>

