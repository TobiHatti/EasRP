<?php
    RegisterCards("department?purchase","&Uuml;bersicht|!|overview","Lagersbestand||warehouse","Nachbestellen||reorder","Warenstruktur||structure");
    echo PreventAutoScroll();

    if(isset($_GET['warehouse']))
    {
        echo '<h2>Aktueller Lagerbestand</h2>';

        echo '
            <br><br>
            <center>
                <form action="/'.basename($_SERVER["REQUEST_URI"], '.php').'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <table class="orders_table">
                    ';
                    $i=0;
                    $prefix_raw = GetProperty("prefix_raw");
                    $strSQL = "SELECT * FROM products WHERE number LIKE '$prefix_raw%'";
                    $rs=mysqli_query($link,$strSQL);
                    while($row=mysqli_fetch_assoc($rs))
                    {
                        $style_color = ($i%2==0) ? 'style="background:#DBEDFF;"' : 'style="background:#F2F2F2;"';
                        $style_id = ($i++%2==0) ? 'id="shaded_cell"' : '';

                        echo '
                            <script>
                            function UpdateStockDisplay'.str_replace('-','',$row['number']).'()
                            {
                                if(document.getElementById("stock_add_'.$row['number'].'").value != 0) document.getElementById("out_stocknew_'.$row['number'].'").value = parseInt('.$row['stock'].') + parseInt(document.getElementById("stock_add_'.$row['number'].'").value);
                                else document.getElementById("out_newStock").value = parseInt(document.getElementById("stock_current").value);
                            }

                            function StockAmtCheck'.str_replace('-','',$row['number']).'()
                            {
                                var max = parseInt(document.getElementById("change_maxstock_'.$row['number'].'").value);
                                var reorder = parseInt(document.getElementById("change_reorderstock_'.$row['number'].'").value);
                                var security = parseInt(document.getElementById("change_securitystock_'.$row['number'].'").value);

                                if(security <= reorder && reorder <= max)
                                {
                                    document.getElementById("out_stockmarks_'.$row['number'].'1").value = "";
                                    document.getElementById("out_stockmarks_'.$row['number'].'2").value = "";
                                    document.getElementById("update_btn_'.$row['number'].'").disabled = false;
                                }
                                else
                                {
                                    document.getElementById("out_stockmarks_'.$row['number'].'1").value = "Bedingung nicht erf\u00fcllt:";
                                    document.getElementById("out_stockmarks_'.$row['number'].'2").value = "H\u00f6chstbestand > Meldebestand > Sicherheitsbestand";
                                    document.getElementById("update_btn_'.$row['number'].'").disabled = true;
                                }
                            }
                            </script>

                            <tr>
                                <td '.$style_id.'>
                                Bestand-Status:<br>
                                ';
                                if($row['security_stock']==0 AND $row['reorder_stock']==0 AND $row['max_stock']==0) echo '<span style="color: #1E90FF"><b>Kein Lagerbestand</b></span>';
                                else if($row['stock']<=0) echo '<span style="color: #CC0000" class="alert_high"><b>Leer</b></span>';
                                else if($row['stock']<=$row['security_stock']) echo '<span style="color: #FF4500" class="alert_medium"><b>Sicherheitsbestand</b></span>';
                                else if($row['stock']<=$row['reorder_stock']) echo '<span style="color: #FFA500" class="alert_low"><b>Nachbestellen</b></span>';
                                else if($row['stock']>$row['reorder_stock']) echo '<span style="color: #32CD32"><b>Bestand OK</b></span>';
                                echo '
                                </td>
                                <td '.$style_id.'>
                                    St&uuml;ck hinzuf&uuml;gen:<br>
                                    <input type="number" oninput="UpdateStockDisplay'.str_replace('-','',$row['number']).'();" name="stock_add_'.$row['number'].'" id="stock_add_'.$row['number'].'" style="width:100px;" class="textfield_mod t_textfield" value="0">
                                    <button style="width:35px;padding:0px;margin:0px;" name="update_stock" class="textfield_mod t_button" value="'.$row['number'].'"><span style="color: #32CD32">&#x2713;</span></button>
                                    '.$row['unit'].'
                                </td>
                                <td '.$style_id.'>
                                    Aktueller Bestand: '.$row['stock'].' '.$row['unit'].'<br>
                                    Neuer Bestand: <output id="out_stocknew_'.$row['number'].'">'.$row['stock'].'</output> '.$row['unit'].'
                                </td>
                                <td '.$style_id.'>
                                    <b>'.$row['number'].'</b><br>
                                    <u>'.$row['name'].'</u><br>
                                    <span style="color: #696969; font-size:10pt;">'.nl2br($row['description']).'</span>
                                </td>
                                <td '.$style_id.'>
                                    H&ouml;chstbestand: '.$row['max_stock'].' '.$row['unit'].'<br>
                                    Meldebestand: '.$row['reorder_stock'].' '.$row['unit'].'<br>
                                    Sicherheitsbestand: '.$row['security_stock'].' '.$row['unit'].'<br>
                                    <a href="#edit'.$row['number'].'" onclick="bgenScroll();"><span style="color: #FFA500"><u>&#128736; Bearbeiten</u></span></a>
                                </td>
                            </tr>
                            <tr>
                                <td class="foldup_cell" id="edit'.$row['number'].'" '.$style_color.' colspan=5>
                                    <table style="width:100%;">
                                        <tr>
                                            <td style="text-align:center;">H&ouml;chstbestand:</td>
                                            <td style="text-align:center;">Meldebestand:</td>
                                            <td style="text-align:center;">Sicherheitsbestand:</td>
                                            <td rowspan=2>
                                                <button class="button_m t_button" type="submit" name="update_stock_marks" id="update_btn_'.$row['number'].'" value="'.$row['number'].'">
                                                    Bestandsmarken<br>&auml;ndern
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:center;"><input type="number" oninput="StockAmtCheck'.str_replace('-','',$row['number']).'();" id="change_maxstock_'.$row['number'].'" name="change_maxstock_'.$row['number'].'" style="width:100px;" class="textfield_mod t_textfield" value="'.$row['max_stock'].'">'.$row['unit'].'</td>
                                            <td style="text-align:center;"><input type="number" oninput="StockAmtCheck'.str_replace('-','',$row['number']).'();" id="change_reorderstock_'.$row['number'].'" name="change_reorderstock_'.$row['number'].'" style="width:100px;" class="textfield_mod t_textfield" value="'.$row['reorder_stock'].'">'.$row['unit'].'</td>
                                            <td style="text-align:center;"><input type="number" oninput="StockAmtCheck'.str_replace('-','',$row['number']).'();" id="change_securitystock_'.$row['number'].'" name="change_securitystock_'.$row['number'].'" style="width:100px;" class="textfield_mod t_textfield" value="'.$row['security_stock'].'">'.$row['unit'].'</td>
                                        </tr>
                                        <tr>
                                            <td colspan=4 style="text-align:center;">
                                                <span style="color: #CC0000">
                                                    <output id="out_stockmarks_'.$row['number'].'1"></output><br>
                                                    <output id="out_stockmarks_'.$row['number'].'2"></output>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        ';
                    }
                    echo '
                    </table>
                </form>
            </center>
        ';
    }

    if(isset($_GET['reorder']))
    {
        echo '<h2>Produkt Nachbestellen</h2>';

        $redirect = (SubStringFind(basename($_SERVER["REQUEST_URI"], '.php'),'=')) ? substr(basename($_SERVER["REQUEST_URI"], '.php'), 0, strpos(basename($_SERVER["REQUEST_URI"], '.php'), '=')) : basename($_SERVER["REQUEST_URI"], '.php');

        echo '
            <br><br>
            <center>
                <table class="data_inputbox_m">
                    <tr>
                        <td style="text-align:center"><h3>Produkt Ausw&auml;hlen</h3></td>
                    </tr>
                    <tr>
                        <td>
                            <br><br>
                            <input type="hidden" id="extension" value="'.$redirect.'"/>
                            <select class="selectbox_m t_selectbox" id="product_list" onchange="ProductSelectRawReorder();">
                                <option value="none">&#9660; Hilfsprodukt ausw&auml;hlen</option>
                            ';

                            $prefix = GetProperty("prefix_raw");
                            $strSQL = "SELECT * FROM products WHERE number LIKE '$prefix%'";
                            $rs=mysqli_query($link,$strSQL);
                            while($row=mysqli_fetch_assoc($rs))
                            {
                                echo '<option value="'.$row['number'].'">'.$row['number'].' - '.$row['name'].'</option>';
                            }
                            echo '
                            </select>
                        </td>
                    </tr>
                    <tr style="display:none" id="loader">
                        <td>
                            <br><br>
                            <center><img src="/files/content/loader.gif" alt="" class="loader"/><span style="font-size: 18pt;"> Bitte warten...</span></center>
                        </td>
                    </tr>
                </table>
            </center>
        ';

        if($_GET['reorder']!=null)
        {
            $pdfScale=3;
            echo '
                <br><br>
                <center>
                <div style="display:inline-block; vertical-align:top">
                    <table class="content_table" style="width:350px;">
                        <tr>
                            <td style="background:#87CEFA"><h3>Bestellformular</h3></td>
                        </tr>
                        <tr>
                            <td id="shaded_cell">Produkte</td>
                        </tr>
                        <tr>
                            <td>X 4x HLRW-1234 - EinProduktTitel</td>
                        </tr>
                    </table>
                </div>
                <div style="display: inline; vertical-align:top; margin-left:100px;">
                    <iframe src="/pdfReorderForm?productNumber='.$_GET['reorder'].'&ship=1&payment=none" style="height:'. 297 * $pdfScale .'px; width: '. 210 * $pdfScale .'px;" id="orderConfirmationFrame"></iframe>
                </div>
                </center>
            ';
        }
    }

    if(isset($_GET['structure']))
    {
        include("products_5structure.php");
    }

?>