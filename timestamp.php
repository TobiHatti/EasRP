<?php
    include("header.php");

    if(isset($_POST['stamp_on']) OR isset($_GET['stamp_on']))
    {
        $date = date("Y-m-d H:i:s");
        $user_id = $_SESSION['user_id'];
        $uid = uniqid();

        if(fetch("users","stamped","id",$_SESSION['user_id'])==0)
        {
            // Set current stamp id
			$strSQL = "UPDATE users SET current_ts_id = '$uid' WHERE id LIKE '$user_id'";
			$rs=mysqli_query($link,$strSQL);
			
			//Obsolete
            //$_SESSION['current_stamp_id']=$uid;

            // Stempel-Status setzen
            $strSQL = "UPDATE users SET stamped = '1' WHERE id LIKE '$user_id'";
            $rs=mysqli_query($link,$strSQL);

            //Neuen eintrag im stempel-log
            $strSQL = "INSERT INTO timestamp (id,user_id,come,go) VALUES ('$uid','$user_id','$date','0000-00-00 00:00:00')";
            $rs=mysqli_query($link,$strSQL);
        }

        protocol_add($_SESSION['first_name'].' '.$_SESSION['last_name'].' hat Angestempelt.');

        echo '<meta http-equiv="refresh" content="0; url=/timestamp?personal_timestamps" />';
    }

    if(isset($_POST['stamp_off']) OR isset($_GET['stamp_off']))
    {
        $date = date("Y-m-d H:i:s");
        $user_id = $_SESSION['user_id'];

        // Test if user is in fact stamped on
        if((fetch("users","stamped","id",$_SESSION['user_id']))==1)
        {
            //get current stamp id
            $uid = fetch("users","current_ts_id","id",$_SESSION['user_id']);

            // Get current worktimes
            $strSQL = "SELECT * FROM timestamp WHERE id LIKE '$uid'";
            $rs=mysqli_query($link,$strSQL);
            while($row=mysqli_fetch_assoc($rs))
            {
                // Test if worktime is longer than 10s
                if((strtotime(date("Y-m-d H:i:s")) - strtotime($row['come']))<10)
                {
                    // Delete Timestamp entry
                    $strSQLd = "DELETE FROM timestamp WHERE id LIKE '$uid'";
                    $rsd=mysqli_query($link,$strSQLd);

                    // Set stamp state
                    $strSQLu = "UPDATE users SET stamped = '0' WHERE id LIKE '$user_id'";
                    $rsu=mysqli_query($link,$strSQLu);

                    echo '<meta http-equiv="refresh" content="0; url=/timestamp?personal_timestamps&error_time" />';
                }
                else
                {
                    // Set stamp state
                    $strSQLu = "UPDATE users SET stamped = '0' WHERE id LIKE '$user_id'";
                    $rsu=mysqli_query($link,$strSQLu);

                    // add stamp entry
                    $strSQLu = "UPDATE timestamp SET go = '$date' WHERE user_id LIKE '$user_id' AND go LIKE '0000-00-00 00:00:00'";
                    $rsu=mysqli_query($link,$strSQLu);

                    protocol_add($_SESSION['first_name'].' '.$_SESSION['last_name'].' hat Abgestempelt.');

                    echo '<meta http-equiv="refresh" content="0; url=/timestamp?personal_timestamps" />';
                }
            }
        }
    }

    if(isset($_GET['delete']))
    {
        $tid = $_GET['delete'];
        $strSQL = "DELETE FROM timestamp WHERE id LIKE '$tid'";
        $rs=mysqli_query($link,$strSQL);

        protocol_add($_SESSION['first_name'].' '.$_SESSION['last_name'].' hat eine Stempelzeit entfernt.');

        echo '<meta http-equiv="refresh" content="0; url=/timestamp?time_correction&delete_entry" />';
    }

    if(isset($_POST['update_timestamp']))
    {
        $tid = $_POST['update_timestamp'];
        $come_time = strtotime(str_replace("T"," ",$_POST['come_time']));
        $leave_time = strtotime(str_replace("T"," ",$_POST['leave_time']));

        $come_t = str_replace("T"," ",$_POST['come_time']);
        $leave_t = str_replace("T"," ",$_POST['leave_time']);

        if(($leave_time - $come_time) < 0) echo '<meta http-equiv="refresh" content="0; url=/timestamp?time_correction&edit_entry&error_t" />';
        else
        {
            $strSQL = "UPDATE timestamp SET
            come = '$come_t',
            go = '$leave_t'
            WHERE id LIKE '$tid'";
            $rs=mysqli_query($link,$strSQL);

            protocol_add($_SESSION['first_name'].' '.$_SESSION['last_name'].' hat eine Stempelzeit Aktualisiert.');

            echo '<meta http-equiv="refresh" content="0; url=/timestamp?time_correction&edit_entry" />';
        }
    }

    if(isset($_POST['add_timestamp']))
    {
        $tid = uniqid();
        $come_time = strtotime(str_replace("T"," ",$_POST['come_time']));
        $leave_time = strtotime(str_replace("T"," ",$_POST['leave_time']));
        $user_id = $_SESSION['user_id'];

        $come_t = str_replace("T"," ",$_POST['come_time']);
        $leave_t = str_replace("T"," ",$_POST['leave_time']);

        if(($leave_time - $come_time) < 0) echo '<meta http-equiv="refresh" content="0; url=/timestamp?time_correction&add_entry&error_t" />';
        else
        {
            $strSQL = "INSERT INTO timestamp (id,user_id,come,go) VALUES ('$tid','$user_id','$come_t','$leave_t')";
            $rs=mysqli_query($link,$strSQL);

            protocol_add($_SESSION['first_name'].' '.$_SESSION['last_name'].' hat eine Stempelzeit nachgetragen.');

            echo '<meta http-equiv="refresh" content="0; url=/timestamp?time_correction&edit_entry" />';
        }
    }

    echo '
        <div id="fade_in">
            <h1>Zeiterfassung</h1>
            <div id="content_fade_in">
                <form action="/timestamp" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                    <center>
                        <div class="content_box_s">
                            <h2>Zeiterfassung</h2>
                            ';
                                if(isset($_GET['error_time'])) echo '<span style="color: #CC0000">Arbeitszeiten unter 10s werden nicht angerechnet</span>';
                            echo '
                            <br>
                            ';
                                if(fetch("users","stamped","id",$_SESSION['user_id'])==0)
                                {
                                    $last_time = get_last_timestamp($_SESSION['user_id']);

                                    echo '
                                        <button type="submit" name="stamp_on" class="button_xl t_button">Anstempeln<br><i><span style="color: #A9A9A9">Kommen</span></i></button>
                                        <br>
                                        <br>
                                        Zuletzt gegangen am <b>'.date("d.m.Y \u\m H:i:s",last_left($_SESSION['user_id'])).'</b>
                                        <br><br>
                                        Letzte Arbeitszeit: <b>'.convert_s_to_date($last_time).'</b>
                                    ';
                                }
                                else
                                {
                                    $current_time = get_current_timestamp($_SESSION['user_id']);

                                    echo '
                                        <button type="submit" name="stamp_off" class="button_xl t_button">Abstempeln<br><i><span style="color: #A9A9A9">Gehen</span></i></button>
                                        <br>
                                        <br>
                                        Gekommen am <b>'.date("d.m.Y \u\m H:i:s",last_come($_SESSION['user_id'])).'</b>
                                        <br><br>
                                        Aktuelle Arbeitszeit: <b>'.convert_s_to_date($current_time).'</b>
                                    ';
                                }
                            echo '
                        </div>
                    </center>
                </form>
                <br>
                ';
                    $selected_register1='';
                    $selected_register2='';
                    $selected_register3='';
                    $selected_register4='';

                    if(isset($_GET['personal_timestamps'])) $selected_register1='id="selected_register"';
                    if(isset($_GET['other_timestamps'])) $selected_register2='id="selected_register"';
                    if(isset($_GET['all_timestamps'])) $selected_register3='id="selected_register"';
                    if(isset($_GET['time_correction'])) $selected_register4='id="selected_register"';
                echo '

                    <a href="timestamp?personal_timestamps"><button type="button" class="button_m t_button" '.$selected_register1.' style="margin-bottom:0px; border-bottom:none; vertical-align:bottom">Deine Stempelzeiten</button></a>
                    <a href="timestamp?other_timestamps"><button type="button" class="button_m t_button" '.$selected_register2.' style="margin-bottom:0px; border-bottom:none; vertical-align:bottom">Fremde Stempelzeiten</button></a>
                    <a href="timestamp?all_timestamps"><button type="button" class="button_m t_button" '.$selected_register3.' style="margin-bottom:0px; border-bottom:none; vertical-align:bottom">Alle Stempelzeiten</button></a>
                    <a href="timestamp?time_correction&add_entry"><button type="button" class="button_m t_button" '.$selected_register4.' style="margin-bottom:0px; border-bottom:none; vertical-align:bottom;">Zeitkorrektur</button></a>
                    <hr style="margin-top:0px;">
                    <hr>
                ';
                if(!isset($_GET['time_correction']))
                {
                    echo '

                    <center>
                        <table class="timestamp_table">
                            <tr>
                                <th class="timestamp_cell" style="font-weight:normal; color: #1E90FF">Nutzer</th>
                                <th class="timestamp_cell" style="font-weight:normal; color: #1E90FF">Gekommen</th>
                                <th class="timestamp_cell" style="font-weight:normal; color: #1E90FF">Gegangen</th>
                                <th class="timestamp_cell" style="font-weight:normal; color: #1E90FF">Zeit gearbeitet</th>
                            </tr>
                        ';
                            $i=0;
                            $strSQL = "SELECT * FROM timestamp ORDER BY come DESC";
                            $rs=mysqli_query($link,$strSQL);
                            while($row=mysqli_fetch_assoc($rs))
                            {
                                if($i%2 == 0) $style_id = 'id="shaded_cell"';
                                else $style_id= '';

                                $show_timestamp = 0;
                                if(isset($_GET['personal_timestamps']) AND $row['user_id'] == $_SESSION['user_id']) $show_timestamp = 1;
                                if(isset($_GET['other_timestamps']) AND $row['user_id'] != $_SESSION['user_id']) $show_timestamp = 1;
                                if(isset($_GET['all_timestamps'])) $show_timestamp = 1;

                                if($show_timestamp == 1)
                                {
                                    if($row['go'] != '0000-00-00 00:00:00')
                                    {
                                        echo '
                                            <tr>
                                                <td '.$style_id.' class="timestamp_cell" style="text-align:left;">'.fetch("users","first_name","id",$row['user_id']).' '.fetch("users","last_name","id",$row['user_id']).'</td>
                                                <td '.$style_id.' class="timestamp_cell">'.date("D, d M Y - H:i:s",strtotime($row['come'])).'</td>
                                                <td '.$style_id.' class="timestamp_cell">'.date("D, d M Y - H:i:s",strtotime($row['go'])).'</td>
                                                <td '.$style_id.' class="timestamp_cell">'.convert_s_to_date(strtotime($row['go']) - strtotime($row['come'])).'</td>
                                            </tr>
                                        ';
                                    }
                                    else
                                    {
                                        echo '
                                            <tr>
                                                <td '.$style_id.' class="timestamp_cell" style="text-align:left;">'.fetch("users","first_name","id",$row['user_id']).' '.fetch("users","last_name","id",$row['user_id']).'</td>
                                                <td '.$style_id.' class="timestamp_cell">'.date("D, d M Y - H:i:s",strtotime($row['come'])).'</td>
                                                <td '.$style_id.' class="timestamp_cell">---</td>
                                                <td '.$style_id.' class="timestamp_cell">'.convert_s_to_date(strtotime(date("Y-m-d H:i:s")) - strtotime($row['come'])).'</td>
                                            </tr>
                                        ';
                                    }
                                    $i++;
                                }
                            }
                        echo '
                        </table>
                    </center>
                    ';
                }
                else
                {
                    $selected_register4_1='';
                    $selected_register4_2='';
                    $selected_register4_3='';

                    if(isset($_GET['add_entry'])) $selected_register4_1='id="selected_register"';
                    if(isset($_GET['delete_entry'])) $selected_register4_2='id="selected_register"';
                    if(isset($_GET['edit_entry'])) $selected_register4_3='id="selected_register"';

                    echo '
                        <a href="timestamp?time_correction&add_entry"><button type="button" class="button_m t_button" '.$selected_register4_1.' style="margin-bottom:0px; border-bottom:none; vertical-align:bottom;">Zeit Nachtragen</button></a>
                        <a href="timestamp?time_correction&delete_entry"><button type="button" class="button_m t_button" '.$selected_register4_2.' style="margin-bottom:0px; border-bottom:none; vertical-align:bottom;">Eintrag L&ouml;schen</button></a>
                        <a href="timestamp?time_correction&edit_entry"><button type="button" class="button_m t_button" '.$selected_register4_3.' style="margin-bottom:0px; border-bottom:none; vertical-align:bottom;">Eintrag Bearbeiten</button></a>
                        <hr style="margin-top:0px;">
                    ';

                    if(isset($_GET['add_entry']))
                    {
                        if(isset($_GET['error_t'])) echo '<center><span style="color: #CC0000">Die "Gehen-Zeit" kann nicht vor der "Kommen-Zeit" sein!</span></center>';
                        echo '
                            <form action="/timestamp" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                                <center>
                                    <br>
                                    <div class="content_box_s">
                                        <h3>Eintrag Bearbeiten</h3>
                                        <br>
                                        Gekommen:
                                        <input type="datetime-local" id="come_time" step="1" name="come_time" class="textfield_m t_textfield">
                                        <br><br>
                                        Gegangen:
                                        <input type="datetime-local" id="leave_time" step="1" name="leave_time" class="textfield_m t_textfield">
                                        <br><br>
                                        <button type="submit" name="add_timestamp" class="button_m t_button">Eintrag Erg&auml;nzen</button>
                                    </div>
                                </center>
                            </form>
                        ';
                    }

                    if(isset($_GET['delete_entry']))
                    {
                        echo '
                            <center>
                                <table class="timestamp_table">
                                    <tr>
                                        <th class="timestamp_cell" style="font-weight:normal; color: #1E90FF">Option</th>
                                        <th class="timestamp_cell" style="font-weight:normal; color: #1E90FF">Nutzer</th>
                                        <th class="timestamp_cell" style="font-weight:normal; color: #1E90FF">Gekommen</th>
                                        <th class="timestamp_cell" style="font-weight:normal; color: #1E90FF">Gegangen</th>
                                        <th class="timestamp_cell" style="font-weight:normal; color: #1E90FF">Zeit gearbeitet</th>
                                    </tr>
                        ';
                        $user_id = $_SESSION['user_id'];
                        $i=0;
                        $strSQL = "SELECT * FROM timestamp WHERE user_id LIKE '$user_id' AND go NOT LIKE '0000-00-00 00:00:00' ORDER BY come DESC";
                        $rs=mysqli_query($link,$strSQL);
                        while($row=mysqli_fetch_assoc($rs))
                        {
                            if($i%2 == 0) $style_id = 'id="shaded_cell_gray"';
                            else $style_id= '';

                                echo '
                                <tr>
                                    <td '.$style_id.' class="timestamp_cell"><a href="timestamp?delete='.$row['id'].'"><span style="color: #CC0000">&#x2718; Eintrag L&ouml;schen</span></a></td>
                                    <td '.$style_id.' class="timestamp_cell" style="text-align:left;">'.fetch("users","first_name","id",$row['user_id']).' '.fetch("users","last_name","id",$row['user_id']).'</td>
                                    <td '.$style_id.' class="timestamp_cell">'.date("D, d M Y - H:i:s",strtotime($row['come'])).'</td>
                                    <td '.$style_id.' class="timestamp_cell">'.date("D, d M Y - H:i:s",strtotime($row['go'])).'</td>
                                    <td '.$style_id.' class="timestamp_cell">'.convert_s_to_date(strtotime($row['go']) - strtotime($row['come'])).'</td>
                                </tr>
                            ';

                            $i++;
                        }

                        echo '
                                </table>
                            </center>
                        ';
                    }

                    if(isset($_GET['edit_entry']))
                    {
                        if(isset($_GET['error_t'])) echo '<center><span style="color: #CC0000">Die "Gehen-Zeit" kann nicht vor der "Kommen-Zeit" sein!</span></center>';
                        if(!isset($_GET['timestamp']))
                        {
                            echo '
                                <center>
                                    <table class="timestamp_table">
                                        <tr>
                                            <th class="timestamp_cell" style="font-weight:normal; color: #1E90FF">Option</th>
                                            <th class="timestamp_cell" style="font-weight:normal; color: #1E90FF">Nutzer</th>
                                            <th class="timestamp_cell" style="font-weight:normal; color: #1E90FF">Gekommen</th>
                                            <th class="timestamp_cell" style="font-weight:normal; color: #1E90FF">Gegangen</th>
                                            <th class="timestamp_cell" style="font-weight:normal; color: #1E90FF">Zeit gearbeitet</th>
                                        </tr>
                            ';
                            $user_id = $_SESSION['user_id'];
                            $i=0;
                            $strSQL = "SELECT * FROM timestamp WHERE user_id LIKE '$user_id' AND go NOT LIKE '0000-00-00 00:00:00' ORDER BY come DESC";
                            $rs=mysqli_query($link,$strSQL);
                            while($row=mysqli_fetch_assoc($rs))
                            {
                                if($i%2 == 0) $style_id = 'id="shaded_cell_gray"';
                                else $style_id= '';

                                    echo '
                                    <tr>
                                        <td '.$style_id.' class="timestamp_cell"><a href="/timestamp?time_correction&edit_entry&timestamp='.$row['id'].'"><span style="color: #FFA500">&#x270E; Eintrag Bearbeiten</span></a></td>
                                        <td '.$style_id.' class="timestamp_cell" style="text-align:left;">'.fetch("users","first_name","id",$row['user_id']).' '.fetch("users","last_name","id",$row['user_id']).'</td>
                                        <td '.$style_id.' class="timestamp_cell">'.date("D, d M Y - H:i:s",strtotime($row['come'])).'</td>
                                        <td '.$style_id.' class="timestamp_cell">'.date("D, d M Y - H:i:s",strtotime($row['go'])).'</td>
                                        <td '.$style_id.' class="timestamp_cell">'.convert_s_to_date(strtotime($row['go']) - strtotime($row['come'])).'</td>
                                    </tr>
                                ';

                                $i++;
                            }

                            echo '
                                    </table>
                                </center>
                            ';
                        }
                        else
                        {
                            echo '
                                <script>
                                    window.onload = function set_datetime()
                                    {
                                        document.getElementById("come_time").value="'.str_replace(" ","T",fetch("timestamp","come","id",$_GET['timestamp'])).'"
                                        document.getElementById("leave_time").value="'.str_replace(" ","T",fetch("timestamp","go","id",$_GET['timestamp'])).'"
                                    }
                                </script>
                                <form action="/timestamp" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                                    <center>
                                        <br>
                                        <div class="content_box_s">
                                            <h3>Eintrag Bearbeiten</h3>
                                            <br>
                                            Gekommen:
                                            <input type="datetime-local" id="come_time" step="1" name="come_time" class="textfield_m t_textfield">
                                            <br><br>
                                            Gegangen:
                                            <input type="datetime-local" id="leave_time" step="1" name="leave_time" class="textfield_m t_textfield">
                                            <br><br>
                                            <button type="submit" name="update_timestamp" class="button_m t_button" value="'.$_GET['timestamp'].'">Eintrag Aktualisieren</button>
                                        </div>
                                    </center>
                                </form>
                            ';
                        }
                    }
                }
                echo '
            </div>
        </div>
    ';

    include("footer.php");
?>