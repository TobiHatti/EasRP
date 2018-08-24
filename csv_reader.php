<?php
include("data/mysql_connect.php");
$row = 1;
if (($handle = fopen("Land.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        $num = count($data);
        $row++;
        for ($c=0; $c < $num; $c+=3)
        {


            $country = $data[$c];
            $alpha2 = $data[$c+1];
            $alpha3= $data[$c+2];

            $strSQL = "INSERT INTO country_list (name,alpha2,alpha3) VALUES ('$country','$alpha2','$alpha3')";
            //$rs=mysqli_query($link,$strSQL);
        }
    }
    fclose($handle);
}
?>