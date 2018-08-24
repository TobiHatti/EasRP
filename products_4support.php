<?php
    if(isset($_GET['show']))
    {
        echo '
            <h2>Hilfsmittel</h2>
            '.ListStyleSelect().'
            '.PreventAutoScroll().'
            <center>
                <table class="orders_table">
                    ';

                    if($_SESSION['list_style']=='detail')
                    {
                        echo '
                            <tr>
                                <td>Operationen</td>
                                <td>Beschreibung</td>
                                <td>Preis</td>
                                <td>Firmen</td>
                                <td>Dateien</td>
                            </tr>
                        ';
                    }
                    if($_SESSION['list_style']=='list')
                    {
                        echo '
                            <tr>
                                <td>Operationen</td>
                                <td>Artikel-Nr.</td>
                                <td>Bezeichnung</td>
                                <td>Beschreibung</td>
                                <td>Preis</td>
                            </tr>
                        ';
                    }

                    $prefix = GetProperty("prefix_support");
                    $strSQL = "SELECT * FROM products WHERE number LIKE '$prefix%'";
                    $i=0;
                    $rs=mysqli_query($link,$strSQL);
                    while($row=mysqli_fetch_assoc($rs))
                    {
                        $style_color = ($i%2==0) ? 'style="background:#DBEDFF;"' : 'style="background:#F2F2F2;"';
                        $style_id = ($i++%2==0) ? 'id="shaded_cell"' : '';


                        if($_SESSION['list_style']=='detail')
                        {
                            echo '
                                <tr>
                                    <td '.$style_id.'>
                                        <a name="'.$row['number'].'"></a>
                                        <a href="products?support&edit='.$row['number'].'"><span style="color: #FFA500">&#128736; Bearbeiten<br></span></a>
                                        <a href="products?support&delete='.$row['number'].'"><span style="color: #CC0000">&#x2718; L&ouml;schen<br> </span></a>
                                    </td>
                                    <td '.$style_id.'>
                                        <b>'.$row['number'].'</b><br>
                                        ';
                                        echo ($row['comment']=='') ? '' :('<div title="'.$row['comment'].'" style="display:inline; cursor:pointer">&#128712;</div>');
                                        echo '
                                        <u>'.$row['name'].'</u><br>
                                        <i><span style="color: #595959">'.nl2br($row['description']).'</span></i>
                                    </td>
                                    <td '.$style_id.'>
                                        <b>Kosten:</b><br>
                                        '.$row['price'].'&euro; f&uuml;r '.$row['per_item'].' '.$row['unit'].'<br><br>
                                        <b>Lager:</b><br>
                                        Lagerbestand: '.$row['stock'].'<br><br>
                                        Meldebestand: '.$row['reorder_stock'].'<br>
                                        Sicherheitsbestand: '.$row['security_stock'].'<br>
                                        Lager: '.$row['storage_location'].'
                                    </td>
                                    <td '.$style_id.'>
                                        <b>Hersteller:</b> '.$row['production_company'].'<br>
                                        <b>Vertreiber:</b> '.$row['retail_company'].'<br><br>
                                        ';
                                        echo ($row['link']=='') ? '' : '<a href="'.$row['link'].'" target="_blank"><button class="t_button button_m">Zur Produktseite</button></a>';
                                        echo '
                                    </td>
                                    <td '.$style_id.'>
                                        '.DirectoryListing('files/products/support_products/'.$row['number'].'/').'
                                    </td>
                                </tr>
                            ';
                        }
                        if($_SESSION['list_style']=='list')
                        {
                            echo '
                                <tr>
                                    <td '.$style_id.'>
                                        <a name="'.$row['number'].'"></a>
                                        <a href="#detail'.$row['number'].'" onclick="bgenScroll();"><span style="color: #1E90FF">&#128712; Details<br></span></a>
                                    </td>
                                    <td '.$style_id.'>
                                        <b>'.$row['number'].'</b>
                                        ';
                                        echo ($row['comment']=='') ? '' :('<div title="'.$row['comment'].'" style="display:inline; cursor:pointer">&#128712;</div>');
                                        echo '
                                    </td>
                                    <td '.$style_id.'>
                                        <u>'.$row['name'].'</u>
                                    </td>
                                    <td '.$style_id.'>
                                        <i><span style="color: #595959">'.nl2br($row['description']).'</span></i>
                                    </td>
                                    <td '.$style_id.'>
                                        <b>Kosten:</b> '.$row['price'].'&euro; f&uuml;r '.$row['per_item'].' '.$row['unit'].'
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan=5 class="foldup_cell" id="detail'.$row['number'].'" '.$style_color.'>
                                        <table style="width:100%;">
                                            <tr>
                                                <td>
                                                    <a href="products?support&edit='.$row['number'].'"><span style="color: #FFA500">&#128736; Bearbeiten<br></span></a>
                                                    <a href="products?support&delete='.$row['number'].'"><span style="color: #CC0000">&#x2718; L&ouml;schen<br> </span></a>
                                                </td>
                                                <td>
                                                    <b>'.$row['number'].'</b><br>
                                                    ';
                                                    echo ($row['comment']=='') ? '' :('<div title="'.$row['comment'].'" style="display:inline; cursor:pointer">&#128712;</div>');
                                                    echo '
                                                    <u>'.$row['name'].'</u><br>
                                                    <i><span style="color: #595959">'.nl2br($row['description']).'</span></i>
                                                </td>
                                                <td>
                                                    <b>Kosten:</b><br>
                                                    '.$row['price'].'&euro; f&uuml;r '.$row['per_item'].' '.$row['unit'].'<br><br>
                                                    <b>Lager:</b><br>
                                                    Lagerbestand: '.$row['stock'].'<br><br>
                                                    H&ouml;chstbestand: '.$row['max_stock'].'<br>
                                                    Meldebestand: '.$row['reorder_stock'].'<br>
                                                    Sicherheitsbestand: '.$row['security_stock'].'<br>
                                                    Lager: '.$row['storage_location'].'
                                                </td>
                                                <td>
                                                    <b>Hersteller:</b> '.$row['production_company'].'<br>
                                                    <b>Vertreiber:</b> '.$row['retail_company'].'<br><br>
                                                    ';
                                                    echo ($row['link']=='') ? '' : '<a href="'.$row['link'].'" target="_blank"><button class="t_button button_m">Zur Produktseite</button></a>';
                                                    echo '
                                                </td>
                                                <td>
                                                    <img src="/files/products/support_products/'.$row['number'].'/'.$row['product_image'].'" alt="" style="width:100px;"/>
                                                </td>
                                                <td>
                                                    '.DirectoryListing('files/products/support_products/'.$row['number'].'/').'
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan=6>
                                                    <a href="#close"><img src="/files/content/close.png" alt="" class="close_icon"/></a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            ';
                        }
                        if($_SESSION['list_style']=='grid')
                        {

                        }


                    }

                    echo '
                </table>
            </center>
        ';

    }
    if(isset($_GET['new']))
    {
        echo '
            <h2>Neues Hilfsmittel eintragen</h2>
            <center>
                <form action="/products" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <table class="data_inputbox_m">
                        <tr>
                            <td>Artikel-Name</td>
                            <td><input name="name" class="textfield_m t_textfield" placeholder="Name..."></td>
                        </tr>
                        <tr>
                            <td>Artikel-Nummer</td>
                            <td><input class="textfield_mod t_textfield" style="width:60px;margin-right:0px;" value="'.GetProperty("prefix_support").'-" readonly><input name="number" class="textfield_mod t_textfield" style="width:231px;margin-left:0px" placeholder="Nummer..."></td>
                        </tr>
                        <tr>
                            <td>Artikel-Bezeichnung</td>
                            <td><textarea name="description" class="textarea_m t_textarea" placeholder="Bezeichnung..."></textarea></td>
                        </tr>
                        <tr>
                            <td>Mengeneinheit</td>
                            <td>
                                <select id="unit_list" class="selectbox_m t_selectbox" name="unit" onchange="UpdateUnit();">
                                    <option value="Stk.">[Stk.] St&uuml;ck</option>
                                    <option value="m">[m] Meter</option>
                                    <option value="cm">[cm] Zentimeter</option>
                                    <option value="mm">[mm] Milimeter</option>
                                    <option value="l">[l] Liter</option>
                                    <option value="kg">[kg] Kilogramm</option>
                                    <option value="g">[g] Gramm</option>
                                    <option value="m2">[m&sup2;] Quadratmeter</option>
                                    <option value="m3">[m&sup3;] Kubikmeter</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Preis</td>
                            <td><input type="number" step="0.001" name="price" class="textfield_xs t_textfield"> &euro; f&uuml;r <input type="number"  step="0.01" name="per_item" class="textfield_xxs t_textfield"><output id="unit">Stk.</output></td>
                        </tr>
                        <tr>
                            <td><br>Lager-H&ouml;chstbestand</td>
                            <td><br><input type="number" step="0.01" name="max_stock" class="textfield_mod t_textfield" style="width:250px;margin-right:0px;" oninput="StockAmtCheck();" placeholder="Anzahl..." id="max_stock"><output>Stk.</output></td>
                        </tr>
                        <tr>
                            <td>Lager-Meldebestand</td>
                            <td><input type="number" step="0.01" name="reorder_stock" class="textfield_mod t_textfield" style="width:250px;margin-right:0px;" oninput="StockAmtCheck();" placeholder="Anzahl..." id="reorder_stock"><output>Stk.</output></td>
                        </tr>
                        <tr>
                            <td>Lager-Sicherheitsbestand</td>
                            <td><input type="number" step="0.01" name="security_stock" class="textfield_mod t_textfield" style="width:250px;margin-right:0px;" oninput="StockAmtCheck();" placeholder="Anzahl..." id="security_stock"><output>Stk.</output></td>
                        </tr>
                        <tr>
                            <td colspan=2>
                                <center>
                                    <span style="color: #CC0000">
                                        <output id="out_stockdata_line1"></output><br>
                                        <output id="out_stockdata_line2"></output>
                                    </span>
                                </center>
                            </td>
                        </tr>
                        <tr>
                            <td><br>Lager</td>
                            <td>
                                <br>
                                <select name="storage_location" class="textfield_m t_textfield">
                                    <option value="" selected disabled>&#9660; Lagerstandort Ausw&auml;hlen</option>
                                    ';
                                    $strSQL = "SELECT * FROM storage_locations";
                                    $rs=mysqli_query($link,$strSQL);
                                    while($row=mysqli_fetch_assoc($rs)) echo '<option value="'.$row['storage_id'].'">'.$row['storage_id'].' - '.$row['storage_name'].'</option>';
                                    echo '
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Hersteller-Firma</td>
                            <td>
                                <input name="company" id="producer" class="textfield_mod t_textfield" style="width:250px;margin-right:0px;" placeholder="Hersteller...">
                                <select class="textfield_mod t_textfield" style="width:50px;margin-left:0px;" id="producer_list" oninput="AutoFillProducer();">
                                    <option value="none">&#9660; Bestehende Firma ausw&auml;hlen:</option>
                                    ';

                                    $strSQL = "SELECT DISTINCT production_company AS company FROM products";
                                    $rs=mysqli_query($link,$strSQL);
                                    while($row=mysqli_fetch_assoc($rs)) echo '<option value="'.$row['company'].'">'.$row['company'].'</option>';

                                    echo '
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Vertriebs-Firma</td>
                            <td>
                                <input name="retailer" id="retailer" class="textfield_mod t_textfield" style="width:250px;margin-right:0px;" placeholder="Vertreiber...">
                                <select class="textfield_mod t_textfield" style="width:50px;margin-left:0px;" id="retailer_list" oninput="AutoFillRetailer();">
                                    <option value="none">&#9660; Bestehende Firma ausw&auml;hlen:</option>
                                    ';

                                    $strSQL = "SELECT DISTINCT retail_company AS company FROM products";
                                    $rs=mysqli_query($link,$strSQL);
                                    while($row=mysqli_fetch_assoc($rs)) echo '<option value="'.$row['company'].'">'.$row['company'].'</option>';

                                    echo '
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Link/Bestellseite</td>
                            <td><textarea name="link" class="textarea_m t_textarea" placeholder="http://..."></textarea></td>
                        </tr>
                        <tr>
                            <td>Dateien</td>
                            <td>
                                &nbsp;<label for="file" class="t_button">&nbsp;&nbsp;'.langlib("Datei(en) ausw&auml;hlen").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><br>
                                 <input type="file" id="file" name="files[]" multiple="multiple" hidden/>
                            </td>
                        </tr>
                        <tr>
                            <td>Produktbild</td>
                            <td>
                                <br>&nbsp;<label for="filesthumb" class="t_button">&nbsp;&nbsp;Titelbild ausw&auml;hlen&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><br>
                                 <input type="file" id="filesthumb" name="filesthumb[]" multiple="multiple" hidden/>
                            </td>
                        </tr>
                        <tr>
                            <td>Kommentar</td>
                            <td><textarea name="comment" class="textarea_m t_textarea" placeholder="Kommentar..."></textarea></td>
                        </tr>
                        <tr>
                            <td colspan=2><br><br><center><button class="button_m t_button" value="new" name="add_support_product" type="submit" id="continue_btn">Hilfsmittel eintragen</button></center></td>
                        </tr>
                    </table>
                </form>
            </center>
        ';
    }
    if(isset($_GET['edit']))
    {
        echo '<h2>Hilfsmittel bearbeiten</h2>';

        $redirect = (SubStringFind(basename($_SERVER["REQUEST_URI"], '.php'),'=')) ? substr(basename($_SERVER["REQUEST_URI"], '.php'), 0, strpos(basename($_SERVER["REQUEST_URI"], '.php'), '=')) : basename($_SERVER["REQUEST_URI"], '.php');

        echo '
            <center>
                <table class="data_inputbox_m">
                    <tr>
                        <td><center><h3>Produkt ausw&auml;hlen:</h3></center></td>
                    </tr>
                    <tr>
                        <td>
                            <br><br>
                            <input type="hidden" id="extension" value="'.$redirect.'"/>
                            <select class="selectbox_m t_selectbox" id="product_list" onchange="ProductSelectEdit();">
                                <option value="none">&#9660; Hilfsprodukt ausw&auml;hlen</option>
                        ';
                        $prefix = GetProperty("prefix_support");
                        $strSQL = "SELECT * FROM products WHERE number LIKE '$prefix%'";
                        $rs=mysqli_query($link,$strSQL);
                        while($row=mysqli_fetch_assoc($rs))
                        {
                            echo '<option value="'.$row['number'].'">'.$row['number'].' - '.$row['name'].'</option>';
                        }
                        echo $_GET['edit'];
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

        if($_GET['edit']!=null)
        {
            $number=$_GET['edit'];
            $strSQL = "SELECT * FROM products WHERE number LIKE '$number'";
            $rs=mysqli_query($link,$strSQL);
            while($row=mysqli_fetch_assoc($rs))
            {
                echo '<h2>'.$row['number'].' - '.$row['name'].' bearbeiten</h2>';

                echo '
                    <center>
                        <form action="/products" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                            <table class="data_inputbox_m">
                                <tr>
                                    <td>Artikel-Name</td>
                                    <td><input name="name" class="textfield_m t_textfield" placeholder="Name..." value="'.$row['name'].'"></td>
                                </tr>
                                <tr>
                                    <td>Artikel-Nummer</td>
                                    <td><input class="textfield_mod t_textfield" style="width:60px;margin-right:0px;" value="'.GetProperty("prefix_support").'-" readonly><input name="number" class="textfield_mod t_textfield" style="width:231px;margin-left:0px" placeholder="Nummer..." value="'.str_replace(GetProperty("prefix_support").'-','',$row['number']).'"></td>
                                </tr>
                                <tr>
                                    <td>Artikel-Bezeichnung</td>
                                    <td><textarea name="description" class="textarea_m t_textarea" placeholder="Bezeichnung...">'.$row['description'].'</textarea></td>
                                </tr>
                                <tr>
                                    <td>Mengeneinheit</td>
                                    <td>
                                        <select id="unit_list" class="selectbox_m t_selectbox" name="unit" onchange="UpdateUnit();">
                                            <option value="'.$row['unit'].'">['.$row['unit'].']</option>
                                            <option value="Stk.">[Stk.] St&uuml;ck</option>
                                            <option value="m">[m] Meter</option>
                                            <option value="cm">[cm] Zentimeter</option>
                                            <option value="mm">[mm] Milimeter</option>
                                            <option value="l">[l] Liter</option>
                                            <option value="kg">[kg] Kilogramm</option>
                                            <option value="g">[g] Gramm</option>
                                            <option value="m2">[m&sup2;] Quadratmeter</option>
                                            <option value="m3">[m&sup3;] Kubikmeter</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Preis</td>
                                    <td><input type="number" step="0.001" name="price" class="textfield_xs t_textfield" value="'.$row['price'].'"> &euro; f&uuml;r <input type="number"  step="0.01" name="per_item" class="textfield_xxs t_textfield" value="'.$row['per_item'].'"><output id="unit">'.$row['unit'].'</output></td>
                                </tr>
                                <tr>
                                    <td><br>Lager-H&ouml;chstbestand</td>
                                    <td><br><input type="number" step="0.01" name="max_stock" class="textfield_mod t_textfield" style="width:250px;margin-right:0px;" oninput="StockAmtCheck();" placeholder="Anzahl..." id="max_stock" value="'.$row['max_stock'].'"><output>Stk.</output></td>
                                </tr>
                                <tr>
                                    <td>Lager-Meldebestand</td>
                                    <td><input type="number" step="0.01" name="reorder_stock" class="textfield_mod t_textfield" style="width:250px;margin-right:0px;" oninput="StockAmtCheck();" placeholder="Anzahl..." id="reorder_stock" value="'.$row['reorder_stock'].'"><output>Stk.</output></td>
                                </tr>
                                <tr>
                                    <td>Lager-Sicherheitsbestand</td>
                                    <td><input type="number" step="0.01" name="security_stock" class="textfield_mod t_textfield" style="width:250px;margin-right:0px;" oninput="StockAmtCheck();" placeholder="Anzahl..." id="security_stock" value="'.$row['security_stock'].'"><output>Stk.</output></td>
                                </tr>
                                <tr>
                                    <td colspan=2>
                                        <center>
                                            <span style="color: #CC0000">
                                                <output id="out_stockdata_line1"></output><br>
                                                <output id="out_stockdata_line2"></output>
                                            </span>
                                        </center>
                                    </td>
                                </tr>
                                <tr>
                                    <td><br>Lager</td>
                                    <td>
                                        <br>
                                        <select name="storage_location" class="textfield_m t_textfield">
                                            <option value="'.$row['storage_location'].'" selected>'.$row['storage_location'].' - '.fetch("storage_locations","storage_name","storage_id",$row['storage_location']).'</option>
                                            ';
                                            $strSQLp = "SELECT * FROM storage_locations";
                                            $rsp=mysqli_query($link,$strSQLp);
                                            while($rowp=mysqli_fetch_assoc($rsp)) echo '<option value="'.$rowp['storage_id'].'">'.$rowp['storage_id'].' - '.$rowp['storage_name'].'</option>';
                                            echo '
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Hersteller-Firma</td>
                                    <td>
                                        <input name="company" id="producer" class="textfield_mod t_textfield" style="width:250px;margin-right:0px;" placeholder="Hersteller..." value="'.$row['production_company'].'">
                                        <select class="textfield_mod t_textfield" style="width:50px;margin-left:0px;" id="producer_list" oninput="AutoFillProducer();">
                                            <option value="none">&#9660; Bestehende Firma ausw&auml;hlen:</option>
                                            ';

                                            $strSQLp = "SELECT DISTINCT production_company AS company FROM products";
                                            $rsp=mysqli_query($link,$strSQLp);
                                            while($rowp=mysqli_fetch_assoc($rsp)) echo '<option value="'.$rowp['company'].'">'.$rowp['company'].'</option>';

                                            echo '
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Vertriebs-Firma</td>
                                    <td>
                                        <input name="retailer" id="retailer" class="textfield_mod t_textfield" style="width:250px;margin-right:0px;" placeholder="Vertreiber..." value="'.$row['retail_company'].'">
                                        <select class="textfield_mod t_textfield" style="width:50px;margin-left:0px;" id="retailer_list" oninput="AutoFillRetailer();">
                                            <option value="none">&#9660; Bestehende Firma ausw&auml;hlen:</option>
                                            ';

                                            $strSQLp = "SELECT DISTINCT retail_company AS company FROM products";
                                            $rsp=mysqli_query($link,$strSQLp);
                                            while($rowp=mysqli_fetch_assoc($rsp)) echo '<option value="'.$rowp['company'].'">'.$rowp['company'].'</option>';

                                            echo '
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Link/Bestellseite</td>
                                    <td><textarea name="link" class="textarea_m t_textarea" placeholder="http://...">'.$row['link'].'</textarea></td>
                                </tr>
                                <tr>
                                    <td>Dateien</td>
                                    <td>
                                        &nbsp;<label for="file" class="t_button">&nbsp;&nbsp;'.langlib("Datei(en) ausw&auml;hlen").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><br>
                                         <input type="file" id="file" name="files[]" multiple="multiple" hidden/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Produktbild</td>
                                    <td>
                                        <br>&nbsp;<label for="filesthumb" class="t_button">&nbsp;&nbsp;Titelbild ausw&auml;hlen&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><br>
                                         <input type="file" id="filesthumb" name="filesthumb[]" multiple="multiple" hidden/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Kommentar</td>
                                    <td><textarea name="comment" class="textarea_m t_textarea" placeholder="Kommentar...">'.$row['comment'].'</textarea></td>
                                </tr>
                                <tr>
                                    <td colspan=2><br><br><center><button class="button_m t_button" value="'.$row['number'].'" name="add_support_product" type="submit" id="continue_btn">Hilfsmittel aktualisieren</button></center></td>
                                </tr>
                            </table>
                        </form>
                    </center>
                ';
            }

        }

    }
    if(isset($_GET['delete']))
    {
        echo '<h2>Hilfsmittel l&ouml;schen</h2>';

        $redirect = (SubStringFind(basename($_SERVER["REQUEST_URI"], '.php'),'=')) ? substr(basename($_SERVER["REQUEST_URI"], '.php'), 0, strpos(basename($_SERVER["REQUEST_URI"], '.php'), '=')) : basename($_SERVER["REQUEST_URI"], '.php');

        echo '
            <center>
                <table class="data_inputbox_m">
                    <tr>
                        <td><center><h3>Produkt ausw&auml;hlen:</h3></center></td>
                    </tr>
                    <tr>
                        <td>
                            <br><br>
                            <input type="hidden" id="extension" value="'.$redirect.'"/>
                            <select class="selectbox_m t_selectbox" id="product_list" onchange="ProductSelectEdit();">
                                <option value="none">&#9660; Hilfsprodukt ausw&auml;hlen</option>
                        ';
                        $prefix = GetProperty("prefix_support");
                        $strSQL = "SELECT * FROM products WHERE number LIKE '$prefix%'";
                        $rs=mysqli_query($link,$strSQL);
                        while($row=mysqli_fetch_assoc($rs))
                        {
                            echo '<option value="'.$row['number'].'">'.$row['number'].' - '.$row['name'].'</option>';
                        }
                        echo $_GET['edit'];
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

        if($_GET['delete']!=null)
        {
            $number=$_GET['delete'];
            $strSQL = "SELECT * FROM products WHERE number LIKE '$number'";
            $rs=mysqli_query($link,$strSQL);
            while($row=mysqli_fetch_assoc($rs))
            {
                echo '
                    <h2>Wollen Sie dieses Hilfsmittel wirklich l&ouml;schen?</h2>
                    <center>
                        <table class="data_inputbox_m">
                            <tr>
                                <td id="shaded_cell">Artikel-Name</td>
                                <td id="shaded_cell">'.$row['name'].'</td>
                            </tr>
                            <tr>
                                <td>Artikel-Nummer</td>
                                <td>'.$row['number'].'</td>
                            </tr>
                            <tr>
                                <td id="shaded_cell">Artikel-Bezeichnung</td>
                                <td id="shaded_cell">'.nl2br($row['description']).'</td>
                            </tr>
                            <tr>
                                <td>Mengeneinheit</td>
                                <td>'.$row['unit'].'</td>
                            </tr>
                            <tr>
                                <td id="shaded_cell">Preis</td>
                                <td id="shaded_cell">'.number_format($row['price'],2).' &euro; f&uuml;r '.$row['per_item'].' '.$row['unit'].'</td>
                            </tr>
                            <tr>
                                <td>Bestellgr&ouml;&szlig;e</td>
                                <td>'.$row['order_size'].'</td>
                            </tr>
                            <tr>
                                <td id="shaded_cell">Hersteller-Firma</td>
                                <td id="shaded_cell">'.$row['production_company'].'</td>
                            </tr>
                            <tr>
                                <td>Vertriebs-Firma</td>
                                <td>'.$row['retail_company'].'</td>
                            </tr>
                            <tr>
                                <td id="shaded_cell">Dateien</td>
                                <td id="shaded_cell">
                                   '.DirectoryListing('files/products/support_products/'.$row['number'].'/').'
                                </td>
                            </tr>
                            <tr>
                                <td>Kommentar</td>
                                <td>'.nl2br($row['comment']).'</td>
                            </tr>
                        </table>
                        <br><br>
                        <form action="/products" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                            <button class="t_button button_xl" type="submit" name="delete_support_item" value="'.$row['number'].'"><span style="color: #CC0000">L&ouml;schen</span></button><br>
                            <a href="/products?support&show"><button class="t_button button_xl" type="button">Abbrechen</button></a>
                        </form>
                    </center>
                ';
            }
        }
    }
?>