<?php
    $redirect = (isset($_GET['selP'])) ?  str_replace(('&selP='.$_GET['selP']),'',basename($_SERVER["REQUEST_URI"], '.php')) : basename($_SERVER["REQUEST_URI"], '.php');

    /*<script>
            window.onload = function() {
                CheckMail();
            };
        </script>*/

    echo '
        <br><br>
        <center>
            <form action="/orders" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <table class="data_inputbox_m">
                    <tr>
                        <td colspan=2><center><h3>Artikel Ausw&auml;hlen</h3></center></td>
                    </tr>
                    <tr>
                        <td colspan=2>
                            <center>
                                <br><br>
                                <input type="hidden" id="extension" value="'.$redirect.'"/>
                                <span style="color: #FFA500">'.((isset($_GET['newCustomer']) OR !isset($_GET['existingCustomer'])) ? 'Zuerst Kundendaten ausf&uuml;llen!' : '').'</span>
                                <select name="" id="product_list" class="selectbox_lm t_selectbox" onchange="ProductSelectAddP();"  '.((isset($_GET['newCustomer']) OR !isset($_GET['existingCustomer'])) ? 'disabled' : '').'>
                                    <option value="none">--- Artikel ausw&auml;hlen ---</option>
                                    ';
                                    $prefix_product = GetProperty("prefix_product");
                                    $strSQL = "SELECT * FROM products WHERE number LIKE '$prefix_product%' AND sellable = '1'";
                                    $rs=mysqli_query($link,$strSQL);
                                    while($row=mysqli_fetch_assoc($rs))
                                    {
                                        $product_selected = (isset($_GET['selP']) AND $_GET['selP']!='' AND $row['number']==$_GET['selP']) ? 'selected' : '' ;
                                        echo '<option value="'.$row['number'].'" '.$product_selected.'>['.$row['number'].'] - '.$row['name'].'</option>';
                                    }
                                    echo '
                                </select>
                            </center>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=2><hr><center><h3>Produktdaten</h3></center></td>
                    </tr>

                    ';
                    if(isset($_GET['selP']))
                    {
                        $selected_product = $_GET['selP'];
                        $strSQL = "SELECT * FROM products WHERE number = '$selected_product'";
                        $rs=mysqli_query($link,$strSQL);
                        while($row=mysqli_fetch_assoc($rs))
                        {
                            if($row['attributes']=='') echo '<tr><td colspan=2><center><span style="color: #FFA500">Keine Zusatzattribute vorhanden.</span></center></td></tr>';
                            else
                            {
                                $i=1;
                                $attributes = explode(';',$row['attributes']);
                                foreach($attributes as $attribute)
                                {
                                    echo '
                                        <tr>
                                            <td style="text-align:right;">'.$attribute.'</td>
                                            <td><input type="" class="textfield_m t_textfield" placeholder="'.$attribute.'" name="productAttr'.$i.'"/></td>
                                        </tr>
                                    ';
                                    $i++;
                                }
                            }
                        }
                        $orderNumber = (!isset($_GET['ordId'])) ? GetProperty("prefix_order").'-'.date("ymd").'-'.NumberFormatEx(InkrementProperty("order_ctr",9999),4) : $_GET['ordId'];
                        echo '
                            <tr>
                                <td style="text-align:right;"><br>Menge:</td>
                                <td><br><input type="number" step="1" name="quantity" class="textfield_m t_textfield" value="1" placeholder="Menge"/>'.fetch("products","unit","number",$_GET['selP']).'</td>
                            </tr>
                            <input type="hidden" value="'.$_GET['existingCustomer'].'" name="customer_number"/>
                            <tr>
                                <td style="text-align:right;"><br>Produkt-Nummer</td>
                                <td><br><input type="" name="product_number" class="textfield_m t_textfield" value="'.$_GET['selP'].'" placeholder="Bestellnummer" readonly/></td>
                            </tr>
                            <tr>
                                <td style="text-align:right;">Produkt-Bezeichnung</td>
                                <td><input type="" class="textfield_m t_textfield" value="'.fetch("products","name","number",$_GET['selP']).'" placeholder="Bestellnummer" readonly/></td>
                            </tr>
                            <tr>
                                <td style="text-align:right;">Bestellung</td>
                                <td><input type="" name="order_number" class="textfield_m t_textfield" value="'.$orderNumber.'" placeholder="Bestellnummer" readonly/></td>
                            </tr>
                            <tr>
                                <td colspan=2><center><br><button class="button_m t_button" name="add_product">Produkt hinzuf&uuml;gen</button></center></td>
                            </tr>
                        ';
                    }
                    else echo '<tr><td colspan=2><center>'.((isset($_GET['ordId'])) ? 'Bitte w&auml;hlen Sie ein Produkt aus<br>oder schlie&szlig;en Sie die Bestellung ab' : 'Bitte w&auml;hlen Sie ein Produkt aus').'<br><br></center></td></tr>';
                    if(isset($_GET['ordId']))
                    {
                         echo '
                            <tr>
                                <td colspan=2>
                                    <table style="width:100%; border: 1px solid #1E90FF;padding:5px;">
                                ';
                                    $i=0;
                                    $order_number = $_GET['ordId'];
                                    $strSQL = "SELECT * FROM order_contains WHERE order_number = '$order_number'";
                                    $rs=mysqli_query($link,$strSQL);
                                    while($row=mysqli_fetch_assoc($rs))
                                    {
                                        $style_id = ($i++%2==0) ? 'id="shaded_cell"' : '';

                                        echo '
                                            <tr>
                                                <td '.$style_id.'>'.$row['quantity'].'x</td>
                                                <td '.$style_id.'>'.$row['product_number'].'</td>
                                                <td '.$style_id.'>'.OrderAttributes($row['order_number'],$row['product_number']).'</td>
                                            </tr>
                                        ';
                                    }
                                echo '
                                    </table>
                                </td>
                            </tr>
                        ';
                    }
                    echo '
                    <tr>
                        <td colspan=2><hr><center><h3>Kundendaten</h3></center></td>
                    </tr>
                    ';

                    if(!isset($_GET['newCustomer']) AND !isset($_GET['duplicateCustomer']) AND !isset($_GET['selectCustomer']) AND !isset($_GET['existingCustomer']))
                    {
                        echo '
                            <tr>
                                <td style="text-align:right;">Vorname*</td>
                                <td><input type="" name="firstName" class="textfield_m t_textfield" placeholder="Vorname" required/></td>
                            </tr>
                            <tr>
                                <td style="text-align:right;">Nachname*</td>
                                <td><input type="" name="lastName" class="textfield_m t_textfield" placeholder="Nachname" required/></td>
                            </tr>
                            <tr>
                                <td style="text-align:right;">E-Mail*</td>
                                <td><input type="" name="eMail" class="textfield_m t_textfield" placeholder="E-Mail" id="customer_email" oninput="CheckMail();" required/><br><span style="color: #CC0000"><output id="email_out"></output></span></td>
                            </tr>
                            <tr>
                                <td colspan=2>
                                    <br><center><button class="button_m t_button" name="check_customer" id="continue_order" disabled>Kundendaten &Uuml;berpr&uuml;fen</button><br><a href="/orders?add&newCustomer">oder neuen Kunden anlegen</a></center>
                                </td>
                            </tr>
                        ';
                    }

                    if(isset($_GET['newCustomer']))
                    {
                        $customer_number = ($_GET['newCustomer']!='') ? $_GET['newCustomer'] : GenerateCustomerNumber();

                        $executeBackgroundCheck = ($_GET['newCustomer']!='') ? '' : 'checked' ;
                        echo '<input name="backgroundCheck" type="checkbox" '.$executeBackgroundCheck.'>';

                        echo '<input type="hidden" id="prefix_customer" value="'.GetProperty("prefix_customer").'" />';
                        echo '
                        <tr>
                         <td colspan=2><center>Neuen Kunden anlegen<br><br></center></td>
                        </tr>
                        <tr>
                            <td style="text-align:right;">Kundennummer*</td>
                            <td><input type="" name="customer_number" id="customer_number" class="textfield_m t_textfield" value="'.$customer_number.'" placeholder="Kundennummer" oninput="CheckCustomerNumber();" readonly/><br><span style="color: #CC0000"><output id="cnr_out"></output></span></td>
                        </tr>
                        <tr>
                            <td style="text-align:right;"><br>Anrede*</td>
                            <td><br>
                                <select name="salutation" class="selectbox_s t_selectbox" id="">
                                    <option value="SN">Keine</option>
                                    <option value="SM">Herr</option>
                                    <option value="SF">Frau</option>
                                    <option value="SC">Firma</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align:right;">Titel</td>
                            <td><input type="" name="title" class="textfield_xs t_textfield" placeholder="Titel" value="'.IfSetFill("customers","title","customer_number",$customer_number).'"/></td>
                        </tr>
                        <tr>
                            <td style="text-align:right;">Vorname*</td>
                            <td><input type="" name="firstName" class="textfield_m t_textfield" placeholder="Vorname" value="'.IfSetFill("customers","first_name","customer_number",$customer_number).'" required/></td>
                        </tr>
                        <tr>
                            <td style="text-align:right;">Nachname*</td>
                            <td><input type="" name="lastName" class="textfield_m t_textfield" placeholder="Nachname" value="'.IfSetFill("customers","last_name","customer_number",$customer_number).'" required/></td>
                        </tr>
                        <tr>
                            <td style="text-align:right;">E-Mail*</td>
                            <td><input type="" name="eMail" class="textfield_m t_textfield" placeholder="E-Mail" id="customer_email" oninput="CheckMail();" value="'.IfSetFill("customers","email","customer_number",$customer_number).'"/><br><span style="color: #CC0000"><output id="email_out"></output></span></td>
                        </tr>
                        <tr>
                            <td style="text-align:right;">Adresszeile 1*</td>
                            <td><input type="" name="adressLine1" class="textfield_m t_textfield" placeholder="Stra&szlig;e / Hausnummer" value="'.IfSetFill("customers","adressline1","customer_number",$customer_number).'" required/></td>
                        </tr>
                        <tr>
                            <td style="text-align:right;">(optional) Adresszeile 2</td>
                            <td><input type="" name="adressLine2" class="textfield_m t_textfield" placeholder="Stiege / T&uuml;r / etc." value="'.IfSetFill("customers","adressline2","customer_number",$customer_number).'"/></td>
                        </tr>
                        <tr>
                            <td style="text-align:right;">Ort*</td>
                            <td><input type="" name="city" class="textfield_m t_textfield" placeholder="Ort" value="'.IfSetFill("customers","city","customer_number",$customer_number).'" required/></td>
                        </tr>
                        <tr>
                            <td style="text-align:right;">PLZ / ZIP-Code*</td>
                            <td>
                                <input type="" name="plzZip" class="textfield_xs t_textfield" placeholder="PLZ / ZIP" value="'.IfSetFill("customers","zip","customer_number",$customer_number).'" required/>
                                <img src="/files/content/blank.gif" class="" alt="" id="flag_img" style="margin-bottom: -16px"/>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align:right;">Land*<br><br></td>
                            <td>
                                <select name="country" id="country_list" class="selectbox_m t_selectbox" onchange="UpdateFlag();" required>
                                    <option selected disabled>---- Ausw&auml;hlen ----</option>
                                    '.CountrySelectList("AT,DE,GB,US").'
                                </select>
                                <br><br>
                            </td>
                        </tr>
                        <tr>
                            <td colspan=2>
                                <br><center><button class="button_m t_button" name="save_customer" id="continue_order" disabled>Kunden eintragen</button></center>
                            </td>
                        </tr>
                        ';
                    }

                    if(isset($_GET['selectCustomer']))
                    {
                        echo '
                        <tr>
                            <td colspan=2><center><span style="color: #FFA500">W&auml;hlen Sie den Richtigen Nutzer aus:</span></center></td>
                        </tr>
                        ';

                        $fn = $_GET['fn'];
                        $ln = $_GET['ln'];
                        $em = $_GET['em'];

                        $strSQL = "SELECT * FROM customers WHERE first_name = '$fn' AND last_name = '$ln' AND email = '$em'";
                        $rs=mysqli_query($link,$strSQL);
                        while($row=mysqli_fetch_assoc($rs))
                        {
                            echo '
                            <tr>
                                <td>
                                    <table class="content_table" style="background:#A2D9FB;margin:5px;padding:5px;">
                                        <tr>
                                            <td><b><u>Aktuelle Kundendaten</u></b></td>
                                        </tr>
                                        <tr>
                                            <td><b>Anrede:</b></td>
                                            <td>'.(($row['salutation']!='SN') ? (SalutationCode($row['salutation']).'<br>') : '').(($row['title']!='') ? ($row['title'].' ') : '').$row['first_name'].' '.$row['last_name'].'</td>
                                        </tr>
                                        <tr>
                                            <td><b>Adresse:</b></td>
                                            <td>'.$row['adressline1'].'<br>'.(($row['adressline2']!='') ? ($row['adressline2'].'<br>') : '').$row['zip'].' '.$row['city'].'<br>'.$row['country'].'</td>
                                        </tr>
                                        <tr>
                                            <td><b>E-Mail:</b></td>
                                            <td>'.$row['email'].'</td>
                                        </tr>
                                        <tr>
                                            <td><b>Kundennummer:</b></td>
                                            <td>'.$row['customer_number'].'</td>
                                        </tr>
                                    </table>
                                </td>
                                <td>
                                    <button class="button_m t_button" name="selectCustomer" value="'.$row['customer_number'].'">Diesen Kunden ausw&auml;hlen<br>('.$row['customer_number'].')</button>
                                </td>
                            </td>
                            ';
                        }
                    }
                    if(isset($_GET['existingCustomer']))
                    {
                        $customerNumber = $_GET['existingCustomer'];
                        $strSQL = "SELECT * FROM customers WHERE customer_number = '$customerNumber'";
                        $rs=mysqli_query($link,$strSQL);
                        while($row=mysqli_fetch_assoc($rs))
                        {
                            echo '
                                <tr>
                                    <td><b>Anrede:</b></td>
                                    <td>'.(($row['salutation']!='SN') ? (SalutationCode($row['salutation']).'<br>') : '').(($row['title']!='') ? ($row['title'].' ') : '').$row['first_name'].' '.$row['last_name'].'</td>
                                </tr>
                                <tr>
                                    <td><b>Adresse:</b></td>
                                    <td>'.$row['adressline1'].'<br>'.(($row['adressline2']!='') ? ($row['adressline2'].'<br>') : '').$row['zip'].' '.$row['city'].'<br>'.$row['country'].'</td>
                                </tr>
                                <tr>
                                    <td><b>E-Mail:</b></td>
                                    <td>'.$row['email'].'</td>
                                </tr>
                                <tr>
                                    <td><b>Kundennummer:</b></td>
                                    <td>'.$row['customer_number'].'</td>
                                </tr>
                            ';
                        }
                    }

                    if(isset($_GET['duplicateCustomer']))
                    {
                        echo '
                            <tr>
                                <td colspan=2><center><span style="color: #CC0000">Kunde wurde bereits eingetragen!</span><br>W&auml;hlen Sie eine Aktion aus:</center></td>
                            </tr>
                            <tr>
                                <td>
                                ';
                                    $exCN = $_GET['exCN'];
                                    $strSQL = "SELECT * FROM customers WHERE customer_number = '$exCN'";
                                    $rs=mysqli_query($link,$strSQL);
                                    while($row=mysqli_fetch_assoc($rs))
                                    {
                                        echo '
                                            <table style="background:#FFFACD;margin:5px;padding:5px;">
                                                <tr>
                                                    <td><b><u>Aktuelle Kundendaten</u></b></td>
                                                </tr>
                                                <tr>
                                                    <td><b>Anrede:</b></td>
                                                    <td>'.(($row['salutation']!='SN') ? (SalutationCode($row['salutation']).'<br>') : '').(($row['title']!='') ? ($row['title'].' ') : '').$row['first_name'].' '.$row['last_name'].'</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Adresse:</b></td>
                                                    <td>'.$row['adressline1'].'<br>'.(($row['adressline2']!='') ? ($row['adressline2'].'<br>') : '').$row['zip'].' '.$row['city'].'<br>'.$row['country'].'</td>
                                                </tr>
                                                <tr>
                                                    <td><b>E-Mail:</b></td>
                                                    <td>'.$row['email'].'</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Kundennummer:</b></td>
                                                    <td>'.$row['customer_number'].'</td>
                                                </tr>
                                            </table>
                                        ';
                                    }
                                echo '
                                </td>
                                <td>
                                ';
                                    $newCN = $_GET['newCN'];
                                    $strSQL = "SELECT * FROM customers WHERE customer_number = '$newCN'";
                                    $rs=mysqli_query($link,$strSQL);
                                    while($row=mysqli_fetch_assoc($rs))
                                    {
                                        echo '
                                            <table style="background:#D8FED8;margin:5px;padding:5px;">
                                                <tr>
                                                    <td><b><u>Neue Kundendaten</u></b></td>
                                                </tr>
                                                <tr>
                                                    <td><b>Anrede:</b></td>
                                                    <td>'.(($row['salutation']!='SN') ? (SalutationCode($row['salutation']).'<br>') : '').(($row['title']!='') ? ($row['title'].' ') : '').$row['first_name'].' '.$row['last_name'].'</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Adresse:</b></td>
                                                    <td>'.$row['adressline1'].'<br>'.(($row['adressline2']!='') ? ($row['adressline2'].'<br>') : '').$row['zip'].' '.$row['city'].'<br>'.$row['country'].'</td>
                                                </tr>
                                                <tr>
                                                    <td><b>E-Mail:</b></td>
                                                    <td>'.$row['email'].'</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Kundennummer:</b></td>
                                                    <td>'.str_replace('-DUPL','',$row['customer_number']).'</td>
                                                </tr>
                                            </table>
                                        ';
                                    }
                                echo '
                                </td>
                            </tr>
                            <tr>
                                <td colspan=2>
                                    <center>
                                        <br><br>
                                        <button class="button_m t_button" name="duplOverwrite"><img src="/files/content/overwrite.png" style="height:60px; float:left;" alt="" /><b>&Uuml;berschreiben</b><br>Neue Kundendaten &uuml;bernehmen und alte Kundendaten &uuml;berschreiben</button><br>
                                        <button class="button_m t_button" name="duplKeepEntry"><img src="/files/content/keepentry.png" style="height:60px; float:left;" alt="" /><b>Beibehalten</b><br>Alte Kundendaten verwenden und neue Kundendaten Ignorieren</button><br>
                                        <button class="button_m t_button" name="duplKeepBoth"><img src="/files/content/keepboth.png" style="height:60px; float:left;" alt="" /><b>Beide speichern</b><br>Alte und neue Kundendaten beibehalten</button><br>

                                        <input type="hidden" value="'.$_GET['exCN'].'" name="exCN"/>
                                        <input type="hidden" value="'.$_GET['newCN'].'" name="newCN"/>
                                    </center>
                                </td>
                            </tr>
                        ';
                    }

                    echo '
                </table>
                <br><br>
                '.((isset($_GET['ordId'])) ? '<button class="button_m t_button" name="finish_order" value="'.$_GET['ordId'].'">Bestellung abschlie&szlig;en</button>' : '').'
            </form>
        </center>


    ';


?>