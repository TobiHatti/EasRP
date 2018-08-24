<?php

   function file_upload($type)
    {
        if($type==1)   //BOX
        {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["fileToUpload1"]["name"]);
            $uploadOk = 1;
            $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
            // Check if image file is a actual image or fake image
            if(isset($_POST["submit1"]))
            {
                $check = getimagesize($_FILES["fileToUpload1"]["tmp_name"]);
                if($check !== false)
                {
                    $uploadOk = 1;
                }
                else
                {
                    echo "<br>Der Ausgew&auml;hlte Dateityp wird nicht unterst&uuml;tzt!.";
                    $uploadOk = 0;
                }
            }
            // Check if file already exists
            if (file_exists($target_file))
            {
                echo "<br>Fehler - Die Datei besteht bereits [Drastic File Overflow - Please contact an Administrator].";
                $uploadOk = 0;
            }
            // Check file size
            if ($_FILES["fileToUpload1"]["size"] > 5242880) // 5242880 = 5MB
            {
                echo "<br>Ihr Bild ist leider zu gro&szlig.<br>Bitte Skalieren Sie es ungef&auml;hr auf die gr&ouml;&szlige 200x200px";
                $uploadOk = 0;
            }
            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "JPG" && $imageFileType != "PNG" && $imageFileType != "JPEG")
            {
                echo "<br>Es sind nur die Dateitypen JPG, JPEG, PNG & GIF erlaubt.";
                $uploadOk = 0;
            }
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0)
            {
                echo "<br>Ein Fehler ist aufgetreten, bitte versuchen Sie es sp&auml;ter noch einmal (1)";
            // if everything is ok, try to upload file
            }
            else
            {
                if (move_uploaded_file($_FILES["fileToUpload1"]["tmp_name"], $target_file)) echo 'Die Datei "'. basename( $_FILES["fileToUpload1"]["name"]). '" wurde erfolgreich hochgeladen!';
                else echo "<br>Ein Fehler ist aufgetreten, bitte versuchen Sie es sp&auml;ter noch einmal (2)";
            }
            if($uploadOk==1)
            {
                $uid = uniqid();
                rename('uploads/'.basename($_FILES["fileToUpload1"]["name"]), 'uup/'.$uid.'.'.$imageFileType);
                $_SESSION['upload_img_box'] = $uid.'.'.$imageFileType;
            }
        }

        if($type==2)  //PEN
        {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["fileToUpload2"]["name"]);
            $uploadOk = 1;
            $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
            // Check if image file is a actual image or fake image
            if(isset($_POST["submit2"]))
            {
                $check = getimagesize($_FILES["fileToUpload2"]["tmp_name"]);
                if($check !== false)
                {
                    $uploadOk = 1;
                }
                else
                {
                    echo "<br>Der Ausgew&auml;hlte Dateityp wird nicht unterst&uuml;tzt!.";
                    $uploadOk = 0;
                }
            }
            // Check if file already exists
            if (file_exists($target_file))
            {
                echo "<br>Fehler - Die Datei besteht bereits [Drastic File Overflow - Please contact an Administrator].";
                $uploadOk = 0;
            }
            // Check file size
            if ($_FILES["fileToUpload2"]["size"] > 5242880) // 5242880 = 5MB
            {
                echo "<br>Ihr Bild ist leider zu gro&szlig.<br>Bitte Skalieren Sie es ungef&auml;hr auf die gr&ouml;&szlige 200x200px";
                $uploadOk = 0;
            }
            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "JPG" && $imageFileType != "PNG" && $imageFileType != "JPEG")
            {
                echo "<br>Es sind nur die Dateitypen JPG, JPEG, PNG & GIF erlaubt.";
                $uploadOk = 0;
            }
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0)
            {
                echo "<br>Ein Fehler ist aufgetreten, bitte versuchen Sie es sp&auml;ter noch einmal (1)";
            // if everything is ok, try to upload file
            }
            else
            {
                if (move_uploaded_file($_FILES["fileToUpload2"]["tmp_name"], $target_file)) echo 'Die Datei "'. basename( $_FILES["fileToUpload2"]["name"]). '" wurde erfolgreich hochgeladen!';
                else echo "<br>Ein Fehler ist aufgetreten, bitte versuchen Sie es sp&auml;ter noch einmal (2)";
            }
            if($uploadOk==1)
            {
                $uid = uniqid();
                rename('uploads/'.basename($_FILES["fileToUpload2"]["name"]), 'uup/'.$uid.'.'.$imageFileType);
                $_SESSION['upload_img_pen'] = $uid.'.'.$imageFileType;
            }
        }

    }

    function fetch($db,$get,$col,$like)
    {
        include("mysql_connect.php");

        $strSQL = "SELECT * FROM $db WHERE $col LIKE '$like'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs)) return $row[$get];
    }

    function fetch_count($db,$col,$like)
    {
        include("mysql_connect.php");

        $strSQL = "SELECT * FROM $db WHERE $col LIKE '$like'";
        $rs=mysqli_query($link,$strSQL);
        return mysqli_num_rows($rs);
    }

    function ifset_fill($field,$alternative='')
    {
        if(isset($_SESSION['product_id']) AND $_SESSION['product_id']!=0)
        {
            include("mysql_connect.php");

            $id = $_SESSION['product_id'];
            $strSQL = "SELECT * FROM products WHERE id LIKE '$id'";
            $rs=mysqli_query($link,$strSQL);
            while($row=mysqli_fetch_assoc($rs)) $retval = $row[$field];

            if($retval == '') return $alternative;
            else return $retval;
        }
        else return $alternative;
    }

    function ifset_fill_0($field,$alternative='')
    {
        if(isset($_SESSION['product_id']) AND $_SESSION['product_id']!=0)
        {
            include("mysql_connect.php");

            $id = $_SESSION['product_id'];
            $strSQL = "SELECT * FROM products WHERE id LIKE '$id'";
            $rs=mysqli_query($link,$strSQL);
            while($row=mysqli_fetch_assoc($rs)) $retval = $row[$field];

            if($retval == '0') return $alternative;
            else return $retval;
        }
        else return $alternative;
    }

    function ifset_fillc($field,$alternative='')
    {
        if(isset($_SESSION['customer_id']) AND $_SESSION['customer_id']!=0)
        {
            include("mysql_connect.php");

            $id = $_SESSION['customer_id'];
            $strSQL = "SELECT * FROM customers WHERE id LIKE '$id'";
            $rs=mysqli_query($link,$strSQL);
            while($row=mysqli_fetch_assoc($rs)) $retval = $row[$field];

            if($retval == '') return $alternative;
            else return $retval;
        }
        else return $alternative;
    }

    function base_price($company_product_id)
    {
        include("mysql_connect.php");
        $strSQL = "SELECT * FROM graduating WHERE product_id LIKE '$company_product_id' AND amount LIKE '1'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs)) return number_format($row['price_per_item'],2);
    }

    function price_grad_per_amt($company_product_id,$amount)
    {
        include("mysql_connect.php");
        // Get base Price. This gets changed in case there are higher graduations
        $price = base_price($company_product_id);

        $strSQL = "SELECT * FROM graduating WHERE product_id LIKE '$company_product_id' AND amount LIKE '$amount'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs)) $price = $row['price_per_item'];
        return number_format($price,2);
    }


    function price_specific($product_id)
    {
        include("mysql_connect.php");

        $price=0;
        $strSQL = "SELECT * FROM products WHERE id LIKE '$product_id'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            if($row['box_set']=='set')
            {
                if($row['amt_pen_b']==0 AND $row['amt_pen_r']==0) $product_nr='WECP-1026';
                else if($row['amt_pen_b']==0 AND $row['amt_pencil']==0) $product_nr='WECP-1025';
                else if($row['amt_pen_r']==0 AND $row['amt_pencil']==0) $product_nr='WECP-1024';
                else if($row['amt_pen_b']==0) $product_nr='WECP-1022';
                else if($row['amt_pen_r']==0) $product_nr='WECP-1021';
                else if($row['amt_pencil']==0) $product_nr='WECP-1023';

                $sproduct_id = fetch("company_products","id","product_nr",$product_nr);

                $strSQLp = "SELECT * FROM graduating WHERE product_id LIKE '$sproduct_id'";
                $rsp=mysqli_query($link,$strSQLp);
                while($rowp=mysqli_fetch_assoc($rsp))  if($row['amt_box']>=$rowp['amount']) $base_price = $rowp['price_per_item'];

                $price = $base_price * $row['amt_box'];
            }

            if($row['box_set']=='pen')
            {
                // CASE 1 : GRADUATING FOR THE TOTAL AMOUNT OF PENS FOR EACH PEN
                // CASE 2 : GRADUATING FOR EACH PEN INDIVIDUALY

                // ============= SELECT CASE HERE: ============
                                    $CASE = 1;
                // ============================================

                if($CASE == 1)
                {
                    // CASE 1 : GRADUATING FOR THE TOTAL AMOUNT OF PENS FOR EACH PEN
                    $pen_cnt_total = $row['amt_pen_b'] + $row['amt_pen_r'] + $row['amt_pencil'];

                    if($row['amt_pen_b']!=0)
                    {
                        $sproduct_id = fetch("company_products","id","product_nr","WECP-1011");

                        $strSQLp = "SELECT * FROM graduating WHERE product_id LIKE '$sproduct_id'";
                        $rsp=mysqli_query($link,$strSQLp);
                        while($rowp=mysqli_fetch_assoc($rsp)) if($pen_cnt_total>=$rowp['amount']) $base_price = $rowp['price_per_item'];

                        $price += $base_price * $row['amt_pen_b'];
                    }

                    if($row['amt_pen_r']!=0)
                    {
                        $sproduct_id = fetch("company_products","id","product_nr","WECP-1012");

                        $strSQLp = "SELECT * FROM graduating WHERE product_id LIKE '$sproduct_id'";
                        $rsp=mysqli_query($link,$strSQLp);
                        while($rowp=mysqli_fetch_assoc($rsp)) if($pen_cnt_total>=$rowp['amount']) $base_price = $rowp['price_per_item'];

                        $price += $base_price * $row['amt_pen_r'];
                    }

                    if($row['amt_pencil']!=0)
                    {
                        $sproduct_id = fetch("company_products","id","product_nr","WECP-1013");

                        $strSQLp = "SELECT * FROM graduating WHERE product_id LIKE '$sproduct_id'";
                        $rsp=mysqli_query($link,$strSQLp);
                        while($rowp=mysqli_fetch_assoc($rsp)) if($pen_cnt_total>=$rowp['amount']) $base_price = $rowp['price_per_item'];

                        $price += $base_price * $row['amt_pencil'];
                    }
                }

                if($CASE == 2)
                {
                    // CASE 2 : GRADUATING FOR EACH PEN INDIVIDUALY

                    if($row['amt_pen_b']!=0)
                    {
                        $sproduct_id = fetch("company_products","id","product_nr","WECP-1011");

                        $strSQLp = "SELECT * FROM graduating WHERE product_id LIKE '$sproduct_id'";
                        $rsp=mysqli_query($link,$strSQLp);
                        while($rowp=mysqli_fetch_assoc($rsp)) if($row['amt_pen_b']>=$rowp['amount']) $base_price = $rowp['price_per_item'];

                        $price += $base_price * $row['amt_pen_b'];
                    }

                    if($row['amt_pen_r']!=0)
                    {
                        $sproduct_id = fetch("company_products","id","product_nr","WECP-1012");

                        $strSQLp = "SELECT * FROM graduating WHERE product_id LIKE '$sproduct_id'";
                        $rsp=mysqli_query($link,$strSQLp);
                        while($rowp=mysqli_fetch_assoc($rsp)) if($row['amt_pen_r']>=$rowp['amount']) $base_price = $rowp['price_per_item'];

                        $price += $base_price * $row['amt_pen_r'];
                    }

                    if($row['amt_pencil']!=0)
                    {
                        $sproduct_id = fetch("company_products","id","product_nr","WECP-1013");

                        $strSQLp = "SELECT * FROM graduating WHERE product_id LIKE '$sproduct_id'";
                        $rsp=mysqli_query($link,$strSQLp);
                        while($rowp=mysqli_fetch_assoc($rsp)) if($row['amt_pencil']>=$rowp['amount']) $base_price = $rowp['price_per_item'];

                        $price += $base_price * $row['amt_pencil'];
                    }
                }

            }

            if($row['box_set']=='box')
            {
                $sproduct_id = fetch("company_products","id","product_nr","WECP-1001");

                $strSQLp = "SELECT * FROM graduating WHERE product_id LIKE '$sproduct_id'";
                $rsp=mysqli_query($link,$strSQLp);
                while($rowp=mysqli_fetch_assoc($rsp)) if($row['amt_box']>=$rowp['amount']) $base_price = $rowp['price_per_item'];

                $price = $base_price * $row['amt_box'];
            }

            return number_format($price,2);
        }
    }

    function check_discount_allowed($customer_id)
    {
        include("mysql_connect.php");
        $retval = 1;

        $strSQL = "SELECT * FROM orders WHERE customer_id LIKE '$customer_id'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            $product_id = $row['product_id'];
            $strSQLp = "SELECT * FROM products WHERE id LIKE '$product_id'";
            $rsp=mysqli_query($link,$strSQLp);
            while($rowp=mysqli_fetch_assoc($rsp))
            {
                if($rowp['box_set']=='discount') $retval = 0;
            }
        }

        return $retval;
    }

    function total_price($customer_id)
    {
        include("mysql_connect.php");
        $price=0;

        $strSQL = "SELECT * FROM orders WHERE customer_id LIKE '$customer_id'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            $price += price_specific($row['product_id']);
        }

        $price = add_discount($price,$customer_id);

        return number_format($price,2);
    }

    function add_discount($price,$customer_id)
    {
        include("mysql_connect.php");
        $discount_type='';
        $strSQL = "SELECT * FROM orders WHERE customer_id LIKE '$customer_id'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            $product_id = $row['product_id'];
            $strSQLp = "SELECT * FROM products WHERE box_set LIKE 'discount' AND id LIKE '$product_id'";
            $rsp=mysqli_query($link,$strSQLp);
            while($rowp=mysqli_fetch_assoc($rsp))
            {
                $discount_code = $rowp['text1_t'];
                $discount_type = fetch("discount_codes","discount_type","code",$discount_code);
                $discount_amount = fetch("discount_codes","discount_amount","code",$discount_code);
            }
        }

        if($discount_type=="percent") $price = $price * ((100-$discount_amount)/100);
        if($discount_type=="absolute") $price = $price - $discount_amount;

        return $price;
    }

    function graduated_price_at_amount($company_product_id,$amount)
    {
        include("mysql_connect.php");

        $base_price=1;

        $strSQL = "SELECT * FROM graduating WHERE product_id LIKE '$company_product_id'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs)) if($amount>=$row['amount']) $base_price = $row['price_per_item'];

        return $base_price;
    }

    function mail_msg($subject,$message,$mail)
    {
        include("mysql_connect.php");

        ini_set("SMTP", "smtp.easyname.com");
        ini_set("sendmail_from", "no-reply@wepen.at");

        $headers = "From: no-reply@wepen.at\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";


        mail($mail, $subject, $message, $headers);

    }

    function protocol_add($string)
    {
        include("mysql_connect.php");

        $id=uniqid();
        $user = $_SESSION['user_id'];
        $date = date("D M j Y G:i:s ");

        $strSQL = "INSERT INTO protocol (id,user,date,description) VALUES ('$id','$user','$date','$string')";
        $rs=mysqli_query($link,$strSQL);
    }

    function order_confirmation_mail($customer_id)
    {
        include("mysql_connect.php");

        $strSQL = "SELECT * FROM customers WHERE id LIKE '$customer_id'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            $payment_method = $row['payment_method'];
            $order_nr = $row['order_nr'];
            $price_total = total_price($customer_id);
        }

        $retval = '
            <!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head>
                <!--[if gte mso 9]><xml>
                 <o:OfficeDocumentSettings>
                  <o:AllowPNG/>
                  <o:PixelsPerInch>96</o:PixelsPerInch>
                 </o:OfficeDocumentSettings>
                </xml><![endif]-->
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <meta name="viewport" content="width=device-width">
                <!--[if !mso]><!--><meta http-equiv="X-UA-Compatible" content="IE=edge"><!--<![endif]-->
                <title></title>


                <style type="text/css" id="media-query">
                  body {
              margin: 0;
              padding: 0; }

            table, tr, td {
              vertical-align: top;
              border-collapse: collapse; }

            .ie-browser table, .mso-container table {
              table-layout: fixed; }

            * {
              line-height: inherit; }

            a[x-apple-data-detectors=true] {
              color: inherit !important;
              text-decoration: none !important; }

            [owa] .img-container div, [owa] .img-container button {
              display: block !important; }

            [owa] .fullwidth button {
              width: 100% !important; }

            [owa] .block-grid .col {
              display: table-cell;
              float: none !important;
              vertical-align: top; }

            .ie-browser .num12, .ie-browser .block-grid, [owa] .num12, [owa] .block-grid {
              width: 740px !important; }

            .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {
              line-height: 100%; }

            .ie-browser .mixed-two-up .num4, [owa] .mixed-two-up .num4 {
              width: 244px !important; }

            .ie-browser .mixed-two-up .num8, [owa] .mixed-two-up .num8 {
              width: 488px !important; }

            .ie-browser .block-grid.two-up .col, [owa] .block-grid.two-up .col {
              width: 370px !important; }

            .ie-browser .block-grid.three-up .col, [owa] .block-grid.three-up .col {
              width: 246px !important; }

            .ie-browser .block-grid.four-up .col, [owa] .block-grid.four-up .col {
              width: 185px !important; }

            .ie-browser .block-grid.five-up .col, [owa] .block-grid.five-up .col {
              width: 148px !important; }

            .ie-browser .block-grid.six-up .col, [owa] .block-grid.six-up .col {
              width: 123px !important; }

            .ie-browser .block-grid.seven-up .col, [owa] .block-grid.seven-up .col {
              width: 105px !important; }

            .ie-browser .block-grid.eight-up .col, [owa] .block-grid.eight-up .col {
              width: 92px !important; }

            .ie-browser .block-grid.nine-up .col, [owa] .block-grid.nine-up .col {
              width: 82px !important; }

            .ie-browser .block-grid.ten-up .col, [owa] .block-grid.ten-up .col {
              width: 74px !important; }

            .ie-browser .block-grid.eleven-up .col, [owa] .block-grid.eleven-up .col {
              width: 67px !important; }

            .ie-browser .block-grid.twelve-up .col, [owa] .block-grid.twelve-up .col {
              width: 61px !important; }

            @media only screen and (min-width: 760px) {
              .block-grid {
                width: 740px !important; }
              .block-grid .col {
                vertical-align: top; }
                .block-grid .col.num12 {
                  width: 740px !important; }
              .block-grid.mixed-two-up .col.num4 {
                width: 244px !important; }
              .block-grid.mixed-two-up .col.num8 {
                width: 488px !important; }
              .block-grid.two-up .col {
                width: 370px !important; }
              .block-grid.three-up .col {
                width: 246px !important; }
              .block-grid.four-up .col {
                width: 185px !important; }
              .block-grid.five-up .col {
                width: 148px !important; }
              .block-grid.six-up .col {
                width: 123px !important; }
              .block-grid.seven-up .col {
                width: 105px !important; }
              .block-grid.eight-up .col {
                width: 92px !important; }
              .block-grid.nine-up .col {
                width: 82px !important; }
              .block-grid.ten-up .col {
                width: 74px !important; }
              .block-grid.eleven-up .col {
                width: 67px !important; }
              .block-grid.twelve-up .col {
                width: 61px !important; } }

            @media (max-width: 760px) {
              .block-grid, .col {
                min-width: 320px !important;
                max-width: 100% !important;
                display: block !important; }
              .block-grid {
                width: calc(100% - 40px) !important; }
              .col {
                width: 100% !important; }
                .col > div {
                  margin: 0 auto; }
              img.fullwidth, img.fullwidthOnMobile {
                max-width: 100% !important; } }

                </style>
            </head>
            <body class="clean-body" style="margin: 0;padding: 0;-webkit-text-size-adjust: 100%;background-color: #FFFFFF">
              <style type="text/css" id="media-query-bodytag">
                @media (max-width: 760px) {
                  .block-grid {
                    min-width: 320px!important;
                    max-width: 100%!important;
                    width: 100%!important;
                    display: block!important;
                  }

                  .col {
                    min-width: 320px!important;
                    max-width: 100%!important;
                    width: 100%!important;
                    display: block!important;
                  }

                  .col>div {
                    margin: 0 auto;
                  }

                  img.fullwidth {
                    max-width: 100%!important;
                  }

                  img.fullwidthOnMobile {
                    max-width: 100%!important;
                  }
                }
              </style>
              <!--[if IE]><div class="ie-browser"><![endif]-->
              <!--[if mso]><div class="mso-container"><![endif]-->
              <table class="nl-container" style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;min-width: 320px;Margin: 0 auto;background-color: #FFFFFF;width: 100%" cellpadding="0" cellspacing="0">
                <tbody>
                  <tr style="vertical-align: top">
                    <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;padding: 0">
                      <!--[if (mso)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="center" style="background-color: #FFFFFF;"><![endif]-->
                      <!--[if (IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="center" style="background-color: #FFFFFF;"><![endif]-->

                      <div style="background-color:transparent;">
                        <div style="Margin: 0 auto;min-width: 320px;max-width: 740px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;" class="block-grid three-up">
                          <div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
                            <!--[if (mso)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="background-color:transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width: 740px;"><tr class="layout-full-width" style="background-color:transparent;"><![endif]-->
                            <!--[if (IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="background-color:transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width: 740px;"><tr class="layout-full-width" style="background-color:transparent;"><![endif]-->

                                <!--[if (mso)]><td align="center" width="247" style=" width:247px; padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><![endif]-->
                                <!--[if (IE)]><td align="center" width="247" style=" width:247px; padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><![endif]-->
                              <div class="col num4" style="max-width: 320px;min-width: 246px;display: table-cell;vertical-align: top;">
                                <div style="background-color: transparent; width: 100% !important;">
                                <!--[if (!mso)]><!--><!--[if (!IE)]><!--><div style="border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;"><!--<![endif]--><!--<![endif]-->


                                      <div align="center" class="img-container center fixedwidth" style="padding-right: 0px;  padding-left: 0px;">
            <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px;" align="center"><![endif]-->
              <img class="center fixedwidth" align="center" border="0" src="http://designer.wepen.at/files/mail_images/icon.png" alt="Image" title="Image" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: 0;height: auto;float: none;width: 100%;max-width: 49.4px" width="49.4">
            <!--[if mso]></td></tr></table><![endif]-->
            </div>


                                <!--[if (!mso)]><!--><!--[if (!IE)]><!--></div><!--<![endif]--><!--<![endif]-->
                                </div>
                              </div>
                                <!--[if (mso)]></td><td align="center" width="247" style=" width:247px; padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><![endif]-->
                                <!--[if (IE)]></td><td align="center" width="247" style=" width:247px; padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><![endif]-->
                              <div class="col num4" style="max-width: 320px;min-width: 246px;display: table-cell;vertical-align: top;">
                                <div style="background-color: transparent; width: 100% !important;">
                                <!--[if (!mso)]><!--><!--[if (!IE)]><!--><div style="border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;"><!--<![endif]--><!--<![endif]-->


                                      <div align="center" class="img-container center  autowidth  fullwidth" style="padding-right: 0px;  padding-left: 0px;">
            <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px;" align="center"><![endif]-->
              <img class="center  autowidth  fullwidth" align="center" border="0" src="http://designer.wepen.at/files/mail_images/logo.png" alt="Image" title="Image" style="outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: 0;height: auto;float: none;width: 100%;max-width: 246.666666666667px" width="246.666666666667">
            <!--[if mso]></td></tr></table><![endif]-->
            </div>


                                <!--[if (!mso)]><!--><!--[if (!IE)]><!--></div><!--<![endif]--><!--<![endif]-->
                                </div>
                              </div>
                                <!--[if (mso)]></td><td align="center" width="247" style=" width:247px; padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><![endif]-->
                                <!--[if (IE)]></td><td align="center" width="247" style=" width:247px; padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><![endif]-->
                              <div class="col num4" style="max-width: 320px;min-width: 246px;display: table-cell;vertical-align: top;">
                                <div style="background-color: transparent; width: 100% !important;">
                                <!--[if (!mso)]><!--><!--[if (!IE)]><!--><div style="border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;"><!--<![endif]--><!--<![endif]-->


                                      <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;"><![endif]-->
            <div style="color:#555555;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif; padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">
            	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 12px;line-height: 14px"><br data-mce-bogus="1"></p></div>
            </div>
            <!--[if mso]></td></tr></table><![endif]-->


                                <!--[if (!mso)]><!--><!--[if (!IE)]><!--></div><!--<![endif]--><!--<![endif]-->
                                </div>
                              </div>
                            <!--[if (mso)]></td></tr/></table></td></tr></table></td></tr></table><![endif]-->
                            <!--[if (IE)]></td></tr/></table></td></tr></table></td></tr></table><![endif]-->
                          </div>
                        </div>
                      </div>          <div style="background-color:transparent;">
                        <div style="Margin: 0 auto;min-width: 320px;max-width: 740px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: transparent;" class="block-grid ">
                          <div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
                            <!--[if (mso)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="background-color:transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width: 740px;"><tr class="layout-full-width" style="background-color:transparent;"><![endif]-->
                            <!--[if (IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="background-color:transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width: 740px;"><tr class="layout-full-width" style="background-color:transparent;"><![endif]-->

                                <!--[if (mso)]><td align="center" width="740" style=" width:740px; padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px;" valign="top"><![endif]-->
                                <!--[if (IE)]><td align="center" width="740" style=" width:740px; padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px;" valign="top"><![endif]-->
                              <div class="col num12" style="min-width: 320px;max-width: 740px;display: table-cell;vertical-align: top;">
                                <div style="background-color: transparent; width: 100% !important;">
                                <!--[if (!mso)]><!--><!--[if (!IE)]><!--><div style="padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;"><!--<![endif]--><!--<![endif]-->


                                      <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;"><![endif]-->
            <div style="color:#555555;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif; padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">
            	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 12px;line-height: 14px;text-align: center"><span style="font-size: 38px; line-height: 57px;">Auftragsbestätigung<br /> '.$order_nr.'</span>&#160;<br></p><br></div>
            </div>
            <!--[if mso]></td></tr></table><![endif]-->



                                      <div style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">
              <!--[if (mso)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px;padding-left: 10px; padding-top: 10px; padding-bottom: 10px;"><table width="100%" align="center" cellpadding="0" cellspacing="0" border="0"><tr><td><![endif]-->
              <div align="center"><div style="border-top: 1px solid #BBBBBB; width:100%; line-height:1px; height:1px; font-size:1px;">&#160;</div></div>
              <!--[if (mso)]></td></tr></table></td></tr></table><![endif]-->
            </div>



                                      <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;"><![endif]-->
            <div style="color:#555555;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif; padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">
            	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px;text-align: center"><span style="font-size: 22px; line-height: 26px;">Ihre Bestellung</span></p></div>
            </div>
            <!--[if mso]></td></tr></table><![endif]-->



                                      <div style="font-size: 16px;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif; text-align: center;">


            <table style="font-size:15pt;border-spacing:0px; width: 700px;">
            <tr>
                <th style="border-bottom:1px solid;">Artikel<br><br></th>
                <th style="border-bottom:1px solid;">Beschreibung<br><br></th>
                <th style="border-bottom:1px solid;">Stückpreis<br><br></th>
                <th style="border-bottom:1px solid;">Preis<br><br></th>
            </tr>
            ';

            $price='';
            $strSQLo = "SELECT * FROM orders WHERE customer_id LIKE '$customer_id'";
            $rso=mysqli_query($link,$strSQLo);
            while($rowo=mysqli_fetch_assoc($rso))
            {
                $product_id=$rowo['product_id'];

                $strSQLp = "SELECT * FROM products WHERE id LIKE '$product_id'";
                $rsp=mysqli_query($link,$strSQLp);
                while($rowp=mysqli_fetch_assoc($rsp))
                {
                    if($rowp['box_set']=='set')
                    {
                        $set_type = 'WEPEN-Set';

                        if($rowp['amt_pen_b']==0 AND $rowp['amt_pen_r']==0) $product_nr='WECP-1026';
                        else if($rowp['amt_pen_b']==0 AND $rowp['amt_pencil']==0) $product_nr='WECP-1025';
                        else if($rowp['amt_pen_r']==0 AND $rowp['amt_pencil']==0) $product_nr='WECP-1024';
                        else if($rowp['amt_pen_b']==0) $product_nr='WECP-1022';
                        else if($rowp['amt_pen_r']==0) $product_nr='WECP-1021';
                        else if($rowp['amt_pencil']==0) $product_nr='WECP-1023';

                        $info='';
                        if($rowp['amt_box']!=0) $info .= $rowp['amt_box'].'x Etui<br>';
                        if($rowp['amt_pen_b']!=0) $info .= $rowp['amt_pen_b'].'x Kugelschreiber Blau<br>';
                        if($rowp['amt_pen_r']!=0) $info .= $rowp['amt_pen_r'].'x Kugelschreiber Rot<br>';
                        if($rowp['amt_pencil']!=0) $info .= $rowp['amt_pencil'].'x Druckbleistift<br>';
                    }
                    else if($rowp['box_set']=='box')
                    {
                        $set_type = 'WEPEN-Etui';

                        $product_nr = "WECP-1001";

                        $info='';
                        if($rowp['amt_box']!=0) $info .= $rowp['amt_box'].'x Etui<br>';
                    }
                    else if($rowp['box_set']=='pen')
                    {
                        $set_type = 'WEPEN-Stifte';

                        $product_nr = "WECP-1011";

                        $info='';
                        if($rowp['amt_pen_b']!=0) $info .= $rowp['amt_pen_b'].'x Kugelschreiber Blau<br>';
                        if($rowp['amt_pen_r']!=0) $info .= $rowp['amt_pen_r'].'x Kugelschreiber Rot<br>';
                        if($rowp['amt_pencil']!=0) $info .= $rowp['amt_pencil'].'x Druckbleistift<br>';
                    }
                    else if($rowp['box_set']=='discount')
                    {
                        $set_type = 'Gutschein';
                        $info='';
                        $price_extra=0;
                        $discount_code = $rowp['text1_t'];

                        $strSQLdis = "SELECT * FROM discount_codes WHERE code LIKE '$discount_code'";
                        $rsdis=mysqli_query($link,$strSQLdis);
                        while($rowdis=mysqli_fetch_assoc($rsdis))
                        {
                            $set_type = 'Gutschein';

                            if($rowdis['discount_type']=='percent')
                            {
                                $info = "Rabatt ".$rowdis['discount_amount']."%";
                                $price_extra = "-".$rowdis['discount_amount']."%";
                            }
                            if($rowdis['discount_type']=='absolute')
                            {
                                $info = "Rabatt ".$rowdis['discount_amount']."€";
                                $price_extra = "€ -".number_format($rowdis['discount_amount'],2);
                            }
                        }
                    }

                    $retval.= '
                        <tr>
                            <td style="border-bottom:1px solid;">'.$set_type.'</td>
                            <td style="border-bottom:1px solid;">'.$info.'</td>
                            ';

                                if($rowp['box_set']!='discount' AND $rowp['box_set']!='shipping') $retval.= '<td style="border-bottom:1px solid;text-align:right;">&euro; '.base_price(fetch("company_products","id","product_nr",$product_nr)).'</td>';
                                else $retval.= '<td style="border-bottom:1px solid;text-align:right;"></td>';

                                if($rowp['box_set']!='discount' AND $rowp['box_set']!='shipping') $retval.= '<td style="border-bottom:1px solid;text-align:right;">&euro; '.price_specific($product_id) .'</td>';
                                else $retval.= '<td style="border-bottom:1px solid;text-align:right;">'.$price_extra.'</td>';

                                $retval.= '
                        </tr>
                    ';
                }

            }

            $retval.= '
            <tr>
                <td style="text-align:right; padding-right:10px;" colspan=3>Gesamt:</td>
                <td style="border-bottom:3px double;text-align:right;"><b>&euro; '.total_price($customer_id).'</b></td>
            </tr>
        </table>


            </div>

                                      <div style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">
              <!--[if (mso)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px;padding-left: 10px; padding-top: 10px; padding-bottom: 10px;"><table width="100%" align="center" cellpadding="0" cellspacing="0" border="0"><tr><td><![endif]-->
              <div align="center"><div style="border-top: 1px solid #BBBBBB; width:100%; line-height:1px; height:1px; font-size:1px;">&#160;</div></div>
              <!--[if (mso)]></td></tr></table></td></tr></table><![endif]-->
            </div>



                                      <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;"><![endif]-->
            <div style="color:#555555;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif; padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">


            ';

            if($payment_method == 'cash')
            {
                $retval .= '
                <div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px;text-align: center"><span style="font-size: 22px; line-height: 26px;"><span style="line-height: 26px; font-size: 22px;"></span>Zahlung: Barzahlung</span><br></p></div>
                </div>
                <!--[if mso]></td></tr></table><![endif]-->
                <center>
                <table style="font-size:15pt;">
                  <tr>
                    <td style="text-align:right; padding-right:10px;"><b>Betrag:</b></td>
                    <td>€ '.$price_total.'</td>
                  </tr>
                </table>
                </center>
                </div>';
            }

            if($payment_method == 'transfer')
            {
                $retval .= '
                <div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px;text-align: center"><span style="font-size: 22px; line-height: 26px;"><span style="line-height: 26px; font-size: 22px;"></span>Zahlung: Überweisung</span><br></p></div>
                </div>
                <!--[if mso]></td></tr></table><![endif]-->
                <div style="font-size: 16px;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif; text-align: center;">
                <center>
                <table style="font-size:15pt;">
                    <tr>
                    <td style="text-align:right; padding-right:10px;"><b>Empfänger:</b></td>
                    <td>WEPEN</td>
                  </tr>
                  <tr>
                    <td style="text-align:right; padding-right:10px;"><b>Verwendungszweck:</b></td>
                    <td>'.$order_nr.'</td>
                  </tr>
                  <tr>
                    <td style="text-align:right; padding-right:10px;"><b>IBAN:</b></td>
                    <td>AT72 4480 0103 8802 0001</td>
                  </tr>
                  <tr>
                    <td style="text-align:right; padding-right:10px;"><b>BIC:</b></td>
                    <td>VBWEAT2WXXX</td>
                  </tr>
                  <tr>
                    <td style="text-align:right; padding-right:10px;"><b>Betrag:</b></td>
                    <td>€ '.$price_total.'</td>
                  </tr>
                </table>
                </center>
                </div>
                ';
            }

            $retval.= '
            <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;"><![endif]-->
            <div style="color:#555555;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif; padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">
            	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px;text-align: center">Sie werden benachrichtigt sobald Ihre Zahlung eingegangen ist.</p><p style="margin: 0;font-size: 14px;line-height: 17px;text-align: center">Bei weiteren Fragen wenden Sie sich an support@wepen.at&#160;<br></p><p style="margin: 0;font-size: 14px;line-height: 17px;text-align: right"><br></p></div>
            </div>
            <!--[if mso]></td></tr></table><![endif]-->




            <div align="center" class="button-container center" style="padding-right: 10px; padding-left: 10px; padding-top:10px; padding-bottom:10px;">
              <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-spacing: 0; border-collapse: collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top:10px; padding-bottom:10px;" align="center"><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://wepen.at" style="height:42px; v-text-anchor:middle; width:198px;" arcsize="10%" strokecolor="#3AAEE0" fillcolor="#3AAEE0"><w:anchorlock/><center style="color:#ffffff; font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif; font-size:16px;"><![endif]-->
                <a href="http://wepen.at" target="_blank" style="display: block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;color: #ffffff; background-color: #3AAEE0; border-radius: 4px; -webkit-border-radius: 4px; -moz-border-radius: 4px; max-width: 198px; width: 158px;width: auto; border-top: 0px solid transparent; border-right: 0px solid transparent; border-bottom: 0px solid transparent; border-left: 0px solid transparent; padding-top: 5px; padding-right: 20px; padding-bottom: 5px; padding-left: 20px; font-family: Arial, \'Helvetica Neue\', Helvetica, sans-serif;mso-border-alt: none">
                  <span style="font-size:16px;line-height:32px;">WEPEN - Homepage</span>
                </a>
              <!--[if mso]></center></v:roundrect></td></tr></table><![endif]-->
            </div>




            <div align="center" class="button-container center" style="padding-right: 10px; padding-left: 10px; padding-top:10px; padding-bottom:10px;">
              <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-spacing: 0; border-collapse: collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top:10px; padding-bottom:10px;" align="center"><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://designer.wepen.at" style="height:42px; v-text-anchor:middle; width:174px;" arcsize="10%" strokecolor="#3AAEE0" fillcolor="#3AAEE0"><w:anchorlock/><center style="color:#ffffff; font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif; font-size:16px;"><![endif]-->
                <a href="http://designer.wepen.at" target="_blank" style="display: block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;color: #ffffff; background-color: #3AAEE0; border-radius: 4px; -webkit-border-radius: 4px; -moz-border-radius: 4px; max-width: 174px; width: 134px;width: auto; border-top: 0px solid transparent; border-right: 0px solid transparent; border-bottom: 0px solid transparent; border-left: 0px solid transparent; padding-top: 5px; padding-right: 20px; padding-bottom: 5px; padding-left: 20px; font-family: Arial, \'Helvetica Neue\', Helvetica, sans-serif;mso-border-alt: none">
                  <span style="font-size:16px;line-height:32px;">WEPEN Designer</span>
                </a>
              <!--[if mso]></center></v:roundrect></td></tr></table><![endif]-->
            </div>



                                      <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;"><![endif]-->
            <div style="color:#555555;line-height:120%;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif; padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">
            	<div style="font-size:12px;line-height:14px;color:#555555;font-family:Arial, \'Helvetica Neue\', Helvetica, sans-serif;text-align:left;"><p style="margin: 0;font-size: 14px;line-height: 17px;text-align: right"><span style="font-size: 9px; line-height: 10px;">Copyright 2017 Tobias Hattinger MOST RIGHTS RESERVED</span></p></div>
            </div>
            <!--[if mso]></td></tr></table><![endif]-->


                                <!--[if (!mso)]><!--><!--[if (!IE)]><!--></div><!--<![endif]--><!--<![endif]-->
                                </div>
                              </div>
                            <!--[if (mso)]></td></tr/></table></td></tr></table></td></tr></table><![endif]-->
                            <!--[if (IE)]></td></tr/></table></td></tr></table></td></tr></table><![endif]-->
                          </div>
                        </div>
                      </div>         <!--[if (mso)]></td></tr></table><![endif]-->
                     <!--[if (IE)]></td></tr></table><![endif]-->
                   </td>
                 </tr>
                </tbody>
              </table>
              <!--[if (mso)]></div><![endif]-->
              <!--[if (IE)]></div><![endif]-->


            </body></html>
        ';

        return $retval;
    }

    function langlib($keyword)
    {
        include("mysql_connect.php");   
        $retval=fetch("language_lib",$_SESSION['language'],"keyword",$keyword);
        if($retval!='') return $retval;
        else
        {
            if(fetch_count(language_lib,"keyword",$keyword)==0)
            {
                $strSQL = "INSERT INTO language_lib (keyword,DE,EN) VALUES ('$keyword','$keyword','')";
                $rs=mysqli_query($link,$strSQL);
            }
            return $keyword;
        }
    }
?>