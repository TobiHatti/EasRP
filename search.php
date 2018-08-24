<?php
    include("header.php");
    
    if(isset($_POST['search_query']))
    {
        if($_POST['search_query']!="")
        {
            // Checking if a result exists
            $q = $_POST['search_query'];
            $checkProducts = (SubStringMultiFind($_POST['search_query'],GetProperty("prefix_support"),GetProperty("prefix_raw"),GetProperty("prefix_semiproduct"),GetProperty("prefix_product")) AND MySQLResultCount("SELECT * FROM products WHERE number = '$q'")!=0) ? 1 : 0;
            $checkCustomers = (SubStringFind($_POST['search_query'],GetProperty("prefix_customer") AND MySQLResultCount("SELECT * FROM customers WHERE customer_number = '$q'")!=0)) ? 1 : 0;
            $checkOrders = (SubStringFind($_POST['search_query'],GetProperty("prefix_order") AND MySQLResultCount("SELECT * FROM orders WHERE order_number = '$q'")!=0)) ? 1 : 0;

            if($checkProducts OR $checkCustomers OR $checkOrders) NotificationBanner("Suchergebnisse gefunden","check");
            else NotificationBanner("Keine Suchergebnisse gefunden","cross");

            Redirect('search?s='.strtoupper($_POST['search_query']));
        }
        else
        {
            $addCode='<button class="button_m t_button" onclick="window.history.back()">Zurück</button>';
            echo FullScreenError("l","Kein Suchwert","Bitte geben Sie einen Suchwert an.",$addCode);
        }
    }

    echo '<br><br>';

    if(isset($_GET['s']) AND $_GET['s']!="")
    {
        if(SubStringFind($_GET['s'],GetProperty("prefix_customer")))
        {
            echo '<h1>Kunde <b>'.fetch("customers","last_name","customer_number",$_GET['s']).' '.fetch("customers","first_name","customer_number",$_GET['s']).'</b> ('.$_GET['s'].')</h1>';
            require("search_customerdata.php");
        }
        else if(SubStringMultiFind($_GET['s'],GetProperty("prefix_support"),GetProperty("prefix_raw"),GetProperty("prefix_semiproduct"),GetProperty("prefix_product")))
        {
            require("search_productdata.php");
        }
        else
        {
            $addCode='<button class="button_m t_button" onclick="window.history.back()">Zurück</button>';
            echo FullScreenError("l","Suchwert nicht gefunden",'Es wurden keine Ergebnisse für den Suchwert <b>"'.$_GET['s'].'"</b> gefunden',$addCode);
        }
    }
    else
    {
        if(!isset($_POST['search_query']))
        {
            $addCode='<button class="button_m t_button" onclick="window.history.back()">Zurück</button>';
            echo FullScreenError("l","Kein Suchwert","Bitte geben Sie einen Suchwert an.",$addCode);
        }
    }

    include("footer.php");
?>