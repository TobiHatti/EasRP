<?php
    include("header.php");

    if(isset($_POST['add_task']))
    {
        $id = uniqid();
        $uid = $_SESSION['user_id'];
        $description = $_POST['description'];
        $status = 'unstarted';
        $points = $_POST['points'];

        protocol_add('SCRUM-Task "'.$description.'" wurde von '.fetch("users","first_name","id",$_SESSION['user_id']).' '.fetch("users","last_name","id",$_SESSION['user_id']).' erstellt.');

        switch(rand(1,6))
        {
            case 1: $color = "9EFFFF";break;
            case 2: $color = "FF9EFF";break;
            case 3: $color = "FFFF9E";break;
            case 4: $color = "9E9EFF";break;
            case 5: $color = "9EFF9E";break;
            case 6: $color = "FF9E9E";break;
        }

        $strSQL = "INSERT INTO scrum_tasks (id,user_id,description,points,status,color) VALUES ('$id','$uid','$description','$points','$status','$color')";
        $rs=mysqli_query($link,$strSQL);

        echo '<meta http-equiv="refresh" content="0; url=/scrum_blackboard" />';
    }

    if(isset($_GET['delete']))
    {
        $id = $_GET['delete'];

        protocol_add('SCRUM-Task "'.fetch("scrum_tasks","description","id",$id).'" wurde von '.fetch("users","first_name","id",$_SESSION['user_id']).' '.fetch("users","last_name","id",$_SESSION['user_id']).' gel&ouml;scht.');

        $strSQL = "DELETE FROM scrum_tasks WHERE id = '$id'";
        $rs=mysqli_query($link,$strSQL);

        echo '<meta http-equiv="refresh" content="0; url=/scrum_blackboard" />';
    }

    if(isset($_GET['to_unstarted']))
    {
        $id = $_GET['to_unstarted'];

        protocol_add('SCRUM-Task "'.fetch("scrum_tasks","description","id",$id).'" wurde auf "Unstarted" gesetzt.');

        $strSQL = "UPDATE scrum_tasks SET status = 'unstarted' WHERE id = '$id'";
        $rs=mysqli_query($link,$strSQL);

        echo '<meta http-equiv="refresh" content="0; url=/scrum_blackboard" />';
    }

    if(isset($_GET['to_progress']))
    {
        $id = $_GET['to_progress'];

        protocol_add('SCRUM-Task "'.fetch("scrum_tasks","description","id",$id).'" wurde auf "In progress" gesetzt.');

        $strSQL = "UPDATE scrum_tasks SET status = 'progress' WHERE id = '$id'";
        $rs=mysqli_query($link,$strSQL);

        echo '<meta http-equiv="refresh" content="0; url=/scrum_blackboard" />';
    }

    if(isset($_GET['to_validate']))
    {
        $id = $_GET['to_validate'];

        protocol_add('SCRUM-Task "'.fetch("scrum_tasks","description","id",$id).'" wurde auf "To be validated" gesetzt.');

        $strSQL = "UPDATE scrum_tasks SET status = 'validate' WHERE id = '$id'";
        $rs=mysqli_query($link,$strSQL);

        echo '<meta http-equiv="refresh" content="0; url=/scrum_blackboard" />';
    }

    if(isset($_GET['to_done']))
    {
        $id = $_GET['to_done'];

        protocol_add('SCRUM-Task "'.fetch("scrum_tasks","description","id",$id).'" wurde auf "Done" gesetzt.');

        $strSQL = "UPDATE scrum_tasks SET status = 'done' WHERE id = '$id'";
        $rs=mysqli_query($link,$strSQL);

        echo '<meta http-equiv="refresh" content="0; url=/scrum_blackboard" />';
    }

    if(isset($_GET['claim']))
    {
        $id = $_GET['claim'];
        $uid = $_SESSION['user_id'];
        $strSQL = "UPDATE scrum_tasks SET user_id = '$uid' WHERE id = '$id'";
        $rs=mysqli_query($link,$strSQL);

        protocol_add('SCRUM-Task "'.fetch("scrum_tasks","description","id",$id).'" wurde von '.fetch("users","first_name","id",$_SESSION['user_id']).' '.fetch("users","last_name","id",$_SESSION['user_id']).' geclaimt.');

        echo '<meta http-equiv="refresh" content="0; url=/scrum_blackboard" />';
    }

    if(isset($_POST['update_points']))
    {
        $id = $_POST['update_points'];
        $points = $_POST['points_update'];

        protocol_add('SCRUM-Tasks "'.fetch("scrum_tasks","description","id",$id).'" Storypoints wurden auf "'.$points.'" ge&auml;ndert.');

        $strSQL = "UPDATE scrum_tasks SET points = '$points' WHERE id = '$id'";
        $rs=mysqli_query($link,$strSQL);

        echo '<meta http-equiv="refresh" content="0; url=/scrum_blackboard" />';
    }

    echo '

        <div id="fade_in"><h1>SCRUM - Blackboard</h1></div><div id="content_fade_in">

        <a href="#add_task"><button class="button_m t_button" type="button">Neue Task hinzuf&uuml;gen</button></a>

        <center>
        <form action="/scrum_blackboard" method="post" accept-charset="utf-8" enctype="multipart/form-data">
            <div id="add_task" class="modalDialog">
                <div>
                    <a href="#close" title="Close" class="close">X</a>
                    <h2 class="slim">Neue Task hinzuf&uuml;gen</h2>

                    <br><br>
                    <textarea class="textarea_m t_textfield" placeholder="Beschreibung..." name="description"></textarea><br>
                    <input type="number" class="textfield_m t_textfield" placeholder="Story-Points" name="points">
                    <br><br>
                    <button class="button_m t_button" type="submit" name="add_task">Task hinzuf&uuml;gen</button>
                </div>
            </div>
        </form>
        </center>

        <svg width="550" height="20">

            <circle cx="50" cy="12" r="8"  style="fill:#FFA500;" />
            <text x="45" y="17" fill="#000000">P</text>
            <text x="60" y="17" fill="#000000">= [Points] Storypoints &auml;ndern</text>

            <circle cx="300" cy="12" r="8"  style="fill:#FFA500;" />
            <text x="295" y="17" fill="#000000">C</text>
            <text x="310" y="17" fill="#000000">= [Claim] Task beanspruchen</text>


        </svg>

        <center>
            <table>
                <tr>
                    <td class="blackboard_cell" style="background:#61B0FF"><span style="font-size: 26pt">Unstarted</span></td>
                    <td class="blackboard_cell" style="background:#FF8585"><span style="font-size: 26pt">In progress</span></td>
                    <td class="blackboard_cell" style="background:#FFC252"><span style="font-size: 26pt">To be validated</span></td>
                    <td class="blackboard_cell" style="background:#73DD73"><span style="font-size: 26pt">Done</span></td>
                </tr>
                <tr>
                    <td class="blackboard_cell" style="background:#F0F7FF">
                        <center>
                        ';

                        $strSQL = "SELECT * FROM scrum_tasks WHERE status = 'unstarted'";
                        $rs=mysqli_query($link,$strSQL);
                        while($row=mysqli_fetch_assoc($rs))
                        {
                            $rotation = rand(-5,5);

                            echo '
                                <div class="scrum_task" style="transform: rotate('.$rotation.'deg);">
                                    <svg width="150" height="20">
                                        <polygon points="0,0 130,0 152,20 130,20 130,0 152,20 152,25 0,25" style="fill:#'.$row['color'].';" stroke="#000000"/>

                                        <a href="/scrum_blackboard?delete='.$row['id'].'" style="cursor:pointer;">
                                        <circle cx="10" cy="12" r="8"  style="fill:#E00000;" />
                                        <text x="5" y="17" fill="#000000">X</text>
                                        </a>
                                        <a href="#update_points'.$row['id'].'" style="cursor:pointer;">
                                        <circle cx="50" cy="12" r="8"  style="fill:#FFA500;" />
                                        <text x="45" y="17" fill="#000000">P</text>
                                        </a>
                                        <a href="/scrum_blackboard?claim='.$row['id'].'" style="cursor:pointer;">
                                        <circle cx="70" cy="12" r="8"  style="fill:#FFA500;" />
                                        <text x="65" y="17" fill="#000000">C</text>
                                        </a>
                                        <a href="/scrum_blackboard?to_progress='.$row['id'].'" style="cursor:pointer;">
                                        <circle cx="120" cy="12" r="8"  style="fill:#1E90FF;" />
                                        <text x="115" y="17" fill="#000000">&gt;</text>
                                        </a>
                                    </svg>
                                    <textarea class="scrum_description" style="background:#'.$row['color'].';" readonly>'.$row['description'].'</textarea>
                                    <textarea class="scrum_points" style="background:#'.$row['color'].';" readonly>'.$row['points'].' Storypoints</textarea>
                                    <textarea class="scrum_author"  style="background:#'.$row['color'].';" readonly>'.fetch("users","first_name","id",$row['user_id']).' '.fetch("users","last_name","id",$row['user_id']).'</textarea>
                                </div>
                                <form action="/scrum_blackboard" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                                    <div id="update_points'.$row['id'].'" class="modalDialog">
                                        <div>
                                            <a href="#close" title="Close" class="close">X</a>
                                            <h2 class="slim">Storypoints Aktualisieren</h2>

                                            <input type="number" class="textfield_m t_textfield" placeholder="Story-Points" name="points_update">
                                            <br><br>
                                            <button class="button_m t_button" type="submit" name="update_points" value="'.$row['id'].'">Update</button>
                                        </div>
                                    </div>
                                </form>
                            ';
                        }
                        echo '

                        </center>
                    </td>
                    <td class="blackboard_cell" style="background:#FFF0F0">
                        <center>
                        ';

                        $strSQL = "SELECT * FROM scrum_tasks WHERE status = 'progress'";
                        $rs=mysqli_query($link,$strSQL);
                        while($row=mysqli_fetch_assoc($rs))
                        {
                            $rotation = rand(-5,5);

                            echo '
                                <div class="scrum_task" style="transform: rotate('.$rotation.'deg);">
                                    <svg width="150" height="20">
                                        <polygon points="0,0 130,0 152,20 130,20 130,0 152,20 152,25 0,25" style="fill:#'.$row['color'].';" stroke="#000000"/>

                                        <a href="/scrum_blackboard?delete='.$row['id'].'" style="cursor:pointer;">
                                        <circle cx="10" cy="12" r="8"  style="fill:#E00000;" />
                                        <text x="5" y="17" fill="#000000">X</text>
                                        </a>
                                        <a href="#update_points'.$row['id'].'" style="cursor:pointer;">
                                        <circle cx="50" cy="12" r="8"  style="fill:#FFA500;" />
                                        <text x="45" y="17" fill="#000000">P</text>
                                        </a>
                                        <a href="/scrum_blackboard?claim='.$row['id'].'" style="cursor:pointer;">
                                        <circle cx="70" cy="12" r="8"  style="fill:#FFA500;" />
                                        <text x="65" y="17" fill="#000000">C</text>
                                        </a>
                                        <a href="/scrum_blackboard?to_unstarted='.$row['id'].'" style="cursor:pointer;">
                                        <circle cx="100" cy="12" r="8"  style="fill:#1E90FF;" />
                                        <text x="95" y="17" fill="#000000">&lt;</text>
                                        </a>
                                        <a href="/scrum_blackboard?to_validate='.$row['id'].'" style="cursor:pointer;">
                                        <circle cx="120" cy="12" r="8"  style="fill:#1E90FF;" />
                                        <text x="115" y="17" fill="#000000">&gt;</text>
                                        </a>
                                    </svg>
                                    <textarea class="scrum_description" style="background:#'.$row['color'].';" readonly>'.$row['description'].'</textarea>
                                    <textarea class="scrum_points" style="background:#'.$row['color'].';" readonly>'.$row['points'].' Storypoints</textarea>
                                    <textarea class="scrum_author"  style="background:#'.$row['color'].';" readonly>'.fetch("users","first_name","id",$row['user_id']).' '.fetch("users","last_name","id",$row['user_id']).'</textarea>
                                </div>
                                <form action="/scrum_blackboard" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                                    <div id="update_points'.$row['id'].'" class="modalDialog">
                                        <div>
                                            <a href="#close" title="Close" class="close">X</a>
                                            <h2 class="slim">Storypoints Aktualisieren</h2>

                                            <input type="number" class="textfield_m t_textfield" placeholder="Story-Points" name="points_update">
                                            <br><br>
                                            <button class="button_m t_button" type="submit" name="update_points" value="'.$row['id'].'">Update</button>
                                        </div>
                                    </div>
                                </form>
                            ';
                        }
                        echo '

                        </center>
                    </td>
                    <td class="blackboard_cell" style="background:#FFFAF0">
                        <center>
                        ';

                        $strSQL = "SELECT * FROM scrum_tasks WHERE status = 'validate'";
                        $rs=mysqli_query($link,$strSQL);
                        while($row=mysqli_fetch_assoc($rs))
                        {
                            $rotation = rand(-5,5);

                            echo '
                                <div class="scrum_task" style="transform: rotate('.$rotation.'deg);">
                                    <svg width="150" height="20">
                                        <polygon points="0,0 130,0 152,20 130,20 130,0 152,20 152,25 0,25" style="fill:#'.$row['color'].';" stroke="#000000"/>

                                        <a href="/scrum_blackboard?delete='.$row['id'].'" style="cursor:pointer;">
                                        <circle cx="10" cy="12" r="8"  style="fill:#E00000;" />
                                        <text x="5" y="17" fill="#000000">X</text>
                                        </a>
                                        <a href="#update_points'.$row['id'].'" style="cursor:pointer;">
                                        <circle cx="50" cy="12" r="8"  style="fill:#FFA500;" />
                                        <text x="45" y="17" fill="#000000">P</text>
                                        </a>
                                        <a href="/scrum_blackboard?claim='.$row['id'].'" style="cursor:pointer;">
                                        <circle cx="70" cy="12" r="8"  style="fill:#FFA500;" />
                                        <text x="65" y="17" fill="#000000">C</text>
                                        </a>
                                        <a href="/scrum_blackboard?to_progress='.$row['id'].'" style="cursor:pointer;">
                                        <circle cx="100" cy="12" r="8"  style="fill:#1E90FF;" />
                                        <text x="95" y="17" fill="#000000">&lt;</text>
                                        </a>
                                        <a href="/scrum_blackboard?to_done='.$row['id'].'" style="cursor:pointer;">
                                        <circle cx="120" cy="12" r="8"  style="fill:#1E90FF;" />
                                        <text x="115" y="17" fill="#000000">&gt;</text>
                                        </a>
                                    </svg>
                                    <textarea class="scrum_description" style="background:#'.$row['color'].';" readonly>'.$row['description'].'</textarea>
                                    <textarea class="scrum_points" style="background:#'.$row['color'].';" readonly>'.$row['points'].' Storypoints</textarea>
                                    <textarea class="scrum_author"  style="background:#'.$row['color'].';" readonly>'.fetch("users","first_name","id",$row['user_id']).' '.fetch("users","last_name","id",$row['user_id']).'</textarea>
                                </div>
                                <form action="/scrum_blackboard" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                                    <div id="update_points'.$row['id'].'" class="modalDialog">
                                        <div>
                                            <a href="#close" title="Close" class="close">X</a>
                                            <h2 class="slim">Storypoints Aktualisieren</h2>

                                            <input type="number" class="textfield_m t_textfield" placeholder="Story-Points" name="points_update">
                                            <br><br>
                                            <button class="button_m t_button" type="submit" name="update_points" value="'.$row['id'].'">Update</button>
                                        </div>
                                    </div>
                                </form>
                            ';
                        }
                        echo '

                        </center>
                    </td>
                    <td class="blackboard_cell" style="background:#F7FDF7">
                        <center>
                        ';

                        $strSQL = "SELECT * FROM scrum_tasks WHERE status = 'done'";
                        $rs=mysqli_query($link,$strSQL);
                        while($row=mysqli_fetch_assoc($rs))
                        {
                            $rotation = rand(-5,5);

                            echo '
                                <div class="scrum_task" style="transform: rotate('.$rotation.'deg);">
                                    <svg width="150" height="20">
                                        <polygon points="0,0 130,0 152,20 130,20 130,0 152,20 152,25 0,25" style="fill:#'.$row['color'].';" stroke="#000000"/>

                                        <a href="/scrum_blackboard?delete='.$row['id'].'" style="cursor:pointer;">
                                        <circle cx="10" cy="12" r="8"  style="fill:#E00000;" />
                                        <text x="5" y="17" fill="#000000">X</text>
                                        </a>
                                        <a href="#update_points'.$row['id'].'" style="cursor:pointer;">
                                        <circle cx="50" cy="12" r="8"  style="fill:#FFA500;" />
                                        <text x="45" y="17" fill="#000000">P</text>
                                        </a>
                                        <a href="/scrum_blackboard?claim='.$row['id'].'" style="cursor:pointer;">
                                        <circle cx="70" cy="12" r="8"  style="fill:#FFA500;" />
                                        <text x="65" y="17" fill="#000000">C</text>
                                        </a>
                                        <a href="/scrum_blackboard?to_validate='.$row['id'].'" style="cursor:pointer;">
                                        <circle cx="100" cy="12" r="8"  style="fill:#1E90FF;" />
                                        <text x="95" y="17" fill="#000000">&lt;</text>
                                        </a>
                                    </svg>
                                    <textarea class="scrum_description" style="background:#'.$row['color'].';" readonly>'.$row['description'].'</textarea>
                                    <textarea class="scrum_points" style="background:#'.$row['color'].';" readonly>'.$row['points'].' Storypoints</textarea>
                                    <textarea class="scrum_author"  style="background:#'.$row['color'].';" readonly>'.fetch("users","first_name","id",$row['user_id']).' '.fetch("users","last_name","id",$row['user_id']).'</textarea>
                                </div>
                                <form action="/scrum_blackboard" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                                    <div id="update_points'.$row['id'].'" class="modalDialog">
                                        <div>
                                            <a href="#close" title="Close" class="close">X</a>
                                            <h2 class="slim">Storypoints Aktualisieren</h2>

                                            <input type="number" class="textfield_m t_textfield" placeholder="Story-Points" name="points_update">
                                            <br><br>
                                            <button class="button_m t_button" type="submit" name="update_points" value="'.$row['id'].'">Update</button>
                                        </div>
                                    </div>
                                </form>
                            ';
                        }
                        echo '

                        </center>
                    </td>
                </tr>
            </table>
        </center>
        <br>
        <a href="#add_task"><button class="button_m t_button" type="button">Neue Task hinzuf&uuml;gen</button></a>
    </div>

    ';

    include("footer.php");
?>