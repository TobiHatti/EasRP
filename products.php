<?php
    include("header.php");
// PRODUCT ===============================================================================
    if(isset($_POST['add_product']))
    {
        $error = 0;
        $name = $_POST['name'];
        $number = GetProperty("prefix_product").'-'.$_POST['number'];
        $description = $_POST['description'];
        $unit = $_POST['unit'];
        $comment = $_POST['comment'];
        $resell_price = $_POST['resell_price'];
        $attributes = $_POST['attributes'];
        $sellable = (isset($_POST['sellable'])) ? 1 : 0;

        $number_id = ($_POST['add_product']=="new") ? $number : $_POST['add_product'];

        if($_POST['add_product']=='new')
        {
            if((fetch_count("products","number",$number_id) + fetch_count("products","number",$number))==0) MySQLNonQuery("INSERT INTO products (number) VALUES ('$number')");
            else $error=1;
        }

        if($number != $number_id) rename('files/products/products/'.$number_id.'/','files/products/products/'.$number.'/');

        if($error!=1)
        {
            $path = 'files/products/products/'.$number.'/';
            MultiFileUpload($path);
            ThumbnailUpload($path,$number); 
            $strSQL = "UPDATE products SET number = '$number', name = '$name', description = '$description', unit = '$unit', comment = '$comment', resell_price = '$resell_price', attributes = '$attributes', sellable = '$sellable' WHERE number = '$number_id'";
            MySQLNonQuery($strSQL);
            MySQLNonQuery("UPDATE product_contains SET parent = '$number' WHERE parent = '$number_id'");
            MySQLNonQuery("UPDATE product_contains SET child = '$number' WHERE child = '$number_id'");
        }

        MySQLNonQuery("DELETE FROM product_contains WHERE parent LIKE '$number'");

        $parent = $number;
        $prefix_semiproduct = GetProperty("prefix_semiproduct");
        $strSQL = "SELECT * FROM products WHERE number LIKE '$prefix_semiproduct%'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            if(isset($_POST['check'.$row['number']]))
            {
                $id = uniqid();
                $child = $row['number'];
                $amt = $_POST['amt'.$row['number']];
                MySQLNonQuery("INSERT INTO product_contains (id,parent,child,quantity) VALUES ('$id','$parent','$child','$amt')");
            }
        }
        $prefix_raw = GetProperty("prefix_raw");
        $strSQL = "SELECT * FROM products WHERE number LIKE '$prefix_raw%'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            if(isset($_POST['check'.$row['number']]))
            {
                $id = uniqid();
                $child = $row['number'];
                $amt = $_POST['amt'.$row['number']];
                MySQLNonQuery("INSERT INTO product_contains (id,parent,child,quantity) VALUES ('$id','$parent','$child','$amt')");
            }
        }
        $prefix_support = GetProperty("prefix_support");
        $strSQL = "SELECT * FROM products WHERE number LIKE '$prefix_support%'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            if(isset($_POST['check'.$row['number']]))
            {
                $id = uniqid();
                $child = $row['number'];
                $amt = $_POST['amt'.$row['number']];
                MySQLNonQuery("INSERT INTO product_contains (id,parent,child,quantity) VALUES ('$id','$parent','$child','$amt')");
            }
        }

        NotificationBanner((($_POST['add_product']=="new") ? "Neues Produkt wurde angelegt." : "Produkt wurde aktualisiert"),"check");
        Redirect("products?products&show");

    }

    if(isset($_POST['delete_product']))
    {
        MySQLNonQuery('DELETE FROM products WHERE number = \''.$_POST['delete_product'].'\'');
        MySQLNonQuery('DELETE FROM product_contains WHERE parent = \''.$_POST['delete_product'].'\'');
        MySQLNonQuery('DELETE FROM product_contains WHERE child = \''.$_POST['delete_product'].'\'');
        DeleteFolder('files/products/products/'.$_POST['delete_product'].'/');
        NotificationBanner("Produkt wurde gel&ouml;scht.","check");
        Redirect("products?products&show");
    }
// SEMIPRODUCT ===========================================================================
    if(isset($_POST['add_semiproduct']))
    {
        $error = 0;
        $name = $_POST['name'];
        $number = GetProperty("prefix_semiproduct").'-'.$_POST['number'];
        $description = $_POST['description'];
        $unit = $_POST['unit'];
        $comment = $_POST['comment'];

        $number_id = ($_POST['add_semiproduct']=="new") ? $number : $_POST['add_semiproduct'];

        if($_POST['add_semiproduct']=='new')
        {
            if((fetch_count("products","number",$number_id) + fetch_count("products","number",$number))==0) MySQLNonQuery("INSERT INTO products (number) VALUES ('$number')");
            else $error=1;
        }

        if($number != $number_id) rename('files/products/semiproducts/'.$number_id.'/','files/products/semiproducts/'.$number.'/');

        if($error!=1)
        {
            $path = 'files/products/semiproducts/'.$number.'/';
            MultiFileUpload($path);
            ThumbnailUpload($path,$number);
            $strSQL = "UPDATE products SET number = '$number', name = '$name', description = '$description', unit = '$unit', comment = '$comment' WHERE number = '$number_id'";
            MySQLNonQuery($strSQL);
            MySQLNonQuery("UPDATE product_contains SET parent = '$number' WHERE parent = '$number_id'");
            MySQLNonQuery("UPDATE product_contains SET child = '$number' WHERE child = '$number_id'");
        }

        MySQLNonQuery("DELETE FROM product_contains WHERE parent LIKE '$number'");

        $parent = $number;
        $prefix_raw = GetProperty("prefix_raw");
        $strSQL = "SELECT * FROM products WHERE number LIKE '$prefix_raw%'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            if(isset($_POST['check'.$row['number']]))
            {
                $id = uniqid();
                $child = $row['number'];
                $amt = $_POST['amt'.$row['number']];
                MySQLNonQuery("INSERT INTO product_contains (id,parent,child,quantity) VALUES ('$id','$parent','$child','$amt')");
            }
        }
        $prefix_support = GetProperty("prefix_support");
        $strSQL = "SELECT * FROM products WHERE number LIKE '$prefix_support%'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            if(isset($_POST['check'.$row['number']]))
            {
                $id = uniqid();
                $child = $row['number'];
                $amt = $_POST['amt'.$row['number']];
                MySQLNonQuery("INSERT INTO product_contains (id,parent,child,quantity) VALUES ('$id','$parent','$child','$amt')");
            }
        }

        NotificationBanner((($_POST['add_semiproduct']=="new") ? "Neues Halbprodukt wurde angelegt." : "Halbprodukt wurde aktualisiert"),"check");
        Redirect("products?semiproducts&show");

    }

    if(isset($_POST['delete_semiproduct']))
    {
        MySQLNonQuery('DELETE FROM products WHERE number = \''.$_POST['delete_semiproduct'].'\'');
        MySQLNonQuery('DELETE FROM product_contains WHERE parent = \''.$_POST['delete_semiproduct'].'\'');
        MySQLNonQuery('DELETE FROM product_contains WHERE child = \''.$_POST['delete_semiproduct'].'\'');
        DeleteFolder('files/products/semiproducts/'.$_POST['delete_semiproduct'].'/');
        NotificationBanner("Halbprodukt wurde gel&ouml;scht.","check");
        Redirect("products?semiproduct&show");
    }

// RAW-PRODUCT ===========================================================================
    if(isset($_POST['add_raw_product']))
    {
        $error = 0;
        $name = $_POST['name'];
        $number = (!isset($_POST['declarationAssistent'])) ? (GetProperty("prefix_raw").'-'.$_POST['numberDefault']) : $_POST['numberAssist'];
        $description = $_POST['description'];
        $unit = $_POST['unit'];
        $price = $_POST['price'];
        $per_item = $_POST['per_item'];
        $company = $_POST['company'];
        $retailer = $_POST['retailer'];
        $link = $_POST['link'];
        $comment = $_POST['comment'];
        $max_stock = $_POST['max_stock'];
        $reorder_stock = $_POST['reorder_stock'];
        $security_stock = $_POST['security_stock'];
        $storage_location = $_POST['storage_location'];

        $number_id = ($_POST['add_raw_product']=="new") ? $number : $_POST['add_raw_product'];

        if($_POST['add_raw_product']=='new')
        {
            if((fetch_count("products","number",$number_id) + fetch_count("products","number",$number))==0) MySQLNonQuery("INSERT INTO products (number) VALUES ('$number')");
            else $error=1;
        }

        if($number != $number_id) rename('files/products/raw_products/'.$number_id.'/','files/products/raw_products/'.$number.'/');

        if($error!=1)
        {
            $path = 'files/products/raw_products/'.$number.'/';
            MultiFileUpload($path);
            ThumbnailUpload($path,$number);
            $strSQL = "UPDATE products SET number = '$number', name = '$name', description = '$description', unit = '$unit', price = '$price', per_item = '$per_item', production_company = '$company', retail_company = '$retailer', link = '$link', comment = '$comment', max_stock = '$max_stock', reorder_stock = '$reorder_stock', security_stock = '$security_stock', storage_location = '$storage_location' WHERE number = '$number_id'";
            echo $strSQL;
            MySQLNonQuery($strSQL);
            MySQLNonQuery("UPDATE product_contains SET parent = '$number' WHERE parent = '$number_id'");
            MySQLNonQuery("UPDATE product_contains SET child = '$number' WHERE child = '$number_id'");
        }

        NotificationBanner((($_POST['add_raw_product']=="new") ? "Neues Rohteil wurde angelegt." : "Rohteil wurde aktualisiert"),"check");
        //Redirect("products?raw&show");

    }

    if(isset($_POST['delete_raw_item']))
    {
        MySQLNonQuery('DELETE FROM products WHERE number = \''.$_POST['delete_raw_item'].'\'');
        MySQLNonQuery('DELETE FROM product_contains WHERE parent = \''.$_POST['delete_raw_item'].'\'');
        MySQLNonQuery('DELETE FROM product_contains WHERE child = \''.$_POST['delete_raw_item'].'\'');
        DeleteFolder('files/products/raw_products/'.$_POST['delete_raw_item'].'/');
        NotificationBanner("Rohteil wurde gel&ouml;scht.","check");
        Redirect("products?raw&show");
    }
// SUPPORT ===============================================================================
    if(isset($_POST['add_support_product']))
    {
        $error = 0;
        $name = $_POST['name'];
        $number = GetProperty("prefix_support").'-'.$_POST['number'];
        $description = $_POST['description'];
        $unit = $_POST['unit'];
        $price = $_POST['price'];
        $per_item = $_POST['per_item'];
        $company = $_POST['company'];
        $retailer = $_POST['retailer'];
        $link = $_POST['link'];
        $comment = $_POST['comment'];
        $max_stock = $_POST['max_stock'];
        $reorder_stock = $_POST['reorder_stock'];
        $security_stock = $_POST['security_stock'];
        $storage_location = $_POST['storage_location'];

        $number_id = ($_POST['add_support_product']=="new") ? $number : $_POST['add_support_product'];

        if($_POST['add_support_product']=='new')
        {
            if((fetch_count("products","number",$number_id) + fetch_count("products","number",$number))==0) MySQLNonQuery("INSERT INTO products (number) VALUES ('$number')");
            else $error=1;
        }

        if($number != $number_id) rename('files/products/support_products/'.$number_id.'/','files/products/support_products/'.$number.'/');

        if($error!=1)
        {
            $path = 'files/products/support_products/'.$number.'/';
            MultiFileUpload($path);
            ThumbnailUpload($path,$number);
            $strSQL = "UPDATE products SET number = '$number', name = '$name', description = '$description', unit = '$unit', price = '$price', per_item = '$per_item', production_company = '$company', retail_company = '$retailer', link = '$link', comment = '$comment', max_stock = '$max_stock', reorder_stock = '$reorder_stock', security_stock = '$security_stock', storage_location = '$storage_location' WHERE number = '$number_id'";
            MySQLNonQuery($strSQL);
            MySQLNonQuery("UPDATE product_contains SET parent = '$number' WHERE parent = '$number_id'");
            MySQLNonQuery("UPDATE product_contains SET child = '$number' WHERE child = '$number_id'");
        }

        NotificationBanner((($_POST['add_support_product']=="new") ? "Neues Hilfsprodukt wurde angelegt." : "Hilfsprodukt wurde aktualisiert"),"check");
        Redirect("products?support&show");

    }

    if(isset($_POST['delete_support_item']))
    {
        MySQLNonQuery('DELETE FROM products WHERE number = \''.$_POST['delete_support_item'].'\'');
        MySQLNonQuery('DELETE FROM product_contains WHERE parent = \''.$_POST['delete_support_item'].'\'');
        MySQLNonQuery('DELETE FROM product_contains WHERE child = \''.$_POST['delete_support_item'].'\'');
        DeleteFolder('files/products/support_products/'.$_POST['delete_support_item'].'/');
        NotificationBanner("Hilfsprodukt wurde gel&ouml;scht.","check");
        Redirect("products?support&show");
    }
// =======================================================================================

    echo '<div id="fade_in"><h1>'.langlib("Produkte").'</h1></div>';

    echo RegisterCards("","Produkte|!|products","Halbprodukte||semiproducts","Rohteile||raw","Hilfsmittel||support","|||","Warenstruktur||structure","Lagerstandorte||storagelocations");

    if(isset($_GET['products']))
    {
        echo RegisterCards("products","Produkte anzeigen|!|show","Neues Produkt eintragen||new","Produkt bearbeiten||edit","Produkt l&ouml;schen||delete");
        echo '<div id="content_fade_in">';
        require "products_1product.php";
        echo '</div>';
    }

    if(isset($_GET['semiproducts']))
    {
        echo RegisterCards("semiproducts","Halbprodukte anzeigen|!|show","Neues Halbprodukt eintragen||new","Halbprodukt bearbeiten||edit","Halbprodukt l&ouml;schen||delete");
        echo '<div id="content_fade_in">';
        require "products_2semiproduct.php";
        echo '</div>';
    }

    if(isset($_GET['raw']))
    {
        echo RegisterCards("raw","Rohteile anzeigen|!|show","Neues Rohteil eintragen||new","Rohteil bearbeiten||edit","Rohteil l&ouml;schen||delete","Deklarationsassistent||declaration#sg1");
        echo '<div id="content_fade_in">';
        require "products_3raw.php";
        echo '</div>';
    }

    if(isset($_GET['support']))
    {
        echo RegisterCards("support","Hilfsmittel anzeigen|!|show","Neues Hilfsmittel eintragen||new","Hilfsmittel bearbeiten||edit","Hilfsmittel l&ouml;schen||delete");
        echo '<div id="content_fade_in">';
        require "products_4support.php";
        echo '</div>';
    }

    if(isset($_GET['structure']))
    {
        echo '<div id="content_fade_in">';
        require "products_5structure.php";
        echo '</div>';
    }

    if(isset($_GET['storagelocations']))
    {
        echo '<div id="content_fade_in">';
        require "products_6storagelocations.php";
        echo '</div>';
    }

    include("footer.php");
?>