<?php
    session_start();
    require('data/functions.php');
    $_SESSION['verwendungszweck'] = fetch("customers","order_nr","id",$_GET['selected']);

    require('data/fpdf/main_functions.php');

    require('data/mysql_connect.php');

    if(isset($_GET['offer']))
    {
        // Instanciation of inherited class
        $pdf = new PDF();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial','',12);

        define('EURO',chr(128));

        // Adress / Contact
        $pdf->Ln();
        $pdf->Cell(130,6,letter_correction($_GET['title']),0,0);
        $pdf->Cell(50,6,'Angebotsdatum:',0,0,'L');
        $pdf->Cell(10,6,date("d.m.Y"),0,0,'R');

        $pdf->Ln();
        $pdf->Cell(1,6,letter_correction($_GET['first']).' '.letter_correction($_GET['last']),0,0);

        $pdf->Ln();
        $pdf->Cell(1,6,letter_correction($_GET['street']),0,0);

        $pdf->Ln();
        $pdf->Cell(1,6,letter_correction($_GET['plz']).' '.letter_correction($_GET['city']),0,0);

        $pdf->Ln();
        $pdf->Cell(1,6,letter_correction($_GET['country']),0,0);

        //Title
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFont('Helvetica','',20);
        $pdf->Cell(80);
        $pdf->Cell(30,15,$_GET['heading'],0,1,'C');
        $pdf->Cell(190,0.01,"",1,1);
        $pdf->Cell(190,1,"",0,1);

        //Content
        $pdf->Ln();
        $pdf->SetFont('helvetica','',12);
        $pdf->WriteHTML(nl2br($_SESSION['offer_content']));

        //Table
        $pdf->Ln();
        $pdf->Cell(190,0.01,"",1,1,'C');

        $pdf->Cell(0.05,10,"",1,0);
        $pdf->Cell(24.95,10,"Art. Nr.",0,0);
        $pdf->Cell(82.5,10,"Beschreibung",0,0);
        $pdf->Cell(27.5,10,"Anz.",0,0);
        $pdf->Cell(10,10,"б",0,0,"R");
        $pdf->Cell(45,10,"Euro inkl. MWSt.",0,0,"R");
        $pdf->Cell(0.05,10,"",1,1);

        $pdf->Cell(190,0.01,"",1,1,'C');

        // Put this in a loop for orders ===================

        $product_parts = explode("|",$_GET['selected_items']);

        $price_total = 0;

        foreach($product_parts as $products)
        {
            $prod_part = explode("-",$products);

            $price_total = $price_total + (graduated_price_at_amount($prod_part[0],$prod_part[1]) * $prod_part[1]);

            $pdf->Cell(0.05,10,"",1,0);
            $pdf->Cell(24.95,10,fetch("company_products","product_nr","id",$prod_part[0]),0,0);
            $pdf->Cell(82.5,10,fetch("company_products","description","id",$prod_part[0]),0,0);
            $pdf->Cell(27.5,10,$prod_part[1]." Stk.",0,0);

            $pdf->Cell(15,7,number_format(graduated_price_at_amount($prod_part[0],$prod_part[1]),2),0,0,"R");
            $pdf->Cell(39.95,7,number_format((graduated_price_at_amount($prod_part[0],$prod_part[1]) * $prod_part[1]),2),0,0,"R");

            $pdf->Cell(0.05,10,"",1,1);
        }

        // =================================================


        $pdf->Cell(35,10,"Junior USt-Satz",1,0,'L');
        $pdf->Cell(45,10,"Warenwert-Netto",1,0,'R');
        $pdf->Cell(55,10,"Junior USt-Betrag",1,0,'R');
        $pdf->Cell(55,10,"Summe Euro",1,1,'R');

        $pdf->Cell(35,10,"20 %",1,0,'L');
        $pdf->Cell(45,10,EURO. number_format($price_total / 1.2,2) ,1,0,'R');
        $pdf->Cell(55,10,EURO.number_format(($price_total - ($price_total / 1.2)),2) ,1,0,'R');
        $pdf->Cell(55,10,EURO.number_format($price_total,2),1,1,'R');

        $pdf->Output();
    }

    if(isset($_GET['bill']) AND isset($_POST['create_pdf']))
    {
        // Instanciation of inherited class
        $pdf = new PDF();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial','',12);

        define('EURO',chr(128));



        // Adress / Contact
        $pdf->Ln();
        $pdf->Cell(130,6,letter_correction(fetch("customers","gender","id",$_GET['selected'])),0,0);
        $pdf->Cell(50,6,'Rechnungsdatum:',0,0,'L');
        $pdf->Cell(10,6,date("d.m.Y"),0,0,'R');

        $pdf->Ln();
        if(fetch("customers","title","id",$_GET['selected'])!='') $pdf->Cell(130,6,letter_correction(fetch("customers","title","id",$_GET['selected'])).' '.letter_correction(fetch("customers","first_name","id",$_GET['selected'])).' '.letter_correction(fetch("customers","last_name","id",$_GET['selected'])),0,0);
        else $pdf->Cell(130,6,letter_correction(fetch("customers","first_name","id",$_GET['selected'])).' '.letter_correction(fetch("customers","last_name","id",$_GET['selected'])),0,0);
        $pdf->Cell(50,6,'Bestelldatum: ',0,0,'L');
        $pdf->Cell(10,6, date("d.m.Y",strtotime(fetch("customers","order_date","id",$_GET['selected']))),0,0,'R');

        $pdf->Ln();
        $pdf->Cell(130,6,letter_correction(fetch("customers","street","id",$_GET['selected'])).' '.letter_correction(fetch("customers","street_nr","id",$_GET['selected'])),0,0);
        $pdf->Cell(50,6,'Kundennummer: ',0,0,'L');
        $pdf->Cell(10,6,substr(fetch("customers","customer_number","id",$_GET['selected']),0,6),0,0,'R');

        $pdf->Ln();
        $pdf->Cell(1,6,letter_correction(fetch("customers","plz","id",$_GET['selected'])).' '.letter_correction(fetch("customers","city","id",$_GET['selected'])),0,0);

        $pdf->Ln();
        $pdf->Cell(1,6,letter_correction(fetch("customers","country","id",$_GET['selected'])),0,0);

        //Title
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFont('Helvetica','',14);
        $pdf->Cell(80);
        $pdf->Cell(30,15,"JUNIOR-Rechnung-Nr.: ".fetch("customers","order_nr","id",$_GET['selected']),0,1,'R');
        $pdf->Cell(190,0.01,"",1,1);
        $pdf->Cell(190,1,"",0,1);

        //Content
        $pdf->Ln();
        $pdf->SetFont('helvetica','',12);
        $pdf->WriteHTML(nl2br(letter_correction($_POST['content'])));

        //Table
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(190,0.01,"",1,1,'C');

        $pdf->Cell(0.05,10,"",1,0);
        $pdf->Cell(24.95,10,"Art. Nr.",0,0);
        $pdf->Cell(82.5,10,"Beschreibung",0,0);
        $pdf->Cell(27.5,10,"Anz.",0,0);
        $pdf->Cell(10,10,"а",0,0,"R");
        $pdf->Cell(45,10,"Euro inkl. MWSt.",0,0,"R");
        $pdf->Cell(0.05,10,"",1,1);

        // Put this in a loop for orders ===================

        $customer_id = $_GET['selected'];
        $price_total = 0;

        $strSQLo = "SELECT * FROM orders WHERE customer_id LIKE '$customer_id'";
        $rso=mysqli_query($link,$strSQLo);
        while($rowo=mysqli_fetch_assoc($rso))
        {
            $product_id = $rowo['product_id'];
            $strSQL = "SELECT * FROM products WHERE id LIKE '$product_id'";
            $rs=mysqli_query($link,$strSQL);
            while($row=mysqli_fetch_assoc($rs))
            {
                $pdf->Cell(190,0.01,"",1,1,'C');

                if($row['box_set']=='set')
                {
                    if($row['amt_pen_b']==0 AND $row['amt_pen_r']==0) $product_nr='WECP-1026';
                    else if($row['amt_pen_b']==0 AND $row['amt_pencil']==0) $product_nr='WECP-1025';
                    else if($row['amt_pen_r']==0 AND $row['amt_pencil']==0) $product_nr='WECP-1024';
                    else if($row['amt_pen_b']==0) $product_nr='WECP-1022';
                    else if($row['amt_pen_r']==0) $product_nr='WECP-1021';
                    else if($row['amt_pencil']==0) $product_nr='WECP-1023';

                    //$price_total += (fetch("company_products","price","product_nr",$product_nr) * $row['amt_box']);
                    $price_total += price_specific($row['id']);

                    $set_type='Set:';

                    $price_a = EURO.number_format(graduated_price_at_amount(fetch("company_products","id","product_nr",$product_nr),$row['amt_box']),2);
                    $price = EURO.number_format(graduated_price_at_amount(fetch("company_products","id","product_nr",$product_nr),$row['amt_box'])*$row['amt_box'],2);
                    if($row['amt_box']!=0)
                    {
                        $pdf->Cell(0.05,7,"",1,0);
                        $pdf->Cell(29.95,7,$product_nr,0,0);
                        $pdf->Cell(15,7,$set_type,0,0);
                        $pdf->Cell(62.5,7,"Etui",0,0);
                        $pdf->Cell(27.5,7,$row['amt_box']." Stk.",0,0);
                        $pdf->Cell(15,7,$price_a,0,0,"R");
                        $pdf->Cell(39.95,7,$price,0,0,"R");
                        $pdf->Cell(0.05,7,"",1,1);

                        $product_nr='';
                        $set_type='';
                        $price = '';
                        $price_a = '';
                    }

                    if($row['amt_pen_b']!=0)
                    {
                        $pdf->Cell(0.05,7,"",1,0);
                        $pdf->Cell(29.95,7,$product_nr,0,0);
                        $pdf->Cell(15,7,$set_type,0,0);
                        $pdf->Cell(62.5,7,"Kugelschreiber Blau ",0,0);
                        $pdf->Cell(27.5,7,$row['amt_pen_b']." Stk.",0,0);
                        $pdf->Cell(15,7,$price_a,0,0,"R");
                        $pdf->Cell(39.95,7,$price,0,0,"R");
                        $pdf->Cell(0.05,7,"",1,1);

                        $product_nr='';
                        $set_type='';
                        $price = '';
                        $price_a = '';
                    }
                    if($row['amt_pen_r']!=0)
                    {
                        $pdf->Cell(0.05,7,"",1,0);
                        $pdf->Cell(29.95,7,$product_nr,0,0);
                        $pdf->Cell(15,7,$set_type,0,0);
                        $pdf->Cell(62.5,7,"Kugelschreiber Rot ",0,0);
                        $pdf->Cell(27.5,7,$row['amt_pen_r']." Stk.",0,0);
                        $pdf->Cell(15,7,$price_a,0,0,"R");
                        $pdf->Cell(39.95,7,$price,0,0,"R");
                        $pdf->Cell(0.05,7,"",1,1);

                        $product_nr='';
                        $set_type='';
                        $price = '';
                        $price_a = '';
                    }
                    if($row['amt_pencil']!=0)
                    {
                        $pdf->Cell(0.05,7,"",1,0);
                        $pdf->Cell(29.95,7,$product_nr,0,0);
                        $pdf->Cell(15,7,$set_type,0,0);
                        $pdf->Cell(62.5,7,"Druckbleistift ",0,0);
                        $pdf->Cell(27.5,7,$row['amt_pencil']." Stk.",0,0);
                        $pdf->Cell(15,7,$price_a,0,0,"R");
                        $pdf->Cell(39.95,7,$price,0,0,"R");
                        $pdf->Cell(0.05,7,"",1,1);

                        $product_nr='';
                        $set_type='';
                        $price = '';
                        $price_a = '';
                    }
                }

                else if($row['box_set']=='pen')
                {
                    $set_type='Stift:';

                    if($row['amt_pen_b']!=0)
                    {
                        $price_total += price_specific($row['id']);;

                        $pdf->Cell(190,0.01,"",1,1,'C');
                        $pdf->Cell(0.05,7,"",1,0);
                        $pdf->Cell(29.95,7,"WECP-1011",0,0);
                        $pdf->Cell(15,7,$set_type,0,0);
                        $pdf->Cell(62.5,7,"Kugelschreiber Blau ",0,0);
                        $pdf->Cell(27.5,7,$row['amt_pen_b']." Stk.",0,0);
                        $pdf->Cell(54.95,7,EURO.number_format(base_price(fetch("company_products","id","product_nr","WECP-1011")),2),0,0,"R");
                        $pdf->Cell(0.05,7,"",1,1);
                    }
                    if($row['amt_pen_r']!=0)
                    {
                        $price_total += price_specific($row['id']);;

                        $pdf->Cell(190,0.01,"",1,1,'C');
                        $pdf->Cell(0.05,7,"",1,0);
                        $pdf->Cell(29.95,7,"WECP-1012",0,0);
                        $pdf->Cell(15,7,$set_type,0,0);
                        $pdf->Cell(62.5,7,"Kugelschreiber Rot ",0,0);
                        $pdf->Cell(27.5,7,$row['amt_pen_r']." Stk.",0,0);
                        $pdf->Cell(54.95,7,EURO.number_format(base_price(fetch("company_products","id","product_nr","WECP-1012")),2),0,0,"R");
                        $pdf->Cell(0.05,7,"",1,1);
                    }
                    if($row['amt_pencil']!=0)
                    {
                        $price_total += price_specific($row['id']);;

                        $pdf->Cell(190,0.01,"",1,1,'C');
                        $pdf->Cell(0.05,7,"",1,0);
                        $pdf->Cell(29.95,7,"WECP-1013",0,0);
                        $pdf->Cell(15,7,$set_type,0,0);
                        $pdf->Cell(62.5,7,"Druckbleistift ",0,0);
                        $pdf->Cell(27.5,7,$row['amt_pencil']." Stk.",0,0);
                        $pdf->Cell(54.95,7,EURO.number_format(base_price(fetch("company_products","id","product_nr","WECP-1013")),2),0,0,"R");
                        $pdf->Cell(0.05,7,"",1,1);
                    }
                }

                else if($row['box_set']=='box')
                {
                    $price_total += price_specific($row['id']);;

                    $set_type='Etui:';
                    if($row['amt_box']!=0)
                    {
                        $pdf->Cell(0.05,7,"",1,0);
                        $pdf->Cell(29.95,7,"WECP-1001",0,0);
                        $pdf->Cell(15,7,$set_type,0,0);
                        $pdf->Cell(62.5,7,"Etui",0,0);
                        $pdf->Cell(27.5,7,$row['amt_box']." Stk.",0,0);
                        $pdf->Cell(54.95,7,EURO.number_format(base_price(fetch("company_products","id","product_nr","WECP-1001")),2),0,0,"R");
                        $pdf->Cell(0.05,7,"",1,1);

                        $set_type='';
                        $price = '';
                    }
                }
                else if($row['box_set']=='coupon')
                {
                    $set_type = 'Gutschein';

                    if($row['text2_t']=='set')
                    {
                        $info = "Gutschein fьr 1 WEPEN-Set (".$row['text1_t'].")";
                        $price_extra = 25.9;
                    }
                    if($row['text2_t']=='box')
                    {
                        $info = "Gutschein fьr 1 WEPEN-Etui (".$row['text1_t'].")";
                        $price_extra = 16.9;
                    }
                    if($row['text2_t']=='pen')
                    {
                        $info = "Gutschein fьr einen WEPEN-Stift (".$row['text1_t'].")";
                        $price_extra = 7;
                    }

                    $pdf->Cell(0.05,7,$set_type,1,0);
                    $pdf->Cell(29.95,7,"",0,0);
                    $pdf->Cell(15,7,"",0,0);
                    $pdf->Cell(62.5,7,$info,0,0);
                    $pdf->Cell(27.5,7,"",0,0);
                    $pdf->Cell(54.95,7,$price_extra,0,0,"R");
                    $pdf->Cell(0.05,7,"",1,1);
                }
                else if($row['box_set']=='discount')
                {
                    $set_type = 'Gutschein';
                    $discount_code = $row['text1_t'];

                    $strSQLdis = "SELECT * FROM discount_codes WHERE code LIKE '$discount_code'";
                    $rsdis=mysqli_query($link,$strSQLdis);
                    while($rowdis=mysqli_fetch_assoc($rsdis))
                    {
                        if($rowdis['discount_type']=='percent')
                        {
                            $info = "Rabatt ".$rowdis['discount_amount']."%";
                            $price_extra = "-".$rowdis['discount_amount']."%";
                        }
                        if($rowdis['discount_type']=='absolute')
                        {
                            $info = "Rabatt ".$rowdis['discount_amount']."А";
                            $price_extra = "А -".number_format($rowdis['discount_amount'],2);
                        }
                    }

                    $pdf->Cell(0.05,7,"Gutschein",1,0);
                    $pdf->Cell(29.95,7,"",0,0);
                    $pdf->Cell(15,7,"",0,0);
                    $pdf->Cell(62.5,7,$info,0,0);
                    $pdf->Cell(27.5,7,"",0,0);
                    $pdf->Cell(54.95,7,$price_extra,0,0,"R");
                    $pdf->Cell(0.05,7,"",1,1);
                }
                else if($row['box_set']=='shipping')
                {
                    $set_type = 'Versand';
                    $shipping_nr = $row['text1_t'];

                    $strSQLdis = "SELECT * FROM shipping WHERE product_nr LIKE '$shipping_nr'";
                    $rsdis=mysqli_query($link,$strSQLdis);
                    while($rowdis=mysqli_fetch_assoc($rsdis))
                    {
                        $info = $rowdis['description'];
                        $price_extra = "А ".number_format($rowdis['price'],2);
                    }



                    $pdf->Cell(0.05,7,$set_type,1,0);
                    $pdf->Cell(29.95,7,"",0,0);
                    $pdf->Cell(15,7,"",0,0);
                    $pdf->Cell(62.5,7,$info,0,0);
                    $pdf->Cell(27.5,7,"",0,0);
                    $pdf->Cell(54.95,7,$price_extra,0,0,"R");
                    $pdf->Cell(0.05,7,"",1,1);

                }
                else
                {
                    /*
                    $set_type='Versand: Paket ьber 2kg';
                    $price_a = EURO.number_format(graduated_price_at_amount(fetch("company_products","id","product_nr","WECP-2001"),1),2);
                    $price = EURO.number_format(graduated_price_at_amount(fetch("company_products","id","product_nr","WECP-2001"),1),2);

                    $pdf->Cell(0.05,7,"",1,0);
                    $pdf->Cell(29.95,7,"WECP-2001",0,0);
                    $pdf->Cell(15,7,$set_type,0,0);
                    $pdf->Cell(62.5,7,"",0,0);
                    $pdf->Cell(27.5,7,"1 Stk.",0,0);

                    $pdf->Cell(15,7,$price_a,0,0,"R");
                    $pdf->Cell(39.95,7,$price,0,0,"R");

                    $pdf->Cell(0.05,7,"",1,1);

                    $set_type='';
                    $price = '';
                    */
                }

            }
        }

        // =================================================


        $pdf->Cell(35,10,"Junior USt-Satz",1,0,'L');
        $pdf->Cell(45,10,"Warenwert-Netto",1,0,'R');
        $pdf->Cell(55,10,"Junior USt-Betrag",1,0,'R');
        $pdf->Cell(55,10,"Summe Euro",1,1,'R');

        $pdf->Cell(35,10,"20 %",1,0,'L');
        $pdf->Cell(45,10,EURO.number_format(total_price($customer_id) /1.2,2),1,0,'R');
        $pdf->Cell(55,10,EURO.number_format((total_price($customer_id) - (total_price($customer_id) / 1.2)),2),1,0,'R');
        $pdf->Cell(55,10,EURO.number_format(total_price($customer_id),2),1,1,'R');

        /*
        $pdf->Cell(135,10,"Zahlung ",1,0,'L');
        $pdf->Cell(27.5,10,"Brutto",1,0,'L');
        $pdf->Cell(27.5,10,"Rest EUR",1,1,'L');

        $pdf->Cell(135,10,"  ",1,0,'L');
        $pdf->Cell(27.5,10," ",1,0,'L');
        $pdf->Cell(27.5,10,EURO.number_format(0,2),1,1,'R');
        */


        $pdf->Ln();
        $pdf->Ln();

        if(isset($_POST['payed'])) $pdf->Cell(20,10,"Die Auftragssumme wurde bereits bezahlt.",0,1,'L');
        else $pdf->Cell(20,10,"Es wird darum gebeten die Auftragssumme innerhalb der nдchsten 14 Tage zu ьberweisen.",0,1,'L');

        $pdf->Ln();

        $pdf->Cell(20,10,"Wir danken fьr Ihren Auftrag und verbleiben mit freundlichen Grьяen.",0,1,'L');

        $pdf->Ln();

// =========================================================
// =========================================================
// =======  Order confirmation for felix that trottl
// =========================================================
// =========================================================

        $pdf->AddPage();

        $pdf->Ln();
        $pdf->SetFont('Helvetica','',30);
        $pdf->Cell(80);
        $pdf->Cell(30,15,fetch("customers","order_nr","id",$_GET['selected']),0,1,'C');

        $pdf->SetFont('Helvetica','',12);
        $pdf->Ln();
        $pdf->Cell(130,6,letter_correction(fetch("customers","gender","id",$_GET['selected'])),0,0);
        $pdf->Cell(50,6,'Rechnungsdatum:',0,0,'L');
        $pdf->Cell(10,6,date("d.m.Y"),0,0,'R');

        $pdf->Ln();
        if(fetch("customers","title","id",$_GET['selected'])!='') $pdf->Cell(130,6,letter_correction(fetch("customers","title","id",$_GET['selected'])).' '.letter_correction(fetch("customers","first_name","id",$_GET['selected'])).' '.letter_correction(fetch("customers","last_name","id",$_GET['selected'])),0,0);
        else $pdf->Cell(130,6,letter_correction(fetch("customers","first_name","id",$_GET['selected'])).' '.letter_correction(fetch("customers","last_name","id",$_GET['selected'])),0,0);
        $pdf->Cell(50,6,'Bestelldatum: ',0,0,'L');
        $pdf->Cell(10,6, date("d.m.Y",strtotime(fetch("customers","order_date","id",$_GET['selected']))),0,0,'R');

        $pdf->Ln();
        $pdf->Cell(130,6,letter_correction(fetch("customers","street","id",$_GET['selected'])).' '.letter_correction(fetch("customers","street_nr","id",$_GET['selected'])),0,0);
        $pdf->Cell(50,6,'Kundennummer: ',0,0,'L');
        $pdf->Cell(10,6,substr(fetch("customers","customer_number","id",$_GET['selected']),0,6),0,0,'R');

        $pdf->Ln();
        $pdf->Cell(1,6,letter_correction(fetch("customers","plz","id",$_GET['selected'])).' '.letter_correction(fetch("customers","city","id",$_GET['selected'])),0,0);

        $pdf->Ln();
        $pdf->Cell(1,6,letter_correction(fetch("customers","country","id",$_GET['selected'])),0,0);

        $pdf->SetFont('Helvetica','',16);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(1,6,"Betrag: ".EURO.number_format(total_price($customer_id),2),0,0);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(1,6,"Betrag erhalten: ______________".EURO,0,0);

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(95);
        $pdf->Cell(1,6,"___________________                              __________________",0,0,'C');
        $pdf->SetFont('Helvetica','',10);
        $pdf->Ln();
        $pdf->Cell(95);
        $pdf->Cell(1,6,"Unterschrift Geld erhalten                                                                                                      Felix Lerchner",0,0,'C');

        $pdf->Output();
    }
?>