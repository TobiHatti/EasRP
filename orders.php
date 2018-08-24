<?php
    include("header.php");

// ADD ===================================================================================
    if(isset($_POST['check_customer']))
    {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $eMail = $_POST['eMail'];
        $customerNumber = GenerateCustomerNumber();

        if(MySQLResultExists("SELECT * FROM customers WHERE email = '$eMail' AND first_name = '$firstName' AND last_name = '$lastName'"))
        {
            if(MySQLResultCount("SELECT * FROM customers WHERE email = '$eMail' AND first_name = '$firstName' AND last_name = '$lastName'")>1)
            {
                Redirect('orders?add&selectCustomer&fn='.$firstName.'&ln='.$lastName.'&em='.$eMail);
            }
            else Redirect('orders?add&existingCustomer='.MySQLSkalar("SELECT customer_number AS x FROM customers WHERE email = '$eMail' AND first_name = '$firstName' AND last_name = '$lastName'"));
        }
        else
        {
            MySQLNonQuery("INSERT INTO customers (customer_number,first_name,last_name,email) VALUES ('$customerNumber','$firstName','$lastName','$eMail');");
            Redirect("orders?add&newCustomer=$customerNumber");
        }
    }

    if(isset($_POST['selectCustomer']))
    {
        $customerNumber = $_POST['selectCustomer'];
        Redirect("orders?add&existingCustomer=$customerNumber");
    }

    if(isset($_POST['save_customer']))
    {
        // Fetching POST Data
        $customerNumber = $_POST['customer_number'];

        $salutation = $_POST['salutation'];
        $title = $_POST['title'];
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $eMail = $_POST['eMail'];
        $adressLine1 = $_POST['adressLine1'];
        $adressLine2 = $_POST['adressLine2'];
        $city = $_POST['city'];
        $plzZip = $_POST['plzZip'];
        $country = $_POST['country'];

        // Tests if it still needs to be checked if user already has an account
        $allowUpdate = (isset($_POST['backgroundCheck']) AND MySQLResultExists("SELECT * FROM customers WHERE email = '$eMail' AND first_name = '$firstName' AND last_name = '$lastName'")) ? 0 : 1 ;

        if($allowUpdate)
        {
            $customerExists = (fetch_count('customers','customer_number',$customerNumber)!=0) ? 1 : 0;
            $customerAdd = (!$customerExists) ? "INSERT INTO customers (customer_number) VALUES ('$customerNumber');" : "" ;

            // Update Tables
            $sqlUpdate = "
            UPDATE customers SET
            salutation = '$salutation',
            title = '$title',
            first_name = '$firstName',
            last_name = '$lastName',
            email = '$eMail',
            adressline1 = '$adressLine1',
            adressline2 = '$adressLine2',
            city = '$city',
            zip = '$plzZip',
            country = '$country'
            WHERE customer_number = '$customerNumber';";

            // Transaction not possible? Didn't work, was tired of trying...
            (!$customerExists) ? MySQLNonQuery($customerAdd) : '';
            MySQLNonQuery($sqlUpdate);

            NotificationBanner("Neuer Nutzer wurde angelegt.","info");
            Redirect("orders?add&existingCustomer=$customerNumber");
        }
        else
        {
            // Add duplicate-Customer and execute selector-question
            $existingCN = MySQLSkalar("SELECT customer_number AS x FROM customers WHERE email = '$eMail' AND first_name = '$firstName' AND last_name = '$lastName'");
            $newCN = $customerNumber.'-DUPL';

            $customerExists = (fetch_count('customers','customer_number',$newCN)!=0) ? 1 : 0;
            $customerAdd = (!$customerExists) ? "INSERT INTO customers (customer_number) VALUES ('$newCN');" : "" ;

            $sqlUpdate = "
            UPDATE customers SET
            salutation = '$salutation',
            title = '$title',
            first_name = '$firstName',
            last_name = '$lastName',
            email = '$eMail',
            adressline1 = '$adressLine1',
            adressline2 = '$adressLine2',
            city = '$city',
            zip = '$plzZip',
            country = '$country'
            WHERE customer_number = '$newCN';";

            // Transaction not possible? Didn't work, was tired of trying...
            MySQLNonQuery($customerAdd);
            MySQLNonQuery($sqlUpdate);

            Redirect("orders?add&duplicateCustomer&exCN=$existingCN&newCN=$newCN");
        }

    }
    if(isset($_POST['duplOverwrite']))
    {
        $exCN = $_POST['exCN'];
        $newCN = $_POST['newCN'];

        $strSQL = "DELETE FROM customers WHERE customer_number = '$exCN';";
        MySQLNonQuery($strSQL);
        $strSQL = "UPDATE customers SET customer_number = '$exCN' WHERE customer_number = '$newCN';";
        MySQLNonQuery($strSQL);

        NotificationBanner("Alte Kundendaten wurden &uuml;berschrieben.","info");
        Redirect("orders?add&existingCustomer=$exCN");
    }
    if(isset($_POST['duplKeepEntry']))
    {
        $exCN = $_POST['exCN'];
        $newCN = $_POST['newCN'];

        $strSQL = "DELETE FROM customers WHERE customer_number = '$newCN'";
        MySQLNonQuery($strSQL);
        NotificationBanner("Alte Kundendaten wurden beibehanten.","info");
        Redirect("orders?add&existingCustomer=$exCN");
    }
    if(isset($_POST['duplKeepBoth']))
    {
        $exCN = $_POST['exCN'];
        $newCN = $_POST['newCN'];
        $newCNcorr = str_replace('-DUPL','',$_POST['newCN']);
        $strSQL = "UPDATE customers SET customer_number = '$newCNcorr' WHERE customer_number = '$newCN'";
        MySQLNonQuery($strSQL);
        NotificationBanner("Neuer Nutzer wurde angelegt.","info");
        Redirect("orders?add&existingCustomer=$newCNcorr");
    }
    if(isset($_POST['add_product']))
    {
        $product_nr = $_POST['product_number'];
        $customer_nr = $_POST['customer_number'];
        $order_nr = $_POST['order_number'];
        $today = date("Y-m-d H:i:s");
        $id1 = uniqid();
        $id2 = uniqid();

        $quantity = $_POST['quantity'];

        $attributes='';
        $attrList = fetch("products","attributes","number",$product_nr);

        if($attrList!="")
        {
            $attrParts = explode(';',$attrList);

            $i=1;
            foreach($attrParts as $attr)
            {
                if($i!=1) $attributes .=';';
                $attributes.= $attr.'##'.$_POST['productAttr'.$i];
                $i++;
            }
        }

        $sqlOrderExists = (!MySQLResultExists("SELECT * FROM orders WHERE order_number = '$order_nr'")) ? "INSERT INTO orders (order_number,order_date,bill_status,status) VALUES ('$order_nr','$today','pending','new');" : "" ;
        $sqlOrderCustomerLink = (!MySQLResultExists("SELECT * FROM customer_order WHERE order_number = '$order_nr' AND customer_number = '$customer_nr'")) ? "INSERT INTO customer_order (id,customer_number,order_number) VALUES ('$id1','$customer_nr','$order_nr');" : "" ;
        $sqlOrderProductLink = "INSERT INTO order_contains (id,product_number,order_number,quantity,attributes) VALUES ('$id2','$product_nr','$order_nr','$quantity','$attributes');";

        if($sqlOrderExists != "") MySQLNonQuery($sqlOrderExists);
        if($sqlOrderCustomerLink != "") MySQLNonQuery($sqlOrderCustomerLink);
        if($sqlOrderProductLink != "") MySQLNonQuery($sqlOrderProductLink);

        NotificationBanner("Produkt wurde zur Bestellung hinzugef&uuml;gt.","info");
        Redirect("orders?add&existingCustomer=$customer_nr&ordId=$order_nr");
    }

    if(isset($_POST['finish_order']))
    {
        $order_number = $_POST['finish_order'];
        NotificationBanner("Bestellung wurde fertiggestellt.","info");
        Redirect("orders?addCheckOrder=$order_number");
    }

    if(isset($_POST['finishOrderConfirmed']))
    {
        // TODO: Send email
        //if(isset($_POST['sendOrderConfirmation'])) SendOrderConmfirmation($_POST['finishOrderConfirmed']);

        $order_number = $_POST['finishOrderConfirmed'];
        $paymentType = $_POST['paymentType'];
        $paymentStatus = $_POST['paymentStatus'];
        $sendOrderConfirmation = (isset($_POST['sendOrderConfirmation'])) ? "sent" : "pending";

        $strSQL = "UPDATE orders SET payment_type = '$paymentType', payment_status = '$paymentStatus', confirmed = '1', order_confirmation_status = '$sendOrderConfirmation' WHERE order_number = '$order_number'";
        MySQLNonQuery($strSQL);

        NotificationBanner("Bestellung wurde hinzugef&uuml;gt!","check");
        Redirect("orders?new&all&selected=$order_number");
    }

// NEW ===================================================================================
// PRODUCTION ============================================================================
// FINISHED ==============================================================================
// SOLD ==================================================================================

    if(isset($_GET['add']) OR isset($_GET['addCheckOrder']))
    {
        echo '<div id="fade_in"><h1>'.langlib("Bestellung eintragen").'</h1></div><div id="content_fade_in">';
        require (isset($_GET['add'])) ? "orders_1aadd.php" : "orders_1baddCheckOrder.php" ;
        echo '</div>';
    }

    if(isset($_GET['new']))
    {
        echo '<div id="fade_in"><h1>'.langlib("Bestellungen").'</h1></div><div id="content_fade_in">';
        echo '<div id="fade_in"><h2>'.langlib("Neue Bestellungen").'</h2></div><div id="content_fade_in">';
        echo RegisterCards("new","Alle Bestellungen|!|all","Zahlung ausstehend||pending","Zahlung eingegangen||paid");

        echo '<h2>'.((isset($_GET['pending'])) ? "Zahlung ausstehend" : ((isset($_GET['paid'])) ? "Zahlung eingegangen" : "Alle Bestellungen")).'</h2>';
        echo ListStyleSelect();
        echo PreventAutoScroll();


        $sqlExtension = (isset($_GET['pending'])) ? "AND payment_status = 'pending'" : ((isset($_GET['paid'])) ? "AND payment_status = 'paid'" : '') ;
        $strSQL = "
        SELECT * FROM orders
        INNER JOIN customer_order ON orders.order_number = customer_order.order_number
        INNER JOIN customers ON customer_order.customer_number = customers.customer_number
        WHERE orders.confirmed = '1' AND status = 'new' $sqlExtension ORDER BY order_date DESC";
        echo OrderTable($strSQL,"P|Produktion Starten|G|PF","X|X|X|BR","S|Bestellung Sperren|Y|LC","C|Zahlungsstatus|Y|PS","X|Bestellung Entfernen|R|RM");

        echo '</div>';
    }

    if(isset($_GET['production']))
    {
        echo '<div id="fade_in"><h1>'.langlib("Bestellungen").'</h1></div><div id="content_fade_in">';
        echo '<div id="fade_in"><h2>'.langlib("In Produktion").'</h2></div><div id="content_fade_in">';
        echo RegisterCards("production","Alle Bestellungen|!|all","Zahlung ausstehend||pending","Zahlung eingegangen||paid");

        echo '<h2>'.((isset($_GET['pending'])) ? "Zahlung ausstehend" : ((isset($_GET['paid'])) ? "Zahlung eingegangen" : "Alle Bestellungen")).'</h2>';
        echo ListStyleSelect();
        echo PreventAutoScroll();


        $sqlExtension = (isset($_GET['pending'])) ? "AND payment_status = 'pending'" : ((isset($_GET['paid'])) ? "AND payment_status = 'paid'" : '') ;
        $strSQL = "
        SELECT * FROM orders
        INNER JOIN customer_order ON orders.order_number = customer_order.order_number
        INNER JOIN customers ON customer_order.customer_number = customers.customer_number
        WHERE orders.confirmed = '1' AND status = 'production' $sqlExtension ORDER BY order_date DESC";
        echo OrderTable($strSQL,"P|Produktion abschlie&szlig;en|G|PF","P|Produktion abbrechen/zur&uuml;cksetzen|R|PB","X|X|X|BR","S|Bestellung Sperren|Y|LC","C|Zahlungsstatus|Y|PS","X|Bestellung Entfernen|R|RM");

        echo '</div>';
    }

    if(isset($_GET['finished']))
    {
        echo '<div id="fade_in"><h1>'.langlib("Bestellungen").'</h1></div><div id="content_fade_in">';
        echo '<div id="fade_in"><h2>'.langlib("Verkaufsbereit").'</h2></div><div id="content_fade_in">';
        echo RegisterCards("finished","Alle Bestellungen|!|all","Zahlung ausstehend||pending","Zahlung eingegangen||paid");

        echo '<h2>'.((isset($_GET['pending'])) ? "Zahlung ausstehend" : ((isset($_GET['paid'])) ? "Zahlung eingegangen" : "Alle Bestellungen")).'</h2>';
        echo ListStyleSelect();
        echo PreventAutoScroll();


        $sqlExtension = (isset($_GET['pending'])) ? "AND payment_status = 'pending'" : ((isset($_GET['paid'])) ? "AND payment_status = 'paid'" : '') ;
        $strSQL = "
        SELECT * FROM orders
        INNER JOIN customer_order ON orders.order_number = customer_order.order_number
        INNER JOIN customers ON customer_order.customer_number = customers.customer_number
        WHERE orders.confirmed = '1' AND status = 'finished' $sqlExtension ORDER BY order_date DESC";
        echo OrderTable($strSQL,"P|Verkauf abschlie&szlig;en|G|PF","P|Zur&uuml;ck nach Produktion|R|PB","X|X|X|BR","S|Bestellung Sperren|Y|LC","C|Zahlungsstatus|Y|PS","X|Bestellung Entfernen|R|RM");

        echo '</div>';
    }

    if(isset($_GET['sold']))
    {
        echo '<div id="fade_in"><h1>'.langlib("Bestellungen").'</h1></div><div id="content_fade_in">';
        echo '<div id="fade_in"><h2>'.langlib("Verkauft").'</h2></div><div id="content_fade_in">';
        echo RegisterCards("sold","Alle Bestellungen|!|all","Zahlung ausstehend||pending","Zahlung eingegangen||paid");

        echo '<h2>'.((isset($_GET['pending'])) ? "Zahlung ausstehend" : ((isset($_GET['paid'])) ? "Zahlung eingegangen" : "Alle Bestellungen")).'</h2>';
        echo ListStyleSelect();
        echo PreventAutoScroll();


        $sqlExtension = (isset($_GET['pending'])) ? "AND payment_status = 'pending'" : ((isset($_GET['paid'])) ? "AND payment_status = 'paid'" : '') ;
        $strSQL = "
        SELECT * FROM orders
        INNER JOIN customer_order ON orders.order_number = customer_order.order_number
        INNER JOIN customers ON customer_order.customer_number = customers.customer_number
        WHERE orders.confirmed = '1' AND status = 'sold' $sqlExtension ORDER BY order_date DESC";
        echo OrderTable($strSQL,"P|Verkauf zur&uuml;cksetzen|R|PB","X|X|X|BR","C|Zahlungsstatus|Y|PS","S|Bestellung Sperren|Y|LC","X|Bestellung Entfernen|R|RM");

        echo '</div>';
    }

    include("footer.php");
?>