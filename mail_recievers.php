<?php
    include("header.php");

    if(isset($_POST['submit']))
    {
        $uid = uniqid();
        $user = $_POST['user'];
        $mail = $_POST['mail'];

        protocol_add('Dem Nutzer "'.fetch("users","last_name","id",$user).'" wurde die E-Mail-Adresse "'.$mail.'" zugewiesen.');

        $strSQL = "INSERT INTO email_recievers (id,user_id,mail_id) VALUES ('$uid','$user','$mail')";
        $rs=mysqli_query($link,$strSQL);

        echo '<meta http-equiv="refresh" content="0; url=/mail_recievers" />';
    }

    echo '
        <div id="fade_in"><h1>Email Empf&auml;nger</h1></div> 
        <div id="content_fade_in">
            <form action="/mail_recievers" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <center>
                    <div class="content_box_s">
                        <h2>E-Mail Link Eintragen</h2>
                        <br>
                        <select name="user" class="selectbox_m t_selectbox">
                        ';
                            $strSQL = "SELECT * FROM users";
                            $rs=mysqli_query($link,$strSQL);
                            while($row=mysqli_fetch_assoc($rs))
                            {
                                echo '<option value="'.$row['id'].'">'.$row['first_name'].' '.$row['last_name'].'</option>';
                            }
                        echo '
                        </select>
                        <br>Erhält Mails von<br>
                        <select name="mail" class="selectbox_m t_selectbox">
                        ';
                            $strSQL = "SELECT * FROM emails";
                            $rs=mysqli_query($link,$strSQL);
                            while($row=mysqli_fetch_assoc($rs))
                            {
                                echo '<option value="'.$row['id'].'">'.$row['email'].'</option>';
                            }
                        echo '
                        </select>
                        <br><br>
                        <button type="submit" name="submit" class="button_m t_button">Link Hinzufügen</button>
                    </div>
                </center>
            </form>
        </div>
    ';

    include("footer.php");
?>