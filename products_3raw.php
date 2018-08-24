<?php
    if(isset($_GET['show']))
    {
        echo '
            <h2>Rohteile</h2>
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

                    $prefix = GetProperty("prefix_raw");
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
                                        <a href="products?raw&edit='.$row['number'].'"><span style="color: #FFA500">&#128736; Bearbeiten<br></span></a>
                                        <a href="products?raw&delete='.$row['number'].'"><span style="color: #CC0000">&#x2718; L&ouml;schen<br> </span></a>
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
                                        '.DirectoryListing('files/products/raw_products/'.$row['number'].'/').'
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
                                                    <a href="products?raw&edit='.$row['number'].'"><span style="color: #FFA500">&#128736; Bearbeiten<br></span></a>
                                                    <a href="products?raw&delete='.$row['number'].'"><span style="color: #CC0000">&#x2718; L&ouml;schen<br> </span></a>
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
                                                    <img src="/files/products/raw_products/'.$row['number'].'/'.$row['product_image'].'" alt="" style="width:100px;"/>
                                                </td>
                                                <td>
                                                    '.DirectoryListing('files/products/raw_products/'.$row['number'].'/').'
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
            <h2>Neues Rohteil eintragen</h2>
            <center>
                <form action="/products" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <table class="data_inputbox_m">
                        <tr>
                            <td>Artikel-Name</td>
                            <td><input name="name" class="textfield_m t_textfield" placeholder="Name..."></td>
                        </tr>
                        <tr>
                            <td id="shaded_cell">Artikel-Nummer</td>
                            <td id="shaded_cell">
                                <input type="checkbox" id="enableDecAssist" name="declarationAssistent" onchange="EnableDeclarationAssistent()"><label class="checkbox_label" for="enableDecAssist"></label> Deklarations-Assistenten Verwenden<br>
                                <div id="declarationAssist_disabled">
                                    <input class="textfield_mod t_textfield" style="width:60px;margin-right:0px;" value="'.GetProperty("prefix_raw").'-" readonly>
                                    <input name="numberDefault" class="textfield_mod t_textfield" style="width:231px;margin-left:0px" placeholder="Nummer...">
                                </div>
                                <div id="declarationAssist_enabled" style="display:none">
                                    <input type="hidden" value="'.GetProperty("prefix_raw").'" id="product_prefix"/>
                                    <input type="hidden" value="" id="group1Value"/>
                                    <input type="hidden" value="" id="group2Value"/>
                                    <input name="numberAssist" class="textfield_m t_textfield" id="product_number" readonly value="'.GetProperty("prefix_raw").'-????XXXX"><br>
                                    <select id="subGroup1" class="selectbox_m t_selectbox" onchange="PopulateSubGroup2()">
                                        <option value="" selected disabled>Untergruppe 1</option>
                                        ';
                                        $strSQL = "SELECT * FROM declarations WHERE subgroup_type = '1'";
                                        $rs=mysqli_query($link,$strSQL);
                                        while($row=mysqli_fetch_assoc($rs))
                                        {
                                            $subgroups='';
                                            $parent = $row['subgroup_sign'];
                                            $strSQLS = "SELECT * FROM declarations WHERE parent_subgroup = '$parent'";
                                            $rsS=mysqli_query($link,$strSQLS);
                                            while($rowS=mysqli_fetch_assoc($rsS))
                                            {
                                                $subgroups .= $rowS['subgroup_sign'].'||'.$rowS['subgroup_name'].'|-|';
                                            }
                                            echo '<option value="'.$row['subgroup_sign'].'|-|'.$row['has_subgroups'].'|-|'.$subgroups.'">'.$row['subgroup_name'].'</option>';
                                        }
                                        echo '
                                    </select>
                                    <br>
                                    <select id="subGroup2" class="selectbox_m t_selectbox" onchange="UpdateSubGroup2()" style="display:none">
                                    </select>
                                    <input name="number" class="textfield_m t_textfield" id="runnumber" placeholder="Fortlaufende Nummer..." oninput="UpdateSubGroupNumber()"><br>
                                </div>
                            </td>
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
                            <td colspan=2><br><br><center><button class="button_m t_button" id="continue_btn" value="new" name="add_raw_product" type="submit">Rohteil eintragen</button></center></td>
                        </tr>
                    </table>
                </form>
            </center>
        ';
    }
    if(isset($_GET['edit']))
    {
        echo '<h2>Rohteil bearbeiten</h2>';

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
                                <option value="none">&#9660; Rohteil ausw&auml;hlen</option>
                        ';
                        $prefix = GetProperty("prefix_raw");
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
                                    <td id="shaded_cell">Artikel-Nummer</td>
                                    <td id="shaded_cell">
                                        <input type="checkbox" id="enableDecAssist" name="declarationAssistent" onchange="EnableDeclarationAssistent()"><label class="checkbox_label" for="enableDecAssist"></label> Deklarations-Assistenten Verwenden<br>
                                        <div id="declarationAssist_disabled">
                                            <input class="textfield_mod t_textfield" style="width:60px;margin-right:0px;" value="'.GetProperty("prefix_raw").'-" readonly>
                                            <input name="numberDefault" class="textfield_mod t_textfield" style="width:231px;margin-left:0px" placeholder="Nummer..." value="'.str_replace(GetProperty("prefix_raw").'-','',$row['number']).'">
                                        </div>
                                        <div id="declarationAssist_enabled" style="display:none">
                                            <input type="hidden" value="'.GetProperty("prefix_raw").'" id="product_prefix"/>
                                            <input type="hidden" value="" id="group1Value"/>
                                            <input type="hidden" value="" id="group2Value"/>
                                            <input name="numberAssist" class="textfield_m t_textfield" id="product_number" readonly value="'.$row['number'].'"><br>
                                            <select id="subGroup1" class="selectbox_m t_selectbox" onchange="PopulateSubGroup2()">
                                                <option value="" selected disabled>Untergruppe 1</option>
                                                ';
                                                $strSQLG = "SELECT * FROM declarations WHERE subgroup_type = '1'";
                                                $rsG=mysqli_query($link,$strSQLG);
                                                while($rowG=mysqli_fetch_assoc($rsG))
                                                {
                                                    $subgroups='';
                                                    $parent = $rowG['subgroup_sign'];
                                                    $strSQLS = "SELECT * FROM declarations WHERE parent_subgroup = '$parent'";
                                                    $rsS=mysqli_query($link,$strSQLS);
                                                    while($rowS=mysqli_fetch_assoc($rsS))
                                                    {
                                                        $subgroups .= $rowS['subgroup_sign'].'||'.$rowS['subgroup_name'].'|-|';
                                                    }
                                                    echo '<option value="'.$rowG['subgroup_sign'].'|-|'.$rowG['has_subgroups'].'|-|'.$subgroups.'">'.$rowG['subgroup_name'].'</option>';
                                                }
                                                echo '
                                            </select>
                                            <br>
                                            <select id="subGroup2" class="selectbox_m t_selectbox" onchange="UpdateSubGroup2()" style="display:none">
                                            </select>
                                            <input name="number" class="textfield_m t_textfield" id="runnumber" placeholder="Fortlaufende Nummer..." oninput="UpdateSubGroupNumber()"><br>
                                        </div>
                                    </td>
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
                                    <td colspan=2><br><br><center><button class="button_m t_button" id="continue_btn" value="'.$row['number'].'" name="add_raw_product" type="submit">Rohteil aktualisieren</button></center></td>
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
        echo '<h2>Rohteil l&ouml;schen</h2>';

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
                                <option value="none">&#9660; Rohteil ausw&auml;hlen</option>
                        ';
                        $prefix = GetProperty("prefix_raw");
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
                    <h2>Wollen Sie dieses Rohteil wirklich l&ouml;schen?</h2>
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
                                   '.DirectoryListing('files/products/raw_products/'.$row['number'].'/').'
                                </td>
                            </tr>
                            <tr>
                                <td>Kommentar</td>
                                <td>'.nl2br($row['comment']).'</td>
                            </tr>
                        </table>
                        <br><br>
                        <form action="/products" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                            <button class="t_button button_xl" type="submit" name="delete_raw_item" value="'.$row['number'].'"><span style="color: #CC0000">L&ouml;schen</span></button><br>
                            <a href="/products?raw&show"><button class="t_button button_xl" type="button">Abbrechen</button></a>
                        </form>
                    </center>
                ';
            }
        }
    }
    if(isset($_GET['declaration']))
    {
        echo '<h2>Rohteil Deklarationsassistent</h2>';
        echo '
            Deklarationen k&ouml;nnen verwendet werden um Produkte besser Systematisch zu Organisieren.<br>
            Mithilfe k&oumlnnen zus&auml;tzliche zwei Produktuntergruppen hinzugef&uuml;gt werden, neben der Produkt-Hauptgruppe (Hier "'.GetProperty("prefix_raw").'")<br><br>
            Eine Untergruppe ist eine zweistellige Zeichenfolge (0-9, A-Z)<br><br>
            Beim Anlegen eines neuen Produktes kann ausgew&auml;hlt werden, ob die Deklaration verwendet werden soll.
        ';

        echo '
            <center>
                <h1>
                    '.GetProperty("prefix_raw").' -
                    <output id="out_subgroup1" style="color:#FFA500">??</output>
                    <output id="out_subgroup2">??</output>
                    XXXX
                </h1>
                <h4>Hauptgruppe - Untergruppe 1 - Untergruppe 2 - Fortlaufende Nummer</h4>
                <br><br>

                <a href="#sg1"><button onfocus="document.getElementById(\'out_subgroup1\').style.color = \'#FFA500\';document.getElementById(\'out_subgroup2\').style.color = \'#000000\';" type="button" class="button_m t_button">Neue<br>Untergruppe-1<br>hinzuf&uumlgen</button></a>
                <a href="#sg2"><button onfocus="document.getElementById(\'out_subgroup2\').style.color = \'#FFA500\';document.getElementById(\'out_subgroup1\').style.color = \'#000000\';" type="button" class="button_m t_button">Neue<br>Untergruppe-2<br>hinzuf&uumlgen</button></a>

                <br><br>
                <div id="sg1" class="target_div">
                    <form action="/'.basename($_SERVER["REQUEST_URI"], '.php').'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                        <input type="hidden" value="'.GetProperty("prefix_raw").'" name="productGroup"/>
                        <table class="content_table">
                            <tr>
                                <td colspan=2 style="text-align:center;" id="shaded_cell"><h3>Untergruppe 1</h3></td>
                            </tr>
                            <tr>
                                <td>Kurzzeichen:</td>
                                <td><input required oninput="UpdateDeclarationPreview1()" name="subGroup1Short" class="textfield_xxs t_textfield" id="subGroup1Short" maxlength="2"></td>
                            </tr>
                            <tr>
                                <td>Bezeichnung:</td>
                                <td><input required name="subGroup1Name" class="textfield_s t_textfield"></td>
                            </tr>
                            <tr>
                                <td>Besitzt weitere Untergruppen:</td>
                                <td>
                                    <input type="checkbox" name="hasSubGroups" id="hasSubGroups" onchange="UpdateDeclarationPreview1()" checked><label class="checkbox_label" for="hasSubGroups"></label>
                                </td>
                            </tr>
                            <tr>
                                <td colspan=2 style="text-align:center;">
                                    <br>
                                    <button type="submit" class="button_m t_button" name="add_subgroup1">Untergruppe hinzuf&uuml;gen</button>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
                <div id="sg2" class="target_div">
                    <form action="/'.basename($_SERVER["REQUEST_URI"], '.php').'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                        <input type="hidden" value="'.GetProperty("prefix_raw").'" name="productGroup"/>
                        <table class="content_table">
                            <tr>
                                <td colspan=2 style="text-align:center;" id="shaded_cell"><h3>Untergruppe 2</h3></td>
                            </tr>
                            <tr>
                                <td>Elterngruppe:</td>
                                <td>
                                    <select required onchange="UpdateDeclarationPreview2()" name="parentGroup" id="subGroup1Select" class="textfield_s t_selectbox">
                                        <option value="" selected disabled>--- Ausw&auml;hlen ---</option>
                                        ';
                                            $strSQL = "SELECT * FROM declarations WHERE subgroup_type = '1' AND has_subgroups = '1'";
                                            $rs=mysqli_query($link,$strSQL);
                                            while($row=mysqli_fetch_assoc($rs)) echo '<option value="'.$row['subgroup_sign'].'">'.$row['subgroup_sign'].' - '.$row['subgroup_name'].'</option>';
                                        echo '
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Kurzzeichen:</td>
                                <td><input required oninput="UpdateDeclarationPreview2()" name="subGroup2Short" class="textfield_xxs t_textfield" id="subGroup2Short" maxlength="2"></td>
                            </tr>
                            <tr>
                                <td>Bezeichnung:</td>
                                <td><input required name="subGroup2Name" class="textfield_s t_textfield" ></td>
                            </tr>
                            <tr>
                                <td colspan=2 style="text-align:center;">
                                    <br>
                                    <button type="submit" class="button_m t_button" name="add_subgroup2">Untergruppe hinzuf&uuml;gen</button>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </center>
        ';
    }
?>