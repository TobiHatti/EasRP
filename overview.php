<?php
    include("header.php");

    echo '
        <div id="fade_in"><h1>&Uuml;bersicht</h1></div>
        <div id="content_fade_in">
            <h1>Wilkommen zur&uuml;ck, '.$_SESSION['first_name'].'</h1>
            <br>
            '.RegisterCards("overview","Ihre Abteilung|!|personalDepartments","Fremde Abteilungen||otherDepartments","Alle Abteilungen||allDepartments").'

            <h2>Men&uuml;</h2>
            <h3>Allgemeines</h3>
            <button class="t_button button_m">&#x2BC8; Produkte</button>
            <button class="t_button button_m">&#x2BC8; Halbprodukte</button>
            <button class="t_button button_m">&#x2BC8; Rohteile</button>
            <button class="t_button button_m">&#x2BC8; Hilfsprodukte</button><br>

            <button class="t_button button_m">&#x2BC8; Bestellung eintragen</button>
            <button class="t_button button_m">&#x2BC8; Neue Bestellungen</button>
            <button class="t_button button_m">&#x2BC8; In Produktion</button>
            <button class="t_button button_m">&#x2BC8; Verkaufsbereit</button>
            <button class="t_button button_m">&#x2BC8; Verkauft</button><br>

            ';



            $userId = $_SESSION['user_id'];

            if(isset($_GET['otherDepartments'])) $strSQL = "SELECT * FROM departments WHERE id NOT LIKE (SELECT department_id FROM department_assigns WHERE user_id = '$userId')";
            else if(isset($_GET['allDepartments'])) $strSQL = "SELECT * FROM departments";
            else $strSQL = "SELECT * FROM departments INNER JOIN department_assigns ON departments.id = department_assigns.department_id INNER JOIN users ON department_assigns.user_id = users.id";
            $rs=mysqli_query($link,$strSQL);
            while($row=mysqli_fetch_assoc($rs))
            {
                echo '
                    <h3>'.$row['department'].'</h3>

                    <hr>
                    <h3>'.$row['department'].'</h3>
                    Neuigkeiten in dieser Abteilung:

                    <hr>
                ';
            }
            echo '
        </div>
    ';

    include("footer.php");
?>