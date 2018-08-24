<?php
    session_start();
    require('data/functions.php');
    require('data/fpdf/main_functions.php');
    require('data/mysql_connect.php');
    require('data/barcode/vendor/autoload.php');

    $priceB = 0;
    $priceN = 0;
    $product_number = $_GET['productNumber'];

    // Instanciation of inherited class
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',12);
    define('EURO',chr(128));

    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();

    $pdf->SetFont('Arial','',20);
    $pdf->Cell(20,6,LetterCorrection("BESTELLANTRAG"),0,1);
    $pdf->Ln();

    $pdf->SetFont('Arial','',12);
    $msg = "Nachfolgend ist der Bestellantrag für das Produkt<br>
    <b>$product_number - ".fetch("products","name","number",$product_number)."</b><br>
    <br>
    Bei gelegenheit dieses Dokument erweitern.<br>
    Dieses Dokument dient rein zur Dokumentation.
    <br>";
    $pdf->WriteHTML(LetterCorrection($msg));


    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
    file_put_contents('files/barcodes/BC-'.$product_number.'.png', base64_decode(base64_encode($generator->getBarcode($product_number, $generator::TYPE_CODE_128))));
    $pdf->Image('files/barcodes/BC-'.$product_number.'.png',63,265,80);

    //$pdf->Output('F',"files/forms/reorderForm/RF-".$product_number."-D".date("YmdHi").".pdf");
    $pdf->Output();


?>