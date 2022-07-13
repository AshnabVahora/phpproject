<?php

$cur = basename(__FILE__, '.php');
$page_title = 'BookStore - Find the book you would like to read!';
include('includes/header.html');

?>
<h1>BookStore</h1>
<h3>Welcome to our Book Store!</h3>
<p>We are dedicated to helping people find great books at affordable prices!</p>
<p>Find the book you would like to read!</p>

<?php

require('../mysqli_connect.php');


// Popular Books
echo '<div class="items">
<h3>Popular Books</h3>
<p class="index-msg">Meet our best seller books!</p>
<ul class="fl">';


$qBook = "SELECT * FROM BookInventories WHERE BookID IN ( SELECT od.BookID FROM BookInventoryOrder_Details od  INNER JOIN BookInventories b ON od.BookID = b.BookID WHERE b.Quantity > 0 GROUP BY od.BookID ORDER BY sum(od.QuantityOrdered) DESC ) LIMIT 4;"; 
$rBook = @mysqli_query ($dbc, $qBook); 
while ($rowBook = mysqli_fetch_array($rBook, MYSQLI_ASSOC)) {
  echo '<li>
    <a href="cart.php?bookID=' . $rowBook['BookID'] . '">
      <div class="img-wrapper">
      <img src='. "images/{$rowBook['BookImgUrl']} " .' />
      </div>
      <div class="desc">
        <h4>' . $rowBook['BookName'] . '</h4>
        <p>' . $rowBook['BookAuthor'] . '</p>
        <p><span class="price">$' . $rowBook['RetailPrice'] . '</span></p>
        <p><span class="sm">Only ' . $rowBook['Quantity'] . ' left in stock</span></p>
      </div>
    </a>
  </li>';
}
echo '</ul>
</div>';


// Popular Books
echo '<div class="items">
<h3>Last chance</h3>
<p class="index-msg">Almost sold out!</p>
<ul class="fl">';

$qBook = "SELECT * FROM BookInventories WHERE Quantity > 0 ORDER BY Quantity ASC LIMIT 4;";
$rBook = @mysqli_query ($dbc, $qBook); 
while ($rowBook = mysqli_fetch_array($rBook, MYSQLI_ASSOC)) {
  echo '<li>
    <a href="cart.php?bookID=' . $rowBook['BookID'] . '">
      <div class="img-wrapper">
        <img src='. "images/{$rowBook['BookImgUrl']} " .' />
      </div>
      <div class="desc">
        <h4>' . $rowBook['BookName'] . '</h4>
        <p>' . $rowBook['BookAuthor'] . '</p>
        <p><span class="price">$' . $rowBook['RetailPrice'] . '</span></p>
        <p><span class="sm">Only ' . $rowBook['Quantity'] . ' left in stock</span></p>
      </div>
    </a>
  </li>';
}
echo '</ul>
</div>';


include('includes/footer.html');
?>

