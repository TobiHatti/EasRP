<?php
    session_start();
    require('data/functions.php');
    require('data/fpdf/main_functions.php');
    require('data/mysql_connect.php');
    require('data/barcode/vendor/autoload.php');

    $priceB = 0;
    $priceN = 0;
    $order_number = $_GET['orderNumber'];

    // Instanciation of inherited class
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',12);
    define('EURO',chr(128));

    // Anschrift
    $strSQL = "SELECT * FROM orders
    INNER JOIN customer_order ON orders.order_number = customer_order.order_number
    INNER JOIN customers ON customers.customer_number = customer_order.customer_number
    WHERE orders.order_number = '$order_number'";
    $rs=mysqli_query($link,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        $pdf->Cell(90,6,LetterCorrection(SalutationCode($row['salutation'])),0,1,'L');

        $pdf->Cell(90,6,LetterCorrection((($row['title']!='') ? ($row['title'].' ') : '').$row['first_name'].' '.$row['last_name']),0,0,'L');
            $pdf->Cell(45,6,"Bestelldatum:",0,0,'R');
            $pdf->Cell(45,6,$row['order_date'],0,1,'R');
        $pdf->Cell(90,6,LetterCorrection($row['adressline1']),0,0,'L');
            $pdf->Cell(45,6,"Bestellnummer:",0,0,'R');
            $pdf->Cell(45,6,$row['order_number'],0,1,'R');
        $pdf->Cell(90,6,LetterCorrection((($row['adressline2']=='') ? ($row['zip'].' '.$row['city']) : $row['adressline2'])),0,0,'L');
            $pdf->Cell(45,6,"Kundennummer:",0,0,'R');
            $pdf->Cell(45,6,$row['customer_number'],0,1,'R');
        $pdf->Cell(90,6,LetterCorrection((($row['adressline2']=='') ? $row['country'] : ($row['zip'].' '.$row['city']))),0,1,'L');

        $pdf->Cell(90,6,LetterCorrection((($row['adressline2']!='') ? $row['country'] : '' )),0,1,'L');
        $pdf->Ln();
        $pdf->Cell(90,6,$row['email'],0,0,'L');

    }

    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();

    $pdf->SetFont('Arial','',20);
    $pdf->Cell(20,6,LetterCorrection("AUFTRAGSBESTÄTIGUNG"),0,1);
    $pdf->Ln();

    $pdf->SetFont('Arial','',12);
    $msg = "
    Sehr geehrter Herr Hattinger,<br>
    Ihre Bestellung mit der Bestellnummer $order_number ist bei uns eingegangen<br><br>";
    $pdf->WriteHTML(LetterCorrection($msg));

    // Beginn of Table

    // Table Header
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(20,6,"Menge:",1,0);
    $pdf->Cell(40,6,"Artikelnummer:",1,0);
    $pdf->Cell(60,6,"Bezeichnung:",1,0);
    $pdf->Cell(30,6,"Einzelpreis:",1,0);
    $pdf->Cell(30,6,"Summe:",1,1);

    // Table Content
    $pdf->SetFont('Arial','',12);

    $strSQL = "SELECT * FROM order_contains WHERE order_number = '$order_number'";
    $rs=mysqli_query($link,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        $pdf->Cell(20,6,$row['quantity'],1,0);
        $pdf->Cell(40,6,$row['product_number'],1,0);
        $pdf->Cell(60,6,fetch("products","name","number",$row['product_number']),1,0);
        $pdf->Cell(30,6,EURO.' '.number_format(fetch("products","resell_price","number",$row['product_number']),2),1,0,'R');
        $pdf->Cell(30,6,EURO.' '.number_format(fetch("products","resell_price","number",$row['product_number'])*$row['quantity'],2),1,1);
        $priceB += fetch("products","resell_price","number",$row['product_number'])*$row['quantity'];
    }
    $pdf->Cell(180,0.3,"",1,1);

    $priceN = $priceB/ (1 + (GetProperty("vat_amount")/100));
    $tax = $priceB - $priceN;

    if($_GET['ship']==1)
    {
        $pdf->Cell(150,6,"Versandkosten:",0,0,'R');
        $pdf->Cell(30,6,"TODO",1,1);
    }
    if(GetProperty("vat_show"))
    {
        $pdf->Cell(150,6,"Warenwert Netto:",0,0,'R');
        $pdf->Cell(30,6,EURO.' '.number_format($priceN,2),1,1);
        $pdf->Cell(150,6,"Enthaltene MwSt:",0,0,'R');
        $pdf->Cell(30,6,EURO.' '.number_format($tax,2),1,1);
    }

    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(150,6,"Rechnungssumme:",0,0,'R');
    $pdf->Cell(30,6,EURO.' '.number_format(((GetProperty("vat_add")) ? $priceB : $priceN),2),1,1);

    $pdf->Ln();
    $pdf->Ln();
    $pdf->SetFont('Arial','',12);
    $msg = "
    Bitte beachten Sie, dass dieses Schreiben eine automatisch versendete E-Mail darstellt.<br>
    Damit bestätigen wir lediglich den Erhalt Ihrer Bestellung. Den Kaufvertrag mit Ihrer<br>
    Rechnung erhalten Sie sobald ihre Bestellung bearbeitet wurde.<br>
    <br>
    Ihre gewählte Zahlungsart: <b>".PaymentCode($_GET['payment'])."</b><br>
    <br>
    Vielen Dank für Ihre Bestellung!<br>
    Mit freundlichen Grüßen,<br>
    <i>".GetProperty("company_name")."</i>";
    $pdf->WriteHTML(LetterCorrection($msg));

    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG(); 
    file_put_contents('files/barcodes/BC-'.$order_number.'.png', base64_decode(base64_encode($generator->getBarcode($order_number, $generator::TYPE_CODE_128))));
    $pdf->Image('files/barcodes/BC-'.$order_number.'.png',65,265,80);




    $pdf->Output('F',"files/customers/orderConfirmations/orderConfirmation_".$order_number.".pdf");
    $pdf->Output();


?>