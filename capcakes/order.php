

<?php

//flag
$isValid = true;

//check first name
if(!empty($_POST['fname'])){
    $fname = $_POST['fname'];
    echo "<h1>Thank you, $fname, for your order!</h1><br>";
}
else{
    echo"<p> Invalid first name</p>";
    $isValid = false;
}

echo "<h1> Order Summary: </h1>";
//add total of cupcakes for price.
$total = 0;


foreach(($_POST['checkbox'])as $name){
    $total += 3.50;
    echo "<li>" .$name." </li><br>";
};
echo "<br>";
echo "<h3>Order total: " . $total . " Dollars</h3>";
