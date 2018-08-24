<?php
    include("header.php");

    echo '<div class="content_fade_in"><h1>Protokoll</h1><table class="orders_table">';

    $i=0;
    $strSQL = "SELECT * FROM protocol ORDER BY id DESC";
    $rs=mysqli_query($link,$strSQL);
    while($row=mysqli_fetch_assoc($rs))
    {
        echo '<tr>';
        $style_id = ($i++%2==0) ? 'id="shaded_cell"' : '';

        echo '
            <td '.$style_id.'>&nbsp;&nbsp;'.fetch("users","first_name","id",$row['user']).' '.fetch("users","last_name","id",$row['user']).'&nbsp;&nbsp;</td>
            <td '.$style_id.'>'.$row['description'].'<br><span style="color: #696969; font-size:10pt;">'.$row['date'].'</span></td>
        ';
    }


    echo '</table></div>';

    include("footer.php");
?>