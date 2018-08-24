<?php
    include("header.php");

    if(isset($_POST['submit']))
    {
        $uid = uniqid();
        $user = $_POST['user'];
        $department = $_POST['department'];

        protocol_add('Dem Nutzer "'.fetch("users","last_name","id",$user).'" wurde in die Abteilung "'.fetch("departments","department","id",$department).'" zugewiesen.');

        $strSQL = "INSERT INTO department_assigns (id,user_id,department_id) VALUES ('$uid','$user','$department')";
        $rs=mysqli_query($link,$strSQL);

        echo '<meta http-equiv="refresh" content="0; url=/department_assign" />';
    }

    echo '
        <div id="fade_in"><h1>Abteilungszuweisung</h1></div>
        <div id="content_fade_in">
            <form action="/department_assign" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <center>
                    <div class="content_box_s">
                        <h2>Abteilungszugeh&ouml;rigkeit Eintragen</h2>
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
                        <br>Vertritt Abteilung<br>
                        <select name="department" class="selectbox_m t_selectbox">
                        ';
                            $strSQL = "SELECT * FROM departments";
                            $rs=mysqli_query($link,$strSQL);
                            while($row=mysqli_fetch_assoc($rs))
                            {
                                echo '<option value="'.$row['id'].'">'.$row['department'].'</option>';
                            }
                        echo '
                        </select>
                        <br><br>
                        <button type="submit" name="submit" class="button_m t_button">Zuweisung Hinzufügen</button>
                    </div>
                </center>
            </form>
        </div>
    ';

    include("footer.php");
?>