<?php

    echo '
        <h2>Warenstruktur</h2>
        <center>
        <table class="structure_table">
            <tr>
                <td><h3>Produkte</h3></td>
                <td><h3>Halbprodukte</h3></td>
                <td><h3>Rohteile</h3></td>
                <td><h3>Hilfsprodukte</h3></td>
            </tr>
    ';

    $prefix_product =  GetProperty("prefix_product");
    $prefix_semiproduct =  GetProperty("prefix_semiproduct");
    $prefix_raw =  GetProperty("prefix_raw");
    $prefix_support =  GetProperty("prefix_support");

    $strSQL = "SELECT * FROM products WHERE number LIKE '$prefix_product%'";
    $rs=mysqli_query($link,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        $parent = $row['number'];
        echo '<tr>';
        echo '<td rowspan="'.ChildCount($row['number']).'" class="structure_product"><a href="/products?products&show#'.$row['number'].'"><b>'.$row['number'].'</b></a> - '.fetch("product_contains","quantity","parent",$row['number']).'x - '.$row['name'].'</td>';

        $strSQLs = "SELECT * FROM product_contains WHERE parent = '$parent' AND (child LIKE '$prefix_semiproduct%' OR child LIKE '$prefix_raw%' OR child LIKE '$prefix_support%')";
        $rss=mysqli_query($link,$strSQLs);
        while($rows=mysqli_fetch_assoc($rss))
        {
            if(SubStringFind($rows['child'],$prefix_semiproduct))
            {
                echo '<td rowspan="'.(ChildCount($rows['child'])).'" class="structure_semiproduct"><a href="/products?semiproducts&show#'.$rows['child'].'"><b>'.$rows['child'].'</b></a> - '.$rows['quantity'].'x - '.fetch("products","name","number",$rows['child']).'</td>';

                $subparent = $rows['child'];
                $first = 1;
                $strSQLr = "SELECT * FROM product_contains WHERE parent = '$subparent' AND (child LIKE '$prefix_raw%' OR child LIKE '$prefix_support%')";
                $rsr=mysqli_query($link,$strSQLr);
                while($rowr=mysqli_fetch_assoc($rsr))
                {
                    if($first != 1) echo '<tr>';
                    $first = 0;
                    if(SubStringFind($rowr['child'],$prefix_raw)) echo '<td class="structure_raw"><a href="/products?raw&show#'.$rowr['child'].'"><b>'.$rowr['child'].'</b></a> - '.$rowr['quantity'].'x '.RawPrice($rowr['child']).'&euro;- '.fetch("products","name","number",$rowr['child']).'</td><td class="structure_support"></td></tr>';
                    if(SubStringFind($rowr['child'],$prefix_support)) echo '<td colspan=2 class="structure_support"><a href="/products?support&show#'.$rowr['child'].'"><b>'.$rowr['child'].'</b></a> - '.$rowr['quantity'].'x - '.fetch("products","name","number",$rowr['child']).'</td></tr>';
                }
            }
            if(SubStringFind($rows['child'],$prefix_raw))
            {
                echo '<td class="structure_raw" colspan=2><a href="/products?raw&show#'.$rows['child'].'"><b>'.$rows['child'].'</b></a> - '.$rows['quantity'].'x '.RawPrice($rows['child']).'&euro;- '.fetch("products","name","number",$rows['child']).'</td><td class="structure_support"></td></tr>';
            }
            if(SubStringFind($rows['child'],$prefix_support))
            {
                echo '<td class="structure_support" colspan=3><a href="/products?support&show#'.$rows['child'].'"><b>'.$rows['child'].'</b></a> - '.$rows['quantity'].'x - '.fetch("products","name","number",$rows['child']).'</td></tr>';
            }
        }
    }

    echo '</table></center>'
?>