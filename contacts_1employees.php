<?php
    echo '<center><table class="orders_table">';

    $i=0;
    $strSQL = "SELECT * FROM users";
    $rs=mysqli_query($link,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        $style_color = ($i%2==0) ? 'style="background:#DBEDFF;"' : 'style="background:#F2F2F2;"';
        $style_id = ($i++%2==0) ? 'id="shaded_cell"' : '';

       echo '
        <tr>
            <td '.$style_id.'>
                <center>
                    <span style="font-size: 20pt; color:red;">&#11044;</span>
                </center>
            </td>
            <td '.$style_id.' width="120px">
                <img src="/files/content/user.png" alt="" style="height:100px;"/>
            </td>
            <td '.$style_id.'>
                <b>'.$row['last_name'].'</b><br>
                '.$row['first_name'].'<br>
                <sub>('.$row['id'].')</sub>
                <br><br>
                '.$row['room'].'
            </td>
            <td '.$style_id.'>
                <b>Kontakt:</b><br>
                '.(($row['phone_private']!="") ? ('Privat: '.$row['phone_private'].'<br>') : '').'
                '.(($row['phone_mobile']!="") ? ('Mobil: '.$row['phone_mobile'].'<br>') : '').'
                '.(($row['phone_office']!="") ? ('B&uuml;ro: '.$row['phone_office'].'<br>') : '').'
                <br>
                '.(($row['email']!="") ? ('E-Mail: <a href="mailto:'.$row['email'].'">'.$row['email'].'</a><br>') : '').'
            </td>
            <td '.$style_id.'>
                <b>Status:</b><br>
                //<span style="color: #32CD32">&#9679; Anwesend</span><br>
                //<span style="color: #696969">&#9679; Abwesend</span><br>
                //<span style="color: #FFA500">&#9679; Pause</span><br>
                //<span style="color: #CC0000">&#9679; Nicht st&ouml;ren</span>
            </td>
            <td '.$style_id.'>
                <a href="#chat:'.$row['id'].'"><button type="button" class="t_button button_m">Nachricht an<br>Mitarbeiter</button></a>
            </td>
        </tr>
       ';
    }

    echo '</table></center>';

?>