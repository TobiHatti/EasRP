<?php
    include("header.php");

    if(isset($_POST['submit']))
    {
        $uid = uniqid();
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];

        $mail = strtolower($first_name).'.'.strtolower($last_name).'@wepen.at';
        $password = sha1(sha1("123abc"."salt")."salt");

        $strSQL = "INSERT INTO users (id,first_name,last_name,email,password) VALUES ('$uid','$first_name','$last_name','$mail','$password')";
        $rs=mysqli_query($link,$strSQL);

        protocol_add('Neuer Nutzer wurde angelegt.');

        echo '<meta http-equiv="refresh" content="0; url=/user_register" />';
    }

    echo '<div id="fade_in"><h1>Nutzer Registrieren</h1></div>';

    echo '
        <div id="content_fade_in">
            <form action="/user_register" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <center>
                    <div class="content_box_s">
                        <h2>Nutzer Eintragen</h2>
                        <br>
                        <input type="text" name="first_name" placeholder="Vorname" class="textfield_m t_textfield"/><br>
                        <input type="text" name="last_name" placeholder="Nachname" class="textfield_m t_textfield"/><br><br>

                        <button type="submit" name="submit" class="button_m t_button">Nutzer Hinzuf&uuml;gen</button>
                    </div>
                </center>
            </form>
        </div>
    ';


    include("footer.php");
?>