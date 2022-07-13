<?php

$cur = basename(__FILE__, '.php');
$page_title = 'BookStore - Find the book you would like to read!';
include('includes/header.html');

?>
<h1>BookStore - Books</h1>
<p>Find the book you would like to read!</p>

<?php

require('../mysqli_connect.php');

function link_start($rowBook) {
	if ($rowBook['Quantity'] > 0){
		return '<a href="cart.php?bookID=' . $rowBook['BookID'] . '">';
	}else {
		return '<div class="disabled-link">';
	}
}
function link_end($rowBook) {
	if ($rowBook['Quantity'] > 0){
		return '</a>';
	}else {
		return '</div>';
	}
}
function quantity($rowBook) {
	if ($rowBook['Quantity'] > 0){
		return '<p><span class="sm">Only ' . $rowBook['Quantity'] . ' left in stock</span></p>';
	}else {
		return '<p><span class="sm">Not Available (out of stock)</span></p>';
	}
}
function price($rowBook) {
	if ($rowBook['Quantity'] > 0){
		return '<p><span class="price">$' . $rowBook['RetailPrice'] . '</span></p>';
	}else {
		return '<p><span class="">$' . $rowBook['RetailPrice'] . '</span></p>';
	}
}

// Fetch Categories....
$qCate = "SELECT * FROM Categories;";
$rCate = @mysqli_query ($dbc, $qCate); 

while ($rowCate = mysqli_fetch_array($rCate, MYSQLI_ASSOC)) {
	echo '<div class="items">
	<h3>' . $rowCate['CategoryDescription'] . '</h3>
	<ul class="fl">';

	$categoryID = $rowCate['CategoryID'];

	$qBook = "SELECT * FROM BookInventories WHERE CategoryID = '$categoryID';";
	$rBook = @mysqli_query ($dbc, $qBook); 
	
	while ($rowBook = mysqli_fetch_array($rBook, MYSQLI_ASSOC)) {
		echo '<li>
			'. link_start($rowBook) .'
				<div class="img-wrapper">
				<img src='. "images/{$rowBook['BookImgUrl']} " .' />
				</div>
				<div class="desc">
					<h4>' . $rowBook['BookName'] . '</h4>
					<p>' . $rowBook['BookAuthor'] . '</p>
					'. price($rowBook) .'
					'. quantity($rowBook) .'
				</div>
				'. link_end($rowBook) .'
		</li>';
	}

	// close ul 
	echo '</ul>
	</div>';

}

include('includes/footer.html');
?>
