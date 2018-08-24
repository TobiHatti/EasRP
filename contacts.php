<?php
    include("header.php");

    echo '<div id="fade_in"><h1>'.langlib("Produkte").'</h1></div>';

    echo RegisterCards("","Angestellte|!|employees","Kunden||customers","Vertreiber||retailers","Hersteller||producers");

    if(isset($_GET['employees']))
    {
        echo '<div id="content_fade_in"><h2>'.langlib("Angestellte").'</h2>';
        require "contacts_1employees.php" ;
        echo '</div>';
    }

    if(isset($_GET['customers']))
    {
        echo '<div id="content_fade_in"><h2>'.langlib("Kunden").'</h2>';
        require "contacts_2customers.php" ;
        echo '</div>';
    }

    if(isset($_GET['retailers']))
    {
        echo '<div id="content_fade_in"><h2>'.langlib("Vertreiber").'</h2>';
        require "contacts_3retailers.php" ;
        echo '</div>';
    }

    if(isset($_GET['producers']))
    {
        echo '<div id="content_fade_in"><h2>'.langlib("Hersteller").'</h2>';
        require "contacts_4producers.php" ;
        echo '</div>';
    }


    include("footer.php");
?>