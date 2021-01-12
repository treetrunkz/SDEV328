
<!--Ellie Carl and Sarah Mehri-->
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<h1>
   Cupcake Fundraiser
</h1>
<hr>
<?php
$carray = array(
    "The Grasshopper"=>"The&nbsp;Grasshopper",
    "Whiskey"=>"Whiskey&nbsp;Maple&nbsp;Bacon",
    "Carrot"=> "Carrot&nbsp;Walnut",
    "Cupcake"=> "Salted&nbsp;Caramel&nbsp;Cupcake",
    "Velvet"=> "Red&nbsp;Velvet",
    "Lemon"=> "Lemon&nbsp;Drop",
    "Tiramisu"=> "Tiramisu"
);
echo "<form type='submit' action='order.php' method='post'>";
?>
<div class="form-group" style="padding-left: 5px;">
    <label for="fname">First Name</label>
    <input type="text" class="form-control" id="fname" name="fname" style="width: 20%"><br>
</div>
<div class="form-check">

<?php
//form beginning
foreach($carray as $x_value){


    echo "<input type='checkbox' name='checkbox[]' value=" . $x_value ." id=" . $x_value . ">";
    echo "<label for=" . $x_value . ">" . $x_value. "</label><br>";
}
?>
</div>

<div class="form-group" style="padding:10px 10px;">
<input type='submit' name='submit' value='Order'>
</div>
</form>


