<?php
    include("header.php");

    if(isset($_POST['signin']))
    {
        $phash = sha1(sha1($_POST['password']."salt")."salt");
        $email = $_POST['email'];

        $strSQL = "SELECT * FROM users WHERE email LIKE '$email'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            if($row['password'] == $phash)
            {
                $_SESSION['first_name'] = $row['first_name'];
                $_SESSION['last_name'] = $row['last_name'];
                $_SESSION['user_id'] = $row['id'];

                // Expires after 1 week
                //setcookie("user_id",$row['id'],time()+(3600*24*7));

                echo '<meta http-equiv="refresh" content="0; url=/overview?personal_departments" />';
            }
            else echo '<meta http-equiv="refresh" content="0; url=/account?error" />';
        }
    }

    if(isset($_POST['signin_legic']))
    {
        $key = $_POST['legic'];
        $strSQL = "SELECT * FROM users WHERE login_nfc_key = '$key'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
                $_SESSION['first_name'] = $row['first_name'];
                $_SESSION['last_name'] = $row['last_name'];
                $_SESSION['user_id'] = $row['id'];

                // Expires after 1 week
                //setcookie("user_id",$row['id'],time()+(3600*24*7));

                echo '<meta http-equiv="refresh" content="0; url=/overview?personal_departments" />';
        }
    }



    if(isset($_POST['ch_pass']))
    {
        if($_POST['password'] == $_POST['c_password'])
        {
            $phash = sha1(sha1($_POST['password']."salt")."salt");
            $user_id = $_SESSION['user_id'];

            $strSQL = "UPDATE users SET password = '$phash' WHERE id LIKE '$user_id'";
            $rs=mysqli_query($link,$strSQL);

            echo '<meta http-equiv="refresh" content="0; url=/account?error_pr" />';
        }
        else echo '<meta http-equiv="refresh" content="0; url=/account?error_pw" />';
    }

    if(!isset($_SESSION['user_id']))
    {
        // LOGIN FORM
        $error_msg='';
        if(isset($_GET['error'])) $error_msg = '<span style="color: #CC0000">Login Fehlgeschlagen</span>';

        echo '
            <script>
            document.addEventListener("keydown", function(event) {

                // This function only selects the Legic-Bar when no other textbox or textfield is selected
                if (!(document.activeElement.nodeName == "TEXTAREA" || document.activeElement.nodeName == "INPUT" || (document.activeElement.nodeName == "DIV" && document.activeElement.isContentEditable)))
                {
                    if (!event.ctrlKey && event.keyCode!=116 && /[a-zA-Z0-9-_ ]/.test(String.fromCharCode(event.keyCode)))
                    {
                        document.getElementById("legic").focus();
                    }
                }
            });
            </script>

            <div id="fade_in"><h1>Account</h1></div>
            <div id="content_fade_in">
                <form action="/account" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <center>
                        <div class="content_box_s">
                            <h2>Login</h2>
                            '.$error_msg.'
                            <br>
                            Melden Sie sich mit Ihrer WEPEN&reg; - E-mail Adresse an:<br><br>
                            <input type="text" name="email" placeholder="max.muster@wepen.at" class="textfield_m t_textfield" required/><br>
                            <input type="password" name="password" placeholder="Passwort" class="textfield_m t_textfield" required/><br>
                            Achtung: Passwort bei erstem Login: "123abc". Bitte gleich nach Anmelden unter "Account" &auml;ndern!
                            <br><br>
                            <button type="submit" name="signin" class="button_m t_button">Anmelden</button>
                        </div>
                        <br>
                    </form>
                    <form action="/account" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                        <div class="content_box_s">
                            <h2>Legic-Login</h2>
                            <br>
                            Legen Sie ihren Legic&trade; auf das Lesegerät<br>
                            <input type="password" name="legic" placeholder="Legic" id="legic" class="textfield_m t_textfield" required/><br>
                            <br>
                            <button type="submit" name="signin_legic" class="button_m t_button">Anmelden</button>
                        </div>
                    </center>
                </form>
            </div>
        ';
    }
    else
    {
        echo '
            <div id="fade_in"><h1>Account</h1></div>  
            <div id="content_fade_in">
                <form action="/account" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <hr>
                    <hr>
                    <h2>Konto</h2>
                    Angemeldet als <b>'.$_SESSION['first_name'].' '.$_SESSION['last_name'].'</b><br>
                    <a href="/signout"><button type="button" class="button_m t_button">Abmelden</button></a><br>
                    <hr>
                    <hr>
                    <h2>E-Mails</h2>
                    Deine E-Mail Adresse: <u><i><span style="color: #1E90FF">'.seek('mail',$_SESSION['user_id']).'</span></i></u><br><br>
                    E-Mails an folgende Adressen werden an dich weitergeleitet:<br>
                    ';
                        $user_id = $_SESSION['user_id'];
                        $strSQL = "SELECT * FROM email_recievers WHERE user_id LIKE '$user_id'";
                        $rs=mysqli_query($link,$strSQL);
                        while($row=mysqli_fetch_assoc($rs)) echo '<li><i><u><span style="color: #1E90FF">'.seek('mailres',$row['mail_id']).'</span></u></i></li>';
                    echo '
                    <br>
                    <a href="http://mail.wepen.at" target="_blank"><button type="button" class="button_m t_button">Zu deinem E-Mail Postfach</button></a><br>
                    <hr>
                    <hr>
                    <h2>Sicherheit</h2>
                    ';
                        if(isset($_GET['error_pw'])) echo '<span style="color: #CC0000">Passwörter nicht identisch!<br></span>';
                        if(isset($_GET['error_pr'])) echo '<span style="color: #32CD32">Passwort erfolgreich geändert!<br></span>';
                        if(fetch('users','password','id',$_SESSION['user_id']) == sha1(sha1("123abc"."salt")."salt")) echo 'Du benutzt noch das Standart-Passwort (<i>123abc</i>). Bitte ändere es so bald wie möglich!<br>';
                    echo '
                    <br>Passwort ändern:<br>
                    <input type="password" name="password" placeholder="Neues Passwort" class="textfield_m t_textfield" required/><br>
                    <input type="password" name="c_password" placeholder="Passwort Wiederholen" class="textfield_m t_textfield" required/><br>
                    <button type="submit" name="ch_pass" class="button_m t_button">Passwort ändern</button><br>
                    <hr>
                    <hr>
                </form>
            </div>
        ';
    }

    include("footer.php");
?>