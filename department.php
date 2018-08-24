<?php
    include("header.php");

    if(isset($_GET['purchase']))
    {
        echo '<div id="fade_in"><h1>'.langlib("Einkauf").'</h1></div><div id="content_fade_in">';
        echo RegisterCards("purchase","&Uuml;bersicht|!|overview","Lagersbestand||warehouse","Nachbestellen||reorder","Warenstruktur||structure");

        require "department_1purchase.php" ;
        echo '</div>';
    }

    if(isset($_GET['construction']))
    {
        echo '<div id="fade_in"><h1>'.langlib("Konstruktion").'</h1></div><div id="content_fade_in">';
        echo RegisterCards("construction","&Uuml;bersicht|!|overview","Produkte||products","Prototypen||prototypes","Konzepte||concepts");

        require "department_2construction.php";
        echo '</div>';
    }

    if(isset($_GET['production']))
    {
        echo '<div id="fade_in"><h1>'.langlib("Produktion").'</h1></div><div id="content_fade_in">';
        echo RegisterCards("production","&Uuml;bersicht|!|overview","Produktions&uuml;bersicht||overview","Fertigungsdaten||manufacturingdata");

        require "department_3production.php";
        echo '</div>';
    }

    if(isset($_GET['marketing']))
    {
        echo '<div id="fade_in"><h1>'.langlib("Marketing").'</h1></div><div id="content_fade_in">';
        echo RegisterCards("marketing","&Uuml;bersicht|!|overview","Werbung||adds","Social Media||socialmedia","Events||events","Gutscheine||vouchers","Specials||specials");

        require "department_4marketing.php";
        echo '</div>';
    }

    if(isset($_GET['administration']))
    {
        echo '<div id="fade_in"><h1>'.langlib("Administration").'</h1></div><div id="content_fade_in">';
        echo RegisterCards("administration","&Uuml;bersicht|!|overview","Service||service","Fehler Melden||bugreport","Vorschl&auml;ge/Anforderungen||requests");

        require "department_5administration.php";
        echo '</div>';
    }

    if(isset($_GET['accounting']))
    {
        echo '<div id="fade_in"><h1>'.langlib("Buchhaltung").'</h1></div><div id="content_fade_in">';
        echo RegisterCards("accounting","&Uuml;bersicht|!|overview","Finanzen||finances","Personal||employees","Rechnungen||bills","Angebote||offers","Dokumente||documents");

        require "department_6accounting.php";
        echo '</div>';
    }

    if(isset($_GET['sale']))
    {
        echo '<div id="fade_in"><h1>'.langlib("Verkauf").'</h1></div><div id="content_fade_in">';
        echo RegisterCards("sale","&Uuml;bersicht|!|overview","Lieferscheine||deliverynotes","Rechnungen||bills","Versand||shipping");
        
        require "department_7sale.php";
        echo '</div>';
    }

    include("footer.php");
?>