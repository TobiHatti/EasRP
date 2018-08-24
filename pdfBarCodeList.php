<?php
    session_start();
    require('data/functions.php');
    require('data/fpdf/main_functions.php');
    require('data/mysql_connect.php');
    require('data/barcode/vendor/autoload.php');


    // Instanciation of inherited class
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',12);
    define('EURO',chr(128));

    $x=13;
    $xoffset=60;
    $y=42.5;
    $s=50;
    $i=0;
    $yoffset=15.99;

    $cell_height=4;
    $cell_spacing=8;

    $showBorders=0;


    $page_nr = 2;

    $pagenrOffset = 45*($page_nr-1);

    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
    file_put_contents('files/barcodes/BC-HLEM-HATT01.png', base64_decode(base64_encode($generator->getBarcode("HLEM-HATT01", $generator::TYPE_CODE_128))));
    $pdf->Image('files/barcodes/BC-HLEM-HATT01.png',$x+(2*$xoffset),$y+($i*$yoffset),$s);
      /*
    $strSQL = "SELECT * FROM products WHERE number LIKE 'HLRW%' LIMIT $pagenrOffset,45";
    $rs=mysqli_query($link,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        $n1 = $row['number'];
        $row=mysqli_fetch_assoc($rs);
        $n2 = $row['number'];
        $row=mysqli_fetch_assoc($rs);
        $n3 = $row['number'];

        $pdf->SetFont('Arial','',8);
        $pdf->Cell($xoffset,$cell_height,$n1,$showBorders,0,'C');
        $pdf->Cell($xoffset,$cell_height,$n2,$showBorders,0,'C');
        $pdf->Cell($xoffset,$cell_height,$n3,$showBorders,1,'C');

        $pdf->SetFont('Arial','',6);
        $pdf->Cell($xoffset,$cell_height,LetterCorrection(fetch("products","name","number",$n1)),$showBorders,0,'C');
        $pdf->Cell($xoffset,$cell_height,LetterCorrection(fetch("products","name","number",$n2)),$showBorders,0,'C');
        $pdf->Cell($xoffset,$cell_height,LetterCorrection(fetch("products","name","number",$n3)),$showBorders,1,'C');

        $pdf->Cell($xoffset,$cell_spacing,"",$showBorders,0,'C');
        $pdf->Cell($xoffset,$cell_spacing,"",$showBorders,0,'C');
        $pdf->Cell($xoffset,$cell_spacing,"",$showBorders,1,'C');

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        file_put_contents('files/barcodes/BC-'.$n1.'.png', base64_decode(base64_encode($generator->getBarcode($n1, $generator::TYPE_CODE_128))));
        $pdf->Image('files/barcodes/BC-'.$n1.'.png',$x+(0*$xoffset),$y+($i*$yoffset),$s);

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        file_put_contents('files/barcodes/BC-'.$n2.'.png', base64_decode(base64_encode($generator->getBarcode($n2, $generator::TYPE_CODE_128))));
        $pdf->Image('files/barcodes/BC-'.$n2.'.png',$x+(1*$xoffset),$y+($i*$yoffset),$s);

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        file_put_contents('files/barcodes/BC-'.$n3.'.png', base64_decode(base64_encode($generator->getBarcode($n3, $generator::TYPE_CODE_128))));
        $pdf->Image('files/barcodes/BC-'.$n3.'.png',$x+(2*$xoffset),$y+($i*$yoffset),$s);

        $i++;

        if($i==16) break;
    }
      */


    //$pdf->Output('F',"files/forms/reorderForm/RF-".$product_number."-D".date("YmdHi").".pdf");
    $pdf->Output();


?>