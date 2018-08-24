<?php
    $pnumber = $_GET['s'];
    $strSQL = "SELECT * FROM products WHERE number = '$pnumber'";
    $rs=mysqli_query($link,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        if(SubStringFind($_GET['s'],GetProperty("prefix_support")))
        {
            echo '<h1>Hilfsprodukt '.$_GET['s'].' - <b>'.fetch("products","name","number",$_GET['s']).'</b></h1>';

            echo '
                <br><br><br>
                <center>
                    <table class="data_inputbox_m">
                        <tr>
                            <td style="padding:20px;">
                                <center>
                                '.(($row['product_image']!='') ? '<img src="/files/products/raw_products/'.$pnumber.'/'.$row['product_image'].'" alt="" style="width:200px;"/><br>' : '').'
                                '.BarCodeImg($pnumber,true).'
                                </center>
                            </td>
                            <td colspan=2 style="vertical-align: text-top;">
                                <h1 style="margin:0px;">'.$pnumber.'</h1>
                                <h2 style="margin:0px;">'.$row['name'].'</h2>
                                Klasse: Hilfsprodukt ('.GetProperty("prefix_support").')<br><br>
                                '.nl2br($row['description']).'
                            </td>
                            <td>
                                <center>
                                    <a href="'.$row['link'].'" target="_blank"><button class="button_m t_button" style="width:160px;height:60px;">Produktseite</button></a><br>
                                    <button class="button_m t_button" style="width:160px;height:60px;">Bestellantrag erstellen</button>
                                </center>
                            </td>
                        </tr>
                        <tr>
                            <td id="shaded_cell">Preise</td>
                            <td id="shaded_cell" colspan=2>Lager</td>
                            <td id="shaded_cell">Einkauf</td>
                        </tr>
                        <tr>
                            <td rowspan=2>'.$row['price'].'&euro; f&uuml;r '.$row['per_item'].' '.$row['unit'].'</td>
                            <td>Lagerbestand:</td>
                            <td>'.$row['stock'].' '.$row['unit'].'</td>
                            <td rowspan=2>Hersteller: '.$row['production_company'].'</td>
                        </tr>
                        <tr>
                            <td id="shaded_cell">&bull; H&ouml;chstbestand:</td>
                            <td id="shaded_cell">'.$row['max_stock'].' '.$row['unit'].'</td>
                        </tr>
                        <tr>
                            <td id="shaded_cell">Verwendet in '.MySQLResultCount("SELECT * FROM product_contains WHERE child = '$pnumber'").' Produkten</td>
                            <td>&bull; Meldebestand:</td>
                            <td>'.$row['reorder_stock'].' '.$row['unit'].'</td>
                            <td rowspan=2 id="shaded_cell">Vertreiber: '.$row['retail_company'].'</td>
                        </tr>
                        <tr>
                            <td>x '.$row['unit'].' verwendet insgesamt</td>
                            <td id="shaded_cell">&bull; Sicherheitsbestand:</td>
                            <td id="shaded_cell">'.$row['security_stock'].' '.$row['unit'].'</td>
                        </tr>
                    </table>
                    <br><br>
                    <form action="/'.basename($_SERVER["REQUEST_URI"], '.php').'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                        <table class="data_inputbox_m">
                            <tr>
                                <td colspan=3 style="text-align:center;"><h2>Lagerbestand Aktualisieren</h2></td>
                            </tr>
                            <tr>
                                <td colspan=3 id="shaded_cell" style="text-align:center;">
                                    Aktueller Lagerbestand: '.$row['stock'].' '.$row['unit'].'
                                    <input type="hidden" value="'.$row['stock'].'" id="stock_current"/>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:right;width:100px;">
                                    Hinzuf&uuml;gen:
                                </td>
                                <td style="text-align:center;">
                                    <input name="stock_add_'.$_GET['s'].'" class="textfield_s t_textfield" type="number" value="1" oninput="UpdateNewStockDisplay();" id="stock_add">
                                </td>
                                <td style="text-align:left;width:100px;">
                                   '.$row['unit'].'
                                </td>
                            </tr>
                            <tr>
                                <td colspan=3 id="shaded_cell" style="text-align:center;">
                                    Neuer Lagerbestand: <output id="out_newStock">'.(1+$row['stock']).'</output> '.$row['unit'].'
                                </td>
                            </tr>
                            <tr>
                                <td colspan=3 style="text-align:center;">
                                    <br>
                                    <button class="button_m t_button" value="'.$_GET['s'].'" name="update_stock">Bestand aktualisieren</button>
                                </td>
                            </tr>
                        </table>
                    </form>
                </center>
            ';
        }
        else if(SubStringFind($_GET['s'],GetProperty("prefix_raw")))
        {
            echo '<h1>Rohteil '.$_GET['s'].' - <b>'.fetch("products","name","number",$_GET['s']).'</b></h1>';

            echo '
                <br><br><br>
                <center>
                    <table class="data_inputbox_m">
                        <tr>
                            <td style="padding:20px;">
                                <center>
                                '.(($row['product_image']!='') ? '<img src="/files/products/raw_products/'.$pnumber.'/'.$row['product_image'].'" alt="" style="width:200px;"/><br>' : '').'
                                '.BarCodeImg($pnumber,true).'
                                </center>
                            </td>
                            <td colspan=2 style="vertical-align: text-top;">
                                <h1 style="margin:0px;">'.$pnumber.'</h1>
                                <h2 style="margin:0px;">'.$row['name'].'</h2>
                                Klasse: Rohteil ('.GetProperty("prefix_raw").')<br><br>
                                '.nl2br($row['description']).'
                            </td>
                            <td>
                                <center>
                                    <a href="'.$row['link'].'" target="_blank"><button class="button_m t_button" style="width:160px;height:60px;">Produktseite</button></a><br>
                                    <button class="button_m t_button" style="width:160px;height:60px;">Bestellantrag erstellen</button>
                                </center>
                            </td>
                        </tr>
                        <tr>
                            <td id="shaded_cell">Preise</td>
                            <td id="shaded_cell" colspan=2>Lager</td>
                            <td id="shaded_cell">Einkauf</td>
                        </tr>
                        <tr>
                            <td rowspan=2>'.$row['price'].'&euro; f&uuml;r '.$row['per_item'].' '.$row['unit'].'</td>
                            <td>Lagerbestand:</td>
                            <td>'.$row['stock'].' '.$row['unit'].'</td>
                            <td rowspan=2>Hersteller: '.$row['production_company'].'</td>
                        </tr>
                        <tr>
                            <td id="shaded_cell">&bull; H&ouml;chstbestand:</td>
                            <td id="shaded_cell">'.$row['max_stock'].' '.$row['unit'].'</td>
                        </tr>
                        <tr>
                            <td id="shaded_cell">Verbaut in '.MySQLResultCount("SELECT * FROM product_contains WHERE child = '$pnumber'").' Produkten</td>
                            <td>&bull; Meldebestand:</td>
                            <td>'.$row['reorder_stock'].' '.$row['unit'].'</td>
                            <td rowspan=2 id="shaded_cell">Vertreiber: '.$row['retail_company'].'</td>
                        </tr>
                        <tr>
                            <td>x '.$row['unit'].' verbaut insgesamt</td>
                            <td id="shaded_cell">&bull; Sicherheitsbestand:</td>
                            <td id="shaded_cell">'.$row['security_stock'].' '.$row['unit'].'</td>
                        </tr>
                    </table>
                    <br><br>
                    <form action="/'.basename($_SERVER["REQUEST_URI"], '.php').'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                        <table class="data_inputbox_m">
                            <tr>
                                <td colspan=3 style="text-align:center;"><h2>Lagerbestand Aktualisieren</h2></td>
                            </tr>
                            <tr>
                                <td colspan=3 id="shaded_cell" style="text-align:center;">
                                    Aktueller Lagerbestand: '.$row['stock'].' '.$row['unit'].'
                                    <input type="hidden" value="'.$row['stock'].'" id="stock_current"/>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:right;width:100px;">
                                    Hinzuf&uuml;gen:
                                </td>
                                <td style="text-align:center;">
                                    <input name="stock_add_'.$_GET['s'].'" class="textfield_s t_textfield" type="number" value="1" oninput="UpdateNewStockDisplay();" id="stock_add">
                                </td>
                                <td style="text-align:left;width:100px;">
                                   '.$row['unit'].'
                                </td>
                            </tr>
                            <tr>
                                <td colspan=3 id="shaded_cell" style="text-align:center;">
                                    Neuer Lagerbestand: <output id="out_newStock">'.(1+$row['stock']).'</output> '.$row['unit'].'
                                </td>
                            </tr>
                            <tr>
                                <td colspan=3 style="text-align:center;">
                                    <br>
                                    <button class="button_m t_button" value="'.$_GET['s'].'" name="update_stock">Bestand aktualisieren</button>
                                </td>
                            </tr>
                        </table>
                    </form>
                </center>
            ';
        }
        else if(SubStringFind($_GET['s'],GetProperty("prefix_semiproduct")))
        {
            echo '<h1>Halbprodukt '.$_GET['s'].' - <b>'.fetch("products","name","number",$_GET['s']).'</b></h1>';

            echo '
                <br><br><br>
                <center>
                    <table class="data_inputbox_m">
                        <tr>
                            <td style="padding:20px;">
                                <center>
                                '.(($row['product_image']!='') ? '<img src="/files/products/raw_products/'.$pnumber.'/'.$row['product_image'].'" alt="" style="width:200px;"/><br>' : '').'
                                '.BarCodeImg($pnumber,true).'
                                </center>
                            </td>
                            <td colspan=2 style="vertical-align: text-top;">
                                <h1 style="margin:0px;">'.$pnumber.'</h1>
                                <h2 style="margin:0px;">'.$row['name'].'</h2>
                                Klasse: Halbprodukt ('.GetProperty("prefix_semiproduct").')<br><br>
                                '.nl2br($row['description']).'
                            </td>
                            <td>
                                <!-- Not used Currently -->
                            </td>
                        </tr>
                        <tr>
                            <td id="shaded_cell">Preise</td>
                            <td id="shaded_cell" colspan=2>Lager</td>
                            <td id="shaded_cell">Zusammenstellung</td>
                        </tr>
                        <tr>
                            <td rowspan=2>
                                Zwischensumme: '.number_format(ProductPrice($row['number']),2).' &euro;
                            </td>
                            <td>Lagerbestand:</td>
                            <td>'.$row['stock'].' '.$row['unit'].'</td>
                            <td rowspan=2>
                                Rohteile: '.MySQLResultCount("SELECT * FROM product_contains WHERE parent = '$pnumber' AND child LIKE '".GetProperty("prefix_raw")."%'").' Stk.<br>
                                Hilfsprodukte: '.MySQLResultCount("SELECT * FROM product_contains WHERE parent = '$pnumber' AND child LIKE '".GetProperty("prefix_support")."%'").' Stk.<br>
                            </td>
                        </tr>
                        <tr>
                            <td id="shaded_cell">&bull; H&ouml;chstbestand:</td>
                            <td id="shaded_cell">'.$row['max_stock'].' '.$row['unit'].'</td>
                        </tr>
                        <tr>
                            <td id="shaded_cell">Verbaut in '.MySQLResultCount("SELECT * FROM product_contains WHERE child = '$pnumber'").' Produkten</td>
                            <td>&bull; Meldebestand:</td>
                            <td>'.$row['reorder_stock'].' '.$row['unit'].'</td>
                            <td rowspan=2 id="shaded_cell">
                                Rohteile gesamt: '.MySQLSkalar("SELECT SUM(quantity) AS x FROM product_contains WHERE parent = '$pnumber' AND child LIKE '".GetProperty("prefix_raw")."%'").' Stk.<br>
                                Hilfsprodukte gesamt: '.MySQLSkalar("SELECT SUM(quantity) AS x FROM product_contains WHERE parent = '$pnumber' AND child LIKE '".GetProperty("prefix_support")."%'").' Stk.<br>
                            </td>
                        </tr>
                        <tr>
                            <td>x '.$row['unit'].' verbaut insgesamt</td>
                            <td id="shaded_cell">&bull; Sicherheitsbestand:</td>
                            <td id="shaded_cell">'.$row['security_stock'].' '.$row['unit'].'</td>
                        </tr>
                    </table>
                    <br><br>
                    <form action="/'.basename($_SERVER["REQUEST_URI"], '.php').'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                        <table class="data_inputbox_m">
                            <tr>
                                <td colspan=3 style="text-align:center;"><h2>Lagerbestand Aktualisieren</h2></td>
                            </tr>
                            <tr>
                                <td colspan=3 id="shaded_cell" style="text-align:center;">
                                    Aktueller Lagerbestand: '.$row['stock'].' '.$row['unit'].'
                                    <input type="hidden" value="'.$row['stock'].'" id="stock_current"/>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:right;width:100px;">
                                    Hinzuf&uuml;gen:
                                </td>
                                <td style="text-align:center;">
                                    <input name="stock_add_'.$_GET['s'].'" class="textfield_s t_textfield" type="number" value="1" oninput="UpdateNewStockDisplay();" id="stock_add">
                                </td>
                                <td style="text-align:left;width:100px;">
                                   '.$row['unit'].'
                                </td>
                            </tr>
                            <tr>
                                <td colspan=3 id="shaded_cell" style="text-align:center;">
                                    Neuer Lagerbestand: <output id="out_newStock">'.(1+$row['stock']).'</output> '.$row['unit'].'
                                </td>
                            </tr>
                            <tr>
                                <td colspan=3 style="text-align:center;">
                                    <br>
                                    <button class="button_m t_button" value="'.$_GET['s'].'" name="update_stock">Bestand aktualisieren</button>
                                </td>
                            </tr>
                        </table>
                    </form>
                </center>
            ';
        }
        else if(SubStringFind($_GET['s'],GetProperty("prefix_product")))
        {
            echo '<h1>Produkt '.$_GET['s'].' - <b>'.fetch("products","name","number",$_GET['s']).'</b></h1>';

            echo '
                <br><br><br>
                <center>
                    <table class="data_inputbox_m">
                        <tr>
                            <td style="padding:20px;">
                                <center>
                                '.(($row['product_image']!='') ? '<img src="/files/products/raw_products/'.$pnumber.'/'.$row['product_image'].'" alt="" style="width:200px;"/><br>' : '').'
                                '.BarCodeImg($pnumber,true).'
                                </center>
                            </td>
                            <td colspan=2 style="vertical-align: text-top;">
                                <h1 style="margin:0px;">'.$pnumber.'</h1>
                                <h2 style="margin:0px;">'.$row['name'].'</h2>
                                Klasse: Produkt ('.GetProperty("prefix_product").')<br><br>
                                '.nl2br($row['description']).'
                            </td>
                            <td>
                                <!-- Not used Currently -->
                            </td>
                        </tr>
                        <tr>
                            <td id="shaded_cell">Preise</td>
                            <td id="shaded_cell" colspan=2>Lager</td>
                            <td id="shaded_cell">Zusammenstellung</td>
                        </tr>
                        <tr>
                            <td rowspan=2>
                                Einkaufspreis: '.number_format(ProductPrice($row['number']),3).'&euro;<br>
                                Verkaufspreis: '.number_format($row['resell_price'],2).'&euro;<br>
                                Gewinn: '.number_format($row['resell_price'] - ProductPrice($row['number']),3).'&euro;
                            </td>
                            <td>Lagerbestand:</td>
                            <td>'.$row['stock'].' '.$row['unit'].'</td>
                            <td rowspan=2>
                                Halbprodukte: '.MySQLResultCount("SELECT * FROM product_contains WHERE parent = '$pnumber' AND child LIKE '".GetProperty("prefix_semiproduct")."%'").' Stk.<br>
                                Rohteile: '.MySQLResultCount("SELECT * FROM product_contains WHERE parent = '$pnumber' AND child LIKE '".GetProperty("prefix_raw")."%'").' Stk.<br>
                                Hilfsprodukte: '.MySQLResultCount("SELECT * FROM product_contains WHERE parent = '$pnumber' AND child LIKE '".GetProperty("prefix_support")."%'").' Stk.<br>
                            </td>
                        </tr>
                        <tr>
                            <td id="shaded_cell">&bull; H&ouml;chstbestand:</td>
                            <td id="shaded_cell">'.$row['max_stock'].' '.$row['unit'].'</td>
                        </tr>
                        <tr>
                            <td id="shaded_cell" rowspan=2>x Stk. verkauft</td>
                            <td>&bull; Meldebestand:</td>
                            <td>'.$row['reorder_stock'].' '.$row['unit'].'</td>
                            <td rowspan=2 id="shaded_cell">
                                Halbprodukte gesamt: '.MySQLSkalar("SELECT SUM(quantity) AS x FROM product_contains WHERE parent = '$pnumber' AND child LIKE '".GetProperty("prefix_semiproduct")."%'").' Stk.<br>
                                Rohteile gesamt: '.MySQLSkalar("SELECT SUM(quantity) AS x FROM product_contains WHERE parent = '$pnumber' AND child LIKE '".GetProperty("prefix_raw")."%'").' Stk.<br>
                                Hilfsprodukte gesamt: '.MySQLSkalar("SELECT SUM(quantity) AS x FROM product_contains WHERE parent = '$pnumber' AND child LIKE '".GetProperty("prefix_support")."%'").' Stk.<br>
                            </td>
                        </tr>
                        <tr>
                            <td id="shaded_cell">&bull; Sicherheitsbestand:</td>
                            <td id="shaded_cell">'.$row['security_stock'].' '.$row['unit'].'</td>
                        </tr>
                    </table>
                    <br><br>
                    <form action="/'.basename($_SERVER["REQUEST_URI"], '.php').'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                        <table class="data_inputbox_m">
                            <tr>
                                <td colspan=3 style="text-align:center;"><h2>Lagerbestand Aktualisieren</h2></td>
                            </tr>
                            <tr>
                                <td colspan=3 id="shaded_cell" style="text-align:center;">
                                    Aktueller Lagerbestand: '.$row['stock'].' '.$row['unit'].'
                                    <input type="hidden" value="'.$row['stock'].'" id="stock_current"/>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:right;width:100px;">
                                    Hinzuf&uuml;gen:
                                </td>
                                <td style="text-align:center;">
                                    <input name="stock_add_'.$_GET['s'].'" class="textfield_s t_textfield" type="number" value="1" oninput="UpdateNewStockDisplay();" id="stock_add">
                                </td>
                                <td style="text-align:left;width:100px;">
                                   '.$row['unit'].'
                                </td>
                            </tr>
                            <tr>
                                <td colspan=3 id="shaded_cell" style="text-align:center;">
                                    Neuer Lagerbestand: <output id="out_newStock">'.(1+$row['stock']).'</output> '.$row['unit'].'
                                </td>
                            </tr>
                            <tr>
                                <td colspan=3 style="text-align:center;">
                                    <br>
                                    <button class="button_m t_button" value="'.$_GET['s'].'" name="update_stock">Bestand aktualisieren</button>
                                </td>
                            </tr>
                        </table>
                    </form>
                </center>
            ';
        }
    }
?>