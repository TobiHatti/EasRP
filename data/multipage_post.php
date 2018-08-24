<?php
    if(isset($_POST['orderMoveFW']))
    {
        $order_number = $_POST['orderMoveFW'];
        $orderStatus = fetch("orders","status","order_number",$order_number);

        if($orderStatus == 'new')
        {
            NotificationBanner("Bestellung nach \"In Produktion\" verschoben","info");
            MySQLNonQuery("UPDATE orders SET status = 'production' WHERE order_number = '$order_number'");
        }
        if($orderStatus == 'production')
        {
            NotificationBanner("Bestellung nach \"Verkaufsbereit\" verschoben","info");
            MySQLNonQuery("UPDATE orders SET status = 'finished' WHERE order_number = '$order_number'");
        }
        if($orderStatus == 'finished')
        {
            NotificationBanner("Bestellung nach \"Verkauft\" verschoben","info");
            MySQLNonQuery("UPDATE orders SET status = 'sold' WHERE order_number = '$order_number'");
        }

        Redirect(basename($_SERVER["REQUEST_URI"], '.php'));
        die();
    }

    if(isset($_POST['orderMoveBW']))
    {
        $order_number = $_POST['orderMoveBW'];
        $orderStatus = fetch("orders","status","order_number",$order_number);

        if($orderStatus == 'production')
        {
            NotificationBanner("Bestellung nach \"Neue Bestellungen\" verschoben","info");
            MySQLNonQuery("UPDATE orders SET status = 'new' WHERE order_number = '$order_number'");
        }
        if($orderStatus == 'finished')
        {
            NotificationBanner("Bestellung nach \"In Produktion\" verschoben","info");
            MySQLNonQuery("UPDATE orders SET status = 'production' WHERE order_number = '$order_number'");
        }
        if($orderStatus == 'sold')
        {
            NotificationBanner("Bestellung nach \"Verkaufsbereit\" verschoben","info");
            MySQLNonQuery("UPDATE orders SET status = 'finished' WHERE order_number = '$order_number'");
        }

        Redirect(basename($_SERVER["REQUEST_URI"], '.php'));
        die();
    }

    if(isset($_POST['orderSetPaid']))
    {
        if(!fetch("orders","locked","order_number",$_POST['orderSetPaid']))
        {
            $order_number = $_POST['orderSetPaid'];
            NotificationBanner("Zahlungsstatus wurde auf \"Bezahlt\" gesetzt","info");
            MySQLNonQuery("UPDATE orders SET payment_status = 'paid' WHERE order_number = '$order_number'");
        }
        else NotificationBanner("&Auml;nderung nicht m&ouml;glich, Bestellung ist gesperrt","cross");

        Redirect(basename($_SERVER["REQUEST_URI"], '.php'));
        die();
    }

    if(isset($_POST['orderSetPaymentPending']))
    {
        if(!fetch("orders","locked","order_number",$_POST['orderSetPaymentPending']))
        {
            $order_number = $_POST['orderSetPaymentPending'];
            NotificationBanner("Zahlungsstatus wurde auf \"Ausstehend\" gesetzt","info");
            MySQLNonQuery("UPDATE orders SET payment_status = 'pending' WHERE order_number = '$order_number'");
        }
        else NotificationBanner("&Auml;nderung nicht m&ouml;glich, Bestellung ist gesperrt","cross");

        Redirect(basename($_SERVER["REQUEST_URI"], '.php'));
        die();
    }

    if(isset($_POST['orderRemove']))
    {
        if(!fetch("orders","locked","order_number",$_POST['orderRemove']))
        {
            $deleteDate = date('Y-m-d H:i:s', strtotime("+1 months"));
            $order_number = $_POST['orderRemove'];
            NotificationBanner("Bestellung wurde in den Papierkorb verschoben","info");
            MySQLNonQuery("UPDATE orders SET hidden = '1', delete_date = '$deleteDate' WHERE order_number = '$order_number'");
        }
        else NotificationBanner("&Auml;nderung nicht m&ouml;glich, Bestellung ist gesperrt","cross");

        Redirect(basename($_SERVER["REQUEST_URI"], '.php'));
        die();
    }

    if(isset($_POST['orderLock']))
    {
        $order_number = $_POST['orderLock'];
        NotificationBanner("Bestellung wurde gesperrt","info");
        MySQLNonQuery("UPDATE orders SET locked = '1' WHERE order_number = '$order_number'");

        Redirect(basename($_SERVER["REQUEST_URI"], '.php'));
        die();
    }

    if(isset($_POST['orderUnlock']))
    {
        $order_number = $_POST['orderUnlock'];
        NotificationBanner("Bestellung wurde gesperrt","info");
        MySQLNonQuery("UPDATE orders SET locked = '0' WHERE order_number = '$order_number'");

        Redirect(basename($_SERVER["REQUEST_URI"], '.php'));
        die();
    }

    if(isset($_POST['update_stock']))
    {
        $stock_new = MySQLSkalar("SELECT stock AS x FROM products WHERE number = '".$_POST['update_stock']."'") + $_POST['stock_add_'.$_POST['update_stock']];
        MySQLNonQuery("UPDATE products SET stock = '".$stock_new."' WHERE number = '".$_POST['update_stock']."'");

        Redirect(basename($_SERVER["REQUEST_URI"], '.php'));
        die();
    }

    if(isset($_POST['update_stock_marks']))
    {
        $pnumber = $_POST['update_stock_marks'];
        $max = $_POST['change_maxstock_'.$pnumber];
        $reorder = $_POST['change_reorderstock_'.$pnumber];
        $security = $_POST['change_securitystock_'.$pnumber];

        MySQLNonQuery("UPDATE products SET max_stock = '$max', reorder_stock='$reorder', security_stock='$security'  WHERE number = '$pnumber'");

        Redirect(basename($_SERVER["REQUEST_URI"], '.php'));
        die();
    }

    if(isset($_POST['add_subgroup1']))
    {
        $productGroup = $_POST['productGroup'];
        $hasSubGroup = (isset($_POST['hasSubGroups'])) ? 1 : 0;
        $subgroupSign = $_POST['subGroup1Short'];
        $subgroupName = $_POST['subGroup1Name'];

        MySQLNonQuery("INSERT INTO declarations (id,product_group,subgroup_type,has_subgroups,subgroup_sign,subgroup_name,parent_subgroup) VALUES ('','$productGroup','1','$hasSubGroup','$subgroupSign','$subgroupName','')");

        Redirect(basename($_SERVER["REQUEST_URI"], '.php').'#sg1');
        die();
    }

    if(isset($_POST['add_subgroup2']))
    {
        $productGroup = $_POST['productGroup'];
        $subgroupSign = $_POST['subGroup2Short'];
        $subgroupName = $_POST['subGroup2Name'];
        $parentGroup = $_POST['parentGroup'];

        MySQLNonQuery("INSERT INTO declarations (id,product_group,subgroup_type,has_subgroups,subgroup_sign,subgroup_name,parent_subgroup) VALUES ('','$productGroup','2','0','$subgroupSign','$subgroupName','$parentGroup')");

        Redirect(basename($_SERVER["REQUEST_URI"], '.php').'#sg2');
        die();
    }
?>