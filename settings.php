<?php
    include("header.php");

    if(isset($_POST['deflang_erp'])) SetProperty("deflang_erp",$_POST['deflang_erp']);
    if(isset($_POST['vat_add'])) SetProperty("vat_add",$_POST['vat_add']);
    if(isset($_POST['vat_show'])) SetProperty("vat_show",$_POST['vat_show']);
    if(isset($_POST['dbPurge']))
    {
        DataBasePurge();
        Redirect("settings");
    }



    echo '<div id="fade_in"><h1>'.langlib("Einstellungen").'</h1></div>';

    echo '
        <br>
        <form action="settings" method="post" accept-charset="utf-8" enctype="multipart/form-data">
            Standartsprache f&uuml;r ERP : '.GetProperty("deflang_erp").'<br>
            <button type="submit" value="DE" name="deflang_erp" class="button_m t_button">'.langlib("Deutsch").'</button>
            <button type="submit" value="EN" name="deflang_erp" class="button_m t_button">'.langlib("Englisch").'</button>
            <br><br>

            Umsatzsteuer Verrechnen (USt./VAT) : '.GetProperty("vat_add").' <br>
            <button type="submit" value="1" name="vat_add" class="button_m t_button">USt. Verrechnen</button>
            <button type="submit" value="0" name="vat_add" class="button_m t_button">USt. Nicht Verrechnen</button>
            <br><br>

            Umsatzsteuer Anzeigen (USt./VAT) : '.GetProperty("vat_show").' <br>
            <button type="submit" value="1" name="vat_show" class="button_m t_button">USt. Anzeigen</button>
            <button type="submit" value="0" name="vat_show" class="button_m t_button">USt. Nicht Anzeigen</button>
            <br><br>
            Datenbank-Reinigung:<br>
            <button type="submit" name="dbPurge" class="button_m t_button_warning">(!) Jetzt Reinigen</button>
            <hr>
        </form>
    ';

    include("footer.php");
?>