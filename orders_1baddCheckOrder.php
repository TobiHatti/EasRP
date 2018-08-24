<?php

    $order_number  = $_GET['addCheckOrder'];

    $strSQL = "SELECT * FROM orders
    INNER JOIN customer_order ON orders.order_number = customer_order.order_number
    INNER JOIN customers ON customers.customer_number = customer_order.customer_number
    WHERE orders.order_number = '$order_number'";
    $rs=mysqli_query($link,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        $pdfScale=3;
        echo '
            <form action="/orders" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <h2>Bestellungszusammenfassung</h2>
                <br><br>
                <center>
                    <div style="display: inline-table; vertical-align:top;">
                        <table class="content_table" style="width:350px;">
                            <tr>
                                <td style="background:#87CEFA"><h3>Kundeninformationen</h3></td>
                            </tr>
                            <tr>
                                <td id="shaded_cell">Kundennummer</td>
                            </tr>
                            <tr>
                                <td>'.$row['customer_number'].'<br><br></td>
                            </tr>
                            <tr>
                                <td id="shaded_cell">Anrede</td>
                            </tr>
                            <tr>
                                <td>
                                    '.SalutationCode($row['salutation']).'<br>
                                    '.(($row['title']!='') ? ($row['title'].' ') : '').$row['first_name'].' '.$row['last_name'].'<br>
                                    '.$row['adressline1'].'<br>
                                    '.(($row['adressline2']=='') ? ($row['zip'].' '.$row['city']) : $row['adressline2']).'<br>
                                    '.(($row['adressline2']=='') ? $row['country'] : ($row['zip'].' '.$row['city'])).'<br>
                                    '.(($row['adressline2']!='') ? $row['country'] : '').'
                                    <br>
                                    <br>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Versandkosten Anrechnen ('.GetProperty("company_country").' &gt; '.$row['country'].')</b><br>
                                    <input checked type="checkbox" name="addShippingCosts" id="addShippingCosts" onchange="DisplayShippingCostsMessage();"><label class="checkbox_label" for="addShippingCosts"></label>
                                    <output id="outShippingCostsMessage">Versandkosten Anrechen (+?,00 &euro;)</output>
                                    <br><br>
                                </td>
                            </tr>
                            <tr>
                                <td id="shaded_cell">Kontakt</td>
                            </tr>
                            <tr>
                                <td>'.$row['email'].'<br><br></td>
                            </tr>
                            <tr>
                                <td style="background:#87CEFA"><h3>Bestellinformationen</h3></td>
                            </tr>
                            <tr>
                                <td id="shaded_cell">Bestellnummer</td>
                            </tr>
                            <tr>
                                <td>'.$row['order_number'].'<br><br></td>
                            </tr>
                            <tr>
                                <td id="shaded_cell">Datum</td>
                            </tr>
                            <tr>
                                <td>
                                    Bestelldatum: '.((DateFormat($row['order_date'],"date")!='0000-00-00') ? DateFormat($row['order_date'],"date",'.','F') : 'Ausstehend...').'<br>
                                    Zahlungsdatum: '.((DateFormat($row['purchase_date'],"date")!='0000-00-00') ? DateFormat($row['purchase_date'],"date",'.','F') : 'Ausstehend...').'<br>
                                    Rechnungsdatum: '.((DateFormat($row['bill_date'],"date")!='0000-00-00') ? DateFormat($row['bill_date'],"date",'.','F') : 'Ausstehend...').'<br>
                                    Versandsdatum: '.((DateFormat($row['shipping_date'],"date")!='0000-00-00') ? DateFormat($row['shipping_date'],"date",'.','F') : 'Ausstehend...').'<br>
                                    Erhaltsdatum: '.((DateFormat($row['reception_date'],"date")!='0000-00-00') ? DateFormat($row['reception_date'],"date",'.','F') : 'Ausstehend...').'<br>
                                    <br>
                                </td>
                            </tr>
                            <tr>
                                <td id="shaded_cell">Zahlung</td>
                            </tr>
                            <tr>
                                <td>
                                    Zahlungsmethode:
                                    <select name="paymentType" id="paymentType" class="selectbox_m t_selectbox" onchange="PaymentConfirmation();">
                                        <option selected disabled value="none">--- Ausw&auml;hlen ---</option>
                                        <option value="directDebit">Sofort&uuml;berweisung</option>
                                        <option value="kreditCard">Kredidkarte</option>
                                        <option value="cashOnDelivery">Nachnahme</option>
                                        <option value="paypal">PayPal</option>
                                        <option value="cahs">Barzahlung</option>
                                        <option value="other">Andere</option>
                                    </select><br>
                                    Zahlungsstatus:
                                    <select name="paymentStatus" id="paymentStatus" class="selectbox_m t_selectbox" onchange="PaymentConfirmation();">
                                        <option selected disabled value="none">--- Ausw&auml;hlen ---</option>
                                        <option value="pending">Ausstehend</option>
                                        <option value="paid">Bezahlt</option>
                                    </select><br>
                                    <br>
                                </td>
                            </tr>
                            <tr>
                                <td style="background:#87CEFA"><h3>Optionen & Abschlie&szlig;en</h3></td>
                            </tr>
                            <tr>
                                <td id="shaded_cell">Auftragsbest&auml;tigung per E-Mail versenden an '.$row['email'].'</td>
                            </tr>
                            <tr>
                                <td>
                                    <br>
                                    <input checked type="checkbox" name="sendOrderConfirmation" id="sendOrderConfirmation" onchange="DisplayOrderConfirmationMessage();"><label class="checkbox_label" for="sendOrderConfirmation"></label>
                                    <output id="outSendOrderMessage">Auftragsbest&auml;tigung versenden</output>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <br>
                                    <center><button id="finishOrderConfirmed" class="button_m t_button" name="finishOrderConfirmed" value="'.$row['order_number'].'" disabled>Bestellung Abschlie&szlig;en</button></center>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <input type="hidden" id="orderNumber" value="'.$row['order_number'].'"/>
                    <div style="display: inline; vertical-align:top; margin-left:100px;">
                        <iframe src="/pdfOrderConfirmation?orderNumber='.$row['order_number'].'&ship=1&payment=none" style="height:'. 297 * $pdfScale .'px; width: '. 210 * $pdfScale .'px;" id="orderConfirmationFrame"></iframe>
                    </div>
                </center>
            </form>
        ';
    }
?>