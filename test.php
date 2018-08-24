<?php
    include("header.php");

   $strSQL = "SELECT * FROM products";
   $rs=mysqli_query($link,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        echo BarCodeImg($row['number'],true).'<br>';
    }




    include("footer.php");
?>