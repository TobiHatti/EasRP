<?php
    if(isset($_GET['show']))
    {
        echo '
            <h2>Produkte</h2>
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
                                <td>Bestandteile</td>
                                <td>Attribute</td>
                                <td>Preise</td>
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
                                <td>Beschreibung</td>
                                <td>Preise</td>
                            </tr>
                        ';
                    }


                    $prefix = GetProperty("prefix_product");
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
                                        <a href="products?products&edit='.$row['number'].'"><span style="color: #FFA500">&#128736; Bearbeiten<br></span></a>
                                        <a href="products?products&delete='.$row['number'].'"><span style="color: #CC0000">&#x2718; L&ouml;schen<br> </span></a>
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
                                        <b>Halbprodukte:</b><br>
                                        '.ProductContains($row['number'],'semiproduct').'<br>
                                        <b>Rohteile:</b><br>
                                        '.ProductContains($row['number'],'raw').'<br>
                                        <b>Hilfsmittel:</b><br>
                                        '.ProductContains($row['number'],'support').'
                                    </td>
                                    <td '.$style_id.'>
                                        '.ListAttributes($row['attributes']).'
                                    </td>
                                    <td '.$style_id.'>
                                        <b>Einkaufspreis Summe:</b><br>
                                        '.number_format(ProductPrice($row['number']),3).'&euro;<br><br>
                                        <b>Verkaufspreis:</b><br>
                                        '.number_format($row['resell_price'],2).'&euro;<br><br>
                                        <b>Gewinn:</b><br>
                                        '.number_format($row['resell_price'] - ProductPrice($row['number']),3).'&euro;
                                    </td>
                                    <td '.$style_id.'>
                                        '.DirectoryListing('files/products/products/'.$row['number'].'/').'
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
                                        <i><span style="color: #595959">'.nl2br($row['description']).'</span></i>
                                    </td>
                                    <td '.$style_id.'>
                                        <u>'.$row['name'].'</u>
                                    </td>
                                    <td '.$style_id.'>
                                        <b><span style="color: #CC0000">Einkauf</span>/<span style="color: #FFA500">Verkauf</span>/<span style="color: #32CD32">Gewinn</span>:&nbsp;&nbsp; </b>
                                        <span style="color: #CC0000">'.number_format(ProductPrice($row['number']),3).'&euro;</span> /
                                        <span style="color: #FFA500">'.number_format($row['resell_price'],2).'&euro;</span> /
                                        <span style="color: #32CD32">'.number_format($row['resell_price'] - ProductPrice($row['number']),3).'&euro;</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan=4 class="foldup_cell" id="detail'.$row['number'].'" '.$style_color.'>
                                        <table style="width:100%;">
                                            <tr>
                                                <td>
                                                    <a href="products?products&edit='.$row['number'].'"><span style="color: #FFA500">&#128736; Bearbeiten<br></span></a>
                                                    <a href="products?products&delete='.$row['number'].'"><span style="color: #CC0000">&#x2718; L&ouml;schen<br> </span></a>
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
                                                    <b>Halbprodukte:</b><br>
                                                    '.ProductContains($row['number'],'semiproduct').'<br>
                                                    <b>Rohteile:</b><br>
                                                    '.ProductContains($row['number'],'raw').'<br>
                                                    <b>Hilfsmittel:</b><br>
                                                    '.ProductContains($row['number'],'support').'
                                                </td>
                                                <td>
                                                    '.ListAttributes($row['attributes']).'
                                                </td>
                                                <td>
                                                    <b>Einkaufspreis Summe:</b><br>
                                                    '.number_format(ProductPrice($row['number']),3).'&euro;<br><br>
                                                    <b>Verkaufspreis:</b><br>
                                                    '.number_format($row['resell_price'],2).'&euro;<br><br>
                                                    <b>Gewinn:</b><br>
                                                    '.number_format($row['resell_price'] - ProductPrice($row['number']),3).'&euro;
                                                </td>
                                                <td>
                                                    Lager: '.$row['storage_location'].'
                                                </td>
                                                <td>
                                                    <img src="/files/products/products/'.$row['number'].'/'.$row['product_image'].'" alt="" style="width:100px;"/>
                                                </td>
                                                <td>
                                                    '.DirectoryListing('files/products/products/'.$row['number'].'/').'
                                                </td>
                                            </tr>
                                            07
                                            <tr>
                                                <td colspan=7>
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
            <h2>Neues Produkt eintragen</h2>
            <center>
                <form action="/products" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <table class="data_inputbox_m">
                        <tr>
                            <td>Artikel-Name</td>
                            <td><input name="name" class="textfield_m t_textfield" placeholder="Name..."></td>
                        </tr>
                        <tr>
                            <td>Artikel-Nummer</td>
                            <td><input class="textfield_mod t_textfield" style="width:60px;margin-right:0px;" value="'.GetProperty("prefix_product").'-" readonly><input name="number" class="textfield_mod t_textfield" style="width:231px;margin-left:0px" placeholder="Nummer..."></td>
                        </tr>
                        <tr>
                            <td>Artikel-Bezeichnung</td>
                            <td><textarea name="description" class="textarea_m t_textarea" placeholder="Bezeichnung..."></textarea></td>
                        </tr>
                        <tr>
                            <td>Gewicht [kg]</td>
                            <td><input name="weight" type="number" step="0.001" class="textfield_m t_textfield" placeholder="Gewicht in kg"></td>
                        </tr>
                        <tr>
                            <td>Verkaufbar</td>
                            <td><input checked type="checkbox" name="sellable" id="check_sellable" onchange="UpdateSellableText();"><label class="checkbox_label" for="check_sellable"></label><output id="out_sellable">Zum Verkauf freigegeben</output></td>
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
                            <td>Verkaufspreis (inkl. USt.)</td>
                            <td><input name="resell_price" type="number" step="0.01" class="textfield_m t_textfield" placeholder="Verkaufspreis..."></td>
                        </tr>
                        <tr>
                            <td>Attribute<div title="Es k&ouml;nnen mehrere Zusatzfelder f&uuml;r dieses Produkt eingetragen werden. Einzelne Felder mit &quot;;&quot; trennen" style="display:inline; cursor:pointer">&#128712;</div></td>
                            <td><textarea name="attributes" class="textarea_m t_textarea" placeholder="z.B.: Farbe;Gr&ouml;&szlig;e;Qualit&auml;t;"></textarea></td>
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
                    </table>
                    <br><br>
                    <table class="data_inputbox_m">
                        <tr>
                            <td colspan=3><h2><center>Artikel ausw&auml;hlen:</center></h2></td>
                        </tr>
                        <tr>
                            <td colspan=3><h3><center>Halbprodukte:</center></h3></td>
                        </tr>
                    ';
                    $prefix_raw = GetProperty("prefix_semiproduct");
                    $i=0;
                    $strSQL = "SELECT * FROM products WHERE number LIKE '$prefix_raw%'";
                    $rs=mysqli_query($link,$strSQL);
                    while($row=mysqli_fetch_assoc($rs))
                    {
                        $style_id = ($i++%2==0) ? 'id="shaded_cell"' : '';
                        echo '
                            <tr>
                                <td '.$style_id.' style="width:70px;">
                                <input name="amt'.$row['number'].'" class="textfield_xxs t_textfield" type="number" value="1" id="amt'.$row['number'].'" style="margin:0px; display:none;">
                                </td>
                                <td '.$style_id.'>
                                    <input type="checkbox" name="check'.$row['number'].'" id="check'.$row['number'].'" onchange="DisplayAmt'.str_replace('-','',$row['number']).'();"><label class="checkbox_label" for="check'.$row['number'].'"></label>
                                </td>
                                <td '.$style_id.' style="width:300px;">
                                    '.$row['number'].' - '.$row['name'].'
                                </td>
                            </tr>

                            <script>
                            function DisplayAmt'.str_replace('-','',$row['number']).'()
                            {
                                if(document.getElementById("check'.$row['number'].'").checked) document.getElementById("amt'.$row['number'].'").style.display = "block";
                                else document.getElementById("amt'.$row['number'].'").style.display = "none";
                            }
                            </script>
                        ';
                    }
                    echo '
                        <tr>
                            <td colspan=3><h3><center>Rohteile:</center></h3></td>
                        </tr>
                    ';
                    $prefix_raw = GetProperty("prefix_raw");
                    $i=0;
                    $strSQL = "SELECT * FROM products WHERE number LIKE '$prefix_raw%'";
                    $rs=mysqli_query($link,$strSQL);
                    while($row=mysqli_fetch_assoc($rs))
                    {
                        $style_id = ($i++%2==0) ? 'id="shaded_cell"' : '';
                        echo '
                            <tr>
                                <td '.$style_id.' style="width:70px;">
                                <input name="amt'.$row['number'].'" class="textfield_xxs t_textfield" type="number" value="1" id="amt'.$row['number'].'" style="margin:0px; display:none;">
                                </td>
                                <td '.$style_id.'>
                                    <input type="checkbox" name="check'.$row['number'].'" id="check'.$row['number'].'" onchange="DisplayAmt'.str_replace('-','',$row['number']).'();"><label class="checkbox_label" for="check'.$row['number'].'"></label>
                                </td>
                                <td '.$style_id.' style="width:300px;">
                                    '.$row['number'].' - '.$row['name'].'
                                </td>
                            </tr>

                            <script>
                            function DisplayAmt'.str_replace('-','',$row['number']).'()
                            {
                                if(document.getElementById("check'.$row['number'].'").checked) document.getElementById("amt'.$row['number'].'").style.display = "block";
                                else document.getElementById("amt'.$row['number'].'").style.display = "none";
                            }
                            </script>
                        ';
                    }
                    echo '
                        <tr>
                            <td colspan=3><h3><center>Verwendbare Hilfsmittel:</center></h3></td>
                        </tr>
                    ';
                    $prefix_support = GetProperty("prefix_support");
                    $i=0;
                    $strSQL = "SELECT * FROM products WHERE number LIKE '$prefix_support%'";
                    $rs=mysqli_query($link,$strSQL);
                    while($row=mysqli_fetch_assoc($rs))
                    {
                        $style_id = ($i++%2==0) ? 'id="shaded_cell"' : '';
                        echo '
                            <tr>
                                <td '.$style_id.' style="width:70px;">
                                <input name="amt'.$row['number'].'" class="textfield_xxs t_textfield" type="number" value="1" id="amt'.$row['number'].'" style="margin:0px; display:none;">
                                </td>
                                <td '.$style_id.'>
                                    <input type="checkbox" name="check'.$row['number'].'" id="check'.$row['number'].'" onchange="DisplayAmt'.str_replace('-','',$row['number']).'();"><label class="checkbox_label" for="check'.$row['number'].'"></label>
                                </td>
                                <td '.$style_id.' style="width:300px;">
                                    '.$row['number'].' - '.$row['name'].'
                                </td>
                            </tr>

                            <script>
                            function DisplayAmt'.str_replace('-','',$row['number']).'()
                            {
                                if(document.getElementById("check'.$row['number'].'").checked) document.getElementById("amt'.$row['number'].'").style.display = "block";
                                else document.getElementById("amt'.$row['number'].'").style.display = "none";
                            }
                            </script>
                        ';
                    }
                    echo '
                        <tr>
                            <td colspan=3>
                                <br><br>
                                <center>
                                    <button type="submit" name="add_product" value="new" class="button_m t_button">Produkt hinzuf&uuml;gen</button>
                                </center>
                            </td>
                        </tr>
                    </table>
                </center>
            </form>
        ';
    }
    if(isset($_GET['edit']))
    {
        echo '<h2>Produkt bearbeiten</h2>';

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
                                <option value="none">&#9660; Produkt ausw&auml;hlen</option>
                        ';
                        $prefix = GetProperty("prefix_product");
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
                                    <td><input class="textfield_mod t_textfield" style="width:60px;margin-right:0px;" value="'.GetProperty("prefix_product").'-" readonly><input name="number" class="textfield_mod t_textfield" style="width:231px;margin-left:0px" placeholder="Nummer..." value="'.str_replace(GetProperty("prefix_product").'-','',$row['number']).'"></td>
                                </tr>
                                <tr>
                                    <td>Artikel-Bezeichnung</td>
                                    <td><textarea name="description" class="textarea_m t_textarea" placeholder="Bezeichnung...">'.$row['description'].'</textarea></td>
                                </tr>
                                <tr>
                                    <td>Gewicht [kg]</td>
                                    <td><input name="weight" type="number" step="0.001" class="textfield_m t_textfield" placeholder="Gewicht in kg" value="'.$row['weight'].'"></td>
                                </tr>
                                <tr>
                                    <td>Verkaufbar</td>
                                    <td><input '.(($row['sellable']) ? 'checked' : '' ).' type="checkbox" name="sellable" id="check_sellable" onchange="UpdateSellableText();"><label class="checkbox_label" for="check_sellable"></label><output id="out_sellable">Zum Verkauf freigegeben</output></td>
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
                                    <td>Verkaufspreis (inkl. USt.)</td>
                                    <td><input name="resell_price" type="number" step="0.01" class="textfield_m t_textfield" placeholder="Verkaufspreis..." value="'.number_format($row['resell_price'],2).'"></td>
                                </tr>
                                <tr>
                                    <td>Attribute<div title="Es k&ouml;nnen mehrere Zusatzfelder f&uuml;r dieses Produkt eingetragen werden. Einzelne Felder mit &quot;;&quot; trennen" style="display:inline; cursor:pointer">&#128712;</div></td>
                                    <td><textarea name="attributes" class="textarea_m t_textarea" placeholder="z.B.: Farbe;Gr&ouml;&szlig;e;Qualit&auml;t;">'.$row['attributes'].'</textarea></td>
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
                            </table>
                            <br><br>
                            <table class="data_inputbox_m">
                                <tr>
                                    <td colspan=3><h2><center>Artikel ausw&auml;hlen:</center></h2></td>
                                </tr>
                                <tr>
                                    <td colspan=3><h3><center>Halbprodukte:</center></h3></td>
                                </tr>
                                ';

                            $prefix_raw = GetProperty("prefix_semiproduct");
                            $i=0;
                            $strSQL = "SELECT * FROM products WHERE number LIKE '$prefix_raw%'";
                            $rs=mysqli_query($link,$strSQL);
                            while($row=mysqli_fetch_assoc($rs))
                            {
                                $style_id = ($i++%2==0) ? 'id="shaded_cell"' : '';

                                $item=$row['number'];
                                $checked='';
                                $amt = 1;
                                $display='none';
                                if(MySQLSkalar("SELECT quantity AS x FROM product_contains WHERE parent = '$number' AND child LIKE '$item'")>0)
                                {
                                    $checked='checked';
                                    $display='block';
                                    $amt = MySQLSkalar("SELECT quantity AS x FROM product_contains WHERE parent = '$number' AND child LIKE '$item'");
                                }

                                echo '
                                    <tr>
                                        <td '.$style_id.' style="width:70px;">
                                        <input name="amt'.$row['number'].'" class="textfield_xxs t_textfield" type="number" value="'.$amt.'" id="amt'.$row['number'].'" style="margin:0px; display:'.$display.';">
                                        </td>
                                        <td '.$style_id.'>
                                            <input '.$checked.' type="checkbox" name="check'.$row['number'].'" id="check'.$row['number'].'" onchange="DisplayAmt'.str_replace('-','',$row['number']).'();"><label class="checkbox_label" for="check'.$row['number'].'"></label>
                                        </td>
                                        <td '.$style_id.' style="width:300px;">
                                            '.$row['number'].' - '.$row['name'].'
                                        </td>
                                    </tr>

                                    <script>
                                    function DisplayAmt'.str_replace('-','',$row['number']).'()
                                    {
                                        if(document.getElementById("check'.$row['number'].'").checked) document.getElementById("amt'.$row['number'].'").style.display = "block";
                                        else document.getElementById("amt'.$row['number'].'").style.display = "none";
                                    }
                                    </script>
                                ';
                            }
                            echo '
                                <tr>
                                    <td colspan=3><h3><center>Rohteile:</center></h3></td>
                                </tr>
                            ';

                            $prefix_raw = GetProperty("prefix_raw");
                            $i=0;
                            $strSQL = "SELECT * FROM products WHERE number LIKE '$prefix_raw%'";
                            $rs=mysqli_query($link,$strSQL);
                            while($row=mysqli_fetch_assoc($rs))
                            {
                                $style_id = ($i++%2==0) ? 'id="shaded_cell"' : '';

                                $item=$row['number'];
                                $checked='';
                                $amt = 1;
                                $display='none';
                                if(MySQLSkalar("SELECT quantity AS x FROM product_contains WHERE parent = '$number' AND child LIKE '$item'")>0)
                                {
                                    $checked='checked';
                                    $display='block';
                                    $amt = MySQLSkalar("SELECT quantity AS x FROM product_contains WHERE parent = '$number' AND child LIKE '$item'");
                                }

                                echo '
                                    <tr>
                                        <td '.$style_id.' style="width:70px;">
                                        <input name="amt'.$row['number'].'" class="textfield_xxs t_textfield" type="number" value="'.$amt.'" id="amt'.$row['number'].'" style="margin:0px; display:'.$display.';">
                                        </td>
                                        <td '.$style_id.'>
                                            <input '.$checked.' type="checkbox" name="check'.$row['number'].'" id="check'.$row['number'].'" onchange="DisplayAmt'.str_replace('-','',$row['number']).'();"><label class="checkbox_label" for="check'.$row['number'].'"></label>
                                        </td>
                                        <td '.$style_id.' style="width:300px;">
                                            '.$row['number'].' - '.$row['name'].'
                                        </td>
                                    </tr>

                                    <script>
                                    function DisplayAmt'.str_replace('-','',$row['number']).'()
                                    {
                                        if(document.getElementById("check'.$row['number'].'").checked) document.getElementById("amt'.$row['number'].'").style.display = "block";
                                        else document.getElementById("amt'.$row['number'].'").style.display = "none";
                                    }
                                    </script>
                                ';
                            }
                            echo '
                                <tr>
                                    <td colspan=3><h3><center>Verwendbare Hilfsmittel:</center></h3></td>
                                </tr>
                            ';
                            $prefix_support = GetProperty("prefix_support");
                            $i=0;
                            $strSQL = "SELECT * FROM products WHERE number LIKE '$prefix_support%'";
                            $rs=mysqli_query($link,$strSQL);
                            while($row=mysqli_fetch_assoc($rs))
                            {
                                $item=$row['number'];
                                $checked='';
                                $amt = 1;
                                $display='none';
                                $style_id = ($i++%2==0) ? 'id="shaded_cell"' : '';
                                if(MySQLSkalar("SELECT quantity AS x FROM product_contains WHERE parent = '$number' AND child LIKE '$item'")>0)
                                {
                                    $checked='checked';
                                    $display='block';
                                    $amt = MySQLSkalar("SELECT quantity AS x FROM product_contains WHERE parent = '$number' AND child LIKE '$item'");
                                }

                                echo '
                                    <tr>
                                        <td '.$style_id.' style="width:70px;">
                                        <input name="amt'.$row['number'].'" class="textfield_xxs t_textfield" type="number" value="'.$amt.'" id="amt'.$row['number'].'" style="margin:0px; display:'.$display.';">
                                        </td>
                                        <td '.$style_id.'>
                                            <input '.$checked.' type="checkbox" name="check'.$row['number'].'" id="check'.$row['number'].'" onchange="DisplayAmt'.str_replace('-','',$row['number']).'();"><label class="checkbox_label" for="check'.$row['number'].'"></label>
                                        </td>
                                        <td '.$style_id.' style="width:300px;">
                                            '.$row['number'].' - '.$row['name'].'
                                        </td>
                                    </tr>

                                    <script>
                                    function DisplayAmt'.str_replace('-','',$row['number']).'()
                                    {
                                        if(document.getElementById("check'.$row['number'].'").checked) document.getElementById("amt'.$row['number'].'").style.display = "block";
                                        else document.getElementById("amt'.$row['number'].'").style.display = "none";
                                    }
                                    </script>
                                ';
                            }
                            echo '
                                <tr>
                                    <td colspan=3>
                                        <br><br>
                                        <center>
                                            <button type="submit" name="add_product" value="'.$number.'" class="button_m t_button">Produkt aktualisieren</button>
                                        </center>
                                    </td>
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
        echo '<h2>Produkt l&ouml;schen</h2>';

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
                                <option value="none">&#9660; Produkt ausw&auml;hlen</option>
                        ';
                        $prefix = GetProperty("prefix_product");
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
                    <h2>Wollen Sie dieses Produkt wirklich l&ouml;schen?</h2>
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
                                <td id="shaded_cell">Verkaufspreis</td>
                                <td id="shaded_cell">'.number_format($row['resell_price'],2).' &euro;</td>
                            </tr>
                            <tr>
                                <td>Attribute</td>
                                <td>'.ListAttributes($row['attributes']).'</td>
                            </tr>
                            <tr>
                                <td id="shaded_cell">Dateien</td>
                                <td id="shaded_cell">
                                   '.DirectoryListing('files/products/products/'.$row['number'].'/').'
                                </td>
                            </tr>
                            <tr>
                                <td>Kommentar</td>
                                <td>'.nl2br($row['comment']).'</td>
                            </tr>
                        </table>
                        <br><br>
                        <form action="/products" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                            <button class="t_button button_xl" type="submit" name="delete_product" value="'.$row['number'].'"><span style="color: #CC0000">L&ouml;schen</span></button><br>
                            <a href="/products?products&show"><button class="t_button button_xl" type="button">Abbrechen</button></a>
                        </form>
                    </center>
                ';
            }
        }
    }
?>