<?php
    if(isset($_POST['add_storageLocation']))
    {
        $number = GetProperty("prefix_storage").'-'.$_POST['number'];
        $name = $_POST['name'];
        $location = $_POST['location'];
        $slots = $_POST['slots'];
        $isUpdate=true;

        if(!MySQLResultExists("SELECT * FROM storage_locations WHERE storage_id = '$number'"))
        {
            $isUpdate=false;
            MySQLNonQuery("INSERT INTO storage_locations (storage_id) VALUES ('$number')");
        }

        MySQLNonQuery("UPDATE storage_locations SET storage_name = '$name', storage_location = '$location', storage_slots = '$slots' WHERE storage_id = '$number'");

        if($isUpdate) NotificationBanner("Lagerstandort aktualisiert.","check");
        else NotificationBanner("Lagerstandort hinzugef&uuml;gt.","check");

        Redirect("products?storagelocations");
        die();
    }

    if(isset($_POST['delete_storage']))
    {
        $id = $_POST['delete_storage'];

        if(!MySQLResultExists("SELECT * FROM products WHERE storage_location = '$id'"))
        {
            MySQLNonQuery("DELETE FROM storage_locations WHERE storage_id = '$id'");
            NotificationBanner("Lagerstandort entfernt.","check");
        }
        else NotificationBanner("Es befinden sich noch ".fetch_count("products","storage_location",$id)." Produkte in diesem Lager.","cross");

        Redirect(ThisPage());
        die();
    }

    echo '<h2>Lagerstandorte</h2>';

    echo '
        <form action="'.ThisPage().'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
            <center>
                <table class="data_inputbox_m">
                    <tr>
                        <td colspan=2><h3>'.((isset($_GET['edit'])) ? 'Lagerstandort bearbeiten' : 'Neuen Lagerstandort eintragen').'</h3></td>
                    </tr>
                    <tr>
                        <td style="text-align:right;">Lager-Nummer:</td>
                        <td>
                            <input class="textfield_mod t_textfield" style="width:60px;margin-right:0px;" value="'.GetProperty("prefix_storage").'-" readonly><input name="number" value="'.((isset($_GET['edit'])) ? (str_replace(GetProperty("prefix_storage").'-','',fetch("storage_locations","storage_id","storage_id",$_GET['edit']))) : '').'" class="textfield_mod t_textfield" style="width:231px;margin-left:0px" placeholder="Nummer..." '.((isset($_GET['edit'])) ? 'readonly' : 'required').' >
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:right;">Lager-Name:</td>
                        <td>
                            <input name="name" class="textfield_m t_textfield" placeholder="Name..." value="'.((isset($_GET['edit'])) ? (fetch("storage_locations","storage_name","storage_id",$_GET['edit'])) : '').'" required>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:right;">Lager-Ort:</td>
                        <td>
                            <textarea name="location" class="textarea_m t_textarea" placeholder="Adresse, Raum, Gebäude,..." required>'.((isset($_GET['edit'])) ? (fetch("storage_locations","storage_location","storage_id",$_GET['edit'])) : '').'</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:right;">Lagerpl&auml;tze:</td>
                        <td>
                            <input name="slots" type="number" class="textfield_m t_textfield" value="'.((isset($_GET['edit'])) ? (fetch("storage_locations","storage_slots","storage_id",$_GET['edit'])) : '').'" placeholder="Anzahl..." required>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=2 style="text-align:center">
                            <br><br>
                            <button type="submit" name="add_storageLocation" class="t_button button_m">'.((isset($_GET['edit'])) ? 'Lager aktualisieren' : 'Lager eintragen').'</button><br>
                            '.((isset($_GET['edit'])) ? '<a href="products?storagelocations"><button type="button" class="t_button button_m">Abbrechen</button></a>' : '').'
                        </td>
                    </tr>
                </table>
            <br><br>
            </form>
            <form action="'.ThisPage().'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <table class="orders_table">
    ';

    $i=0;
    $strSQL = "SELECT * FROM storage_locations";
    $rs=mysqli_query($link,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        $style_color = ($i%2==0) ? 'style="background:#DBEDFF;"' : 'style="background:#F2F2F2;"';
        $style_id = ($i++%2==0) ? 'id="shaded_cell"' : '';

        echo '
            <tr>
                <td '.$style_id.'>
                    <a href="products?storagelocations&edit='.$row['storage_id'].'"><span style="color: #FFA500">&#9997; <u>Bearbeiten</u></span></a><br>
                    <a href="#delete'.$row['storage_id'].'"><span style="color: #CC0000">&#x2718; <u>Löschen</u></a></span>
                </td>
                <td '.$style_id.'>
                    <b>'.$row['storage_id'].'</b><br>
                    '.$row['storage_name'].'
                </td>
                <td '.$style_id.'>
                    <b><u>Ort:</u></b><br>
                    '.nl2br($row['storage_location']).'
                </td>
                <td '.$style_id.'>
                    <b><u>Lagerpl&auml;tze:</u></b><br>
                    '.$row['storage_slots'].' Pl&auml;tze
                </td>
            </tr>
            <tr>
                <td colspan=4 class="foldup_cell" id="delete'.$row['storage_id'].'" '.$style_color.'>
                    <center>
                        <button class="t_button_warning button_m" type="submit" name="delete_storage" value="'.$row['storage_id'].'">L&ouml;schen best&auml;tigen</button><br>
                        <a href="#close"><button class="t_button button_m" type="button">Abbrechen</button></a>
                    </center>
                </td>
            </tr>
        ';
    }

    echo '
                </table>
            </center>
        </form>
    ';
?>