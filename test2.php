<?php

    require("data/mysql_connect.php");
    try
    {

        mysqli_query($link,"START TRANSACTION");
        mysqli_query($link,"INSERT INTO users (id) VALUES ('123456')");
        mysqli_query($link,"INSERT INTO usersrer (idu) VALUES ('123456')");
        mysqli_query($link,"COMMIT");
    }
    catch(Exception $ex)
    {
        mysqli_query($link,"ROLLBACK");
    }



?>