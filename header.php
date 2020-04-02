<?php
    session_start();
    
    error_reporting(E_ERROR | E_PARSE);  

    include("data/mysql_connect.php");
    include("data/functions.php");
    include("data/multipage_post.php");

    $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    if(!isset($_SESSION['language']))
    {
        $_SESSION['language']=GetProperty("deflang_erp");
    }

    if(isset($_POST['change_liststyle']))
    {
        $_SESSION['list_style'] = $_POST['change_liststyle'];
    }
    else $_SESSION['list_style'] = 'list';


    if(isset($_COOKIE['user_id']) AND !isset($_SESSION['user_id']))
    {
        $user_id_load = $_COOKIE['user_id'];
        $strSQL = "SELECT * FROM users WHERE id LIKE '$user_id_load'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['last_name'] = $row['last_name'];
            $_SESSION['user_id'] = $row['id'];
        }
    }

    if(!isset($_SESSION['user_id']) AND basename($_SERVER["SCRIPT_FILENAME"], '.php')!='account')
    {
        echo '<meta http-equiv="refresh" content="0; url=/account" />';
        die();
    }



    if(isset($_POST['langselect']))
    {
        $_SESSION['language'] = $_POST['langselect'];
    }

    DataBaseBackup();

    $revision=2323;

    echo '
        <!DOCTYPE html>
        <html>
            <head>
                <link rel="stylesheet" type="text/css" href="css/style.css?'.$revision.'">
                <link rel="stylesheet" type="text/css" href="css/designer.css?'.$revision.'">
                <link rel="stylesheet" type="text/css" href="css/flags.css?'.$revision.'">
                <link href="files/content/favicon.ico" rel="icon" type="image/x-icon" />
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
                <script src="/data/source.js?'.$revision.'"></script>

                <script>
                    // Dropdown Sidemenu
                    window.onload = function() {
                        InitialiseState(1,3);
                        //InitialiseState(2,2);
                        InitialiseState(3,4);
                        InitialiseState(4,5);
                        InitialiseState(5,7);
                        InitialiseState(6,3);

                        var selectedItem = sessionStorage.getItem("selectedIndex");
                        var selectedItemLast = sessionStorage.getItem("selectedIndexLast");
                        var styleClass;
                        if(selectedItem == selectedItemLast) styleClass="opened_page";
                        else styleClass="opened_page_fresh";
                        document.getElementById("mListItem" + selectedItem).className = styleClass;
                        sessionStorage.setItem("selectedIndexLast",selectedItem);

                        document.title = "'.GetProperty("company_name").' - " + sessionStorage.getItem("selectedIndexName");

                        document.getElementById("codereader").value="";
                    };

                    function InitialiseState(seltab,tabcount)
                    {
                        if(sessionStorage.getItem("mcatstat" + seltab) == 1)
                        {
                            document.getElementById("mcat" + seltab).style.transform = "rotate(90deg)";
                            document.getElementById("mcatds" + seltab).style.height = (tabcount*45) + "px";
                        }
                        else
                        {
                            document.getElementById("mcat" + seltab).style.transform = "rotate(-90deg)";
                            document.getElementById("mcatds" + seltab).style.height = 0 + "px";
                        }
                    }

                    function ChangeState(seltab,tabcount)
                    {
                        if(document.getElementById("mcat" + seltab).style.transform == "rotate(90deg)")
                        {
                            sessionStorage.setItem(("mcatstat" + seltab),0);
                            document.getElementById("mcat" + seltab).style.transform = "rotate(-90deg)";
                            document.getElementById("mcatds" + seltab).style.height = 0 + "px";
                        }
                        else
                        {
                            sessionStorage.setItem(("mcatstat" + seltab),1);
                            document.getElementById("mcat" + seltab).style.transform = "rotate(90deg)";
                            document.getElementById("mcatds" + seltab).style.height = (tabcount*45) + "px";
                        }
                    }
                </script>

                <script>
                    // Delay for different loader messages in miliseconds
                    var longLoadWarning1 = 2000;
                    var longLoadWarning2 = 5000;
                    var timeoutLoopWarning = 20000;

                    setTimeout(function() { document.getElementById("preloader_text").value="Große Datenmengen werden verarbeitet, bitte warten... "; }, longLoadWarning1);
                    setTimeout(function() { document.getElementById("preloader_text").value="Große Datenmengen werden verarbeitet, bitte warten... Dieser vorgang kann einige Sekunden dauern..."; }, longLoadWarning2);
                    setTimeout(function() { document.getElementById("preloader_text").value="Sollte dieser Fehler noch länger auftreten, kontaktieren Sie bitte den Systemadministrator!"; }, timeoutLoopWarning);
                    window.addEventListener("load", function(){ document.getElementById("loader").style.opacity=0; });
                </script>
                <script>

                </script>



            </head>
            <body>
                ';
                if(isset($_SESSION['notificationMessage']) AND $_SESSION['notificationMessage']!='')
                {
                    $notificationIcon = (isset($_SESSION['notificationIcon']) AND $_SESSION['notificationIcon']!='') ? '<img src="/files/content/'.$_SESSION['notificationIcon'].'.png" class="notification_icon_blur"><img src="/files/content/'.$_SESSION['notificationIcon'].'.png" class="notification_icon">' : '';

                    if($_SESSION['notificationIcon']=='info') $bgcolor = '#CEEBFD';
                    elseif($_SESSION['notificationIcon']=='check') $bgcolor = '#C9FDC9';
                    elseif($_SESSION['notificationIcon']=='cross') $bgcolor = '#FFD1D1';
                    else $bgcolor = '#CEEBFD';

                    echo '
                        <div id="notification_banner" class="notigication_banner" style="background:'.$bgcolor.'">
                            '.$notificationIcon.' '.$_SESSION['notificationMessage'].'
                        </div>
                    ';
                    $_SESSION['notificationMessage'] = '';
                    $_SESSION['notificationIcon'] = '';
                }

                if(basename($_SERVER["SCRIPT_FILENAME"], '.php')!='account')
                {
                    echo SearchBar();
                }

                echo '

                <div id="loader" class="preloader_bottom">
                    <img src="/files/content/loader.gif" class="loader"/> <output id="preloader_text">Inhalte werden geladen...</output>
                </div>
    ';

    /*
    
    if(isset($_SESSION['user_id']))
    {
        echo '<div class="timestamp_info"> ';
        if(fetch("users","stamped","id",$_SESSION['user_id'])==0)
        {
            echo '<b>'.langlib("Nicht angestempelt").'</b><br>'.langlib("Arbeitszeit wird nicht angerechnet");
        }
        if(fetch("users","stamped","id",$_SESSION['user_id'])==1)
        {
            echo '<b>'.langlib("Angestempelt seit").':</b><br>';
            echo date(" H:i:s \a\m d.m.Y",last_come($_SESSION['user_id'])).'<br>';
            echo '('.convert_s_to_date(get_current_timestamp($_SESSION['user_id'])).')';
        }
        echo '</div>';

        echo '
            <div class="timestamp_info_s">
                <img src="files/content/clock.png" alt="" class="clock_img_s"/><br>
                ';
                    if(fetch("users","stamped","id",$_SESSION['user_id'])==0)
                    {
                        echo '
                            <b>'.langlib("Nicht angestempelt").'</b><br>
                            '.langlib("Arbeitszeit wird nicht angerechnet").'
                            <br><br>
                            <a href="/timestamp?stamp_on"><button type="button" class="button_m t_button_inv">'.langlib("Hier Klicken um Anzustempeln").'</button></a>
                        ';
                    }
                    if(fetch("users","stamped","id",$_SESSION['user_id'])==1)
                    {
                        echo '
                            <b>'.langlib("Angestempelt seit").':</b><br>
                            '.date(" H:i:s \a\m d.m.Y",last_come($_SESSION['user_id'])).'<br>
                            ('.convert_s_to_date(get_current_timestamp($_SESSION['user_id'])).')
                            <br><br>
                            <a href="/timestamp?stamp_off"><button type="button" class="button_m t_button_inv">'.langlib("Hier Klicken um Abzustempeln").'</button></a>
                        ';
                    }
                echo '
            </div>
        ';
    }

    */

    echo '
        <div class="side_menu" id="scrollbar_sidemenu">
            <center><img src="/files/content/'.GetProperty("company_logo_menu").'" alt="" style="width:210px; margin-bottom:30px; margin-top:30px;"/></center>
    ';

    $disabled_de = ($_SESSION['language']=='DE') ? 'disabled' : '';
    $disabled_en = ($_SESSION['language']=='EN') ? 'disabled' : '';

    echo '
    <div class="language_container">
        <form action="'.$actual_link.'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
            <button class="language_button_l t_language_button" name="langselect" value="DE" type="submit" '.$disabled_de.'>DE</button>
            <button class="language_button_r t_language_button" name="langselect" value="EN" type="submit" '.$disabled_en.'>EN</button>
        </form>
    </div>
    ';

    $ctrListItems=0;

    if(isset($_SESSION['user_id']) AND $_SESSION['last_name']=="Hattinger") echo '
        <div class="side_menu_title"><strong>'.langlib("Admin").'</strong><div class="dropdown_arrow" id="mcat1" onclick="ChangeState(1,3);" style="transform: rotate(90deg);">&#x2BC8;</div></div>
            <div class="drop_section" id="mcatds1">
            '.ListItem($ctrListItems++,"user_register",langlib("Nutzer Registrieren")).'
            '.ListItem($ctrListItems++,"mail_recievers",langlib("Email Empfänger")).'
            '.ListItem($ctrListItems++,"department_assign",langlib("Abteilungszuweisung")).'
            </div>
        <br><br>';

    echo '
        <!--
        <div class="side_menu_title"><strong>'.langlib("SCRUM").'</strong><div class="dropdown_arrow" id="mcat2" onclick="ChangeState(2,2);" style="transform: rotate(90deg);">&#x2BC8;</div></div>
            <div class="drop_section" id="mcatds2">
            '.ListItem($ctrListItems++,"scrum_blackboard",langlib("Blackboard")).'
            '.ListItem($ctrListItems++,"scrum_setup",langlib("SCRUM-Settings")).'
            </div>
        <br><br>
        -->

        <div class="side_menu_title"><strong>'.langlib("Menü").'</strong><div class="dropdown_arrow" id="mcat3" onclick="ChangeState(3,4);" style="transform: rotate(90deg);">&#x2BC8;</div></div>
            <div class="drop_section" id="mcatds3">
            '.ListItem($ctrListItems++,"overview?personalDepartments",langlib("Übersicht")).'
            '.ListItem($ctrListItems++,"timestamp?personal_timestamps",langlib("Zeiterfassung")).'
            '.ListItem($ctrListItems++,"products?products&show",langlib("Produkte")).'
            '.ListItem($ctrListItems++,"contacts?employees",langlib("Kontakte")).'
            </div>
        <br><br>

        <div class="side_menu_title"><strong>'.langlib("Bestellungen").'</strong><div class="dropdown_arrow" id="mcat4" onclick="ChangeState(4,5);" style="transform: rotate(90deg);">&#x2BC8;</div></div>
            <div class="drop_section" id="mcatds4">
            <b>'.ListItem($ctrListItems++,"orders?add",langlib("Bestellung Eintragen")).'</b>
            '.ListItem($ctrListItems++,"orders?new&all",langlib("Neue Bestellungen")).'
            '.ListItem($ctrListItems++,"orders?production&all",langlib("In Produktion")).'
            '.ListItem($ctrListItems++,"orders?finished&all",langlib("Verkaufsbereit")).'
            '.ListItem($ctrListItems++,"orders?sold&all",langlib("Verkauft")).'
            </div>
        <br><br>

        <div class="side_menu_title"><strong>'.langlib("Abteilungen").'</strong><div class="dropdown_arrow" id="mcat5" onclick="ChangeState(5,7);" style="transform: rotate(90deg);">&#x2BC8;</div></div>
            <div class="drop_section" id="mcatds5">
            '.ListItem($ctrListItems++,"department?purchase&overview",langlib("Einkauf")).'
            '.ListItem($ctrListItems++,"department?construction&overview",langlib("Konstruktion")).'
            '.ListItem($ctrListItems++,"department?production&overview",langlib("Produktion")).'
            '.ListItem($ctrListItems++,"department?marketing&overview",langlib("Marketing")).'
            '.ListItem($ctrListItems++,"department?administration&overview",langlib("Administration")).'
            '.ListItem($ctrListItems++,"department?accounting&overview",langlib("Buchhaltung")).'
            '.ListItem($ctrListItems++,"department?sale&overview",langlib("Verkauf")).'
            </div>
        <br><br>

        <div class="side_menu_title"><strong>'.langlib("Mehr").'</strong><div class="dropdown_arrow" id="mcat6" onclick="ChangeState(6,3);" style="transform: rotate(90deg);">&#x2BC8;</div></div>
            <div class="drop_section" id="mcatds6">
            '.ListItem($ctrListItems++,"protocol",langlib("Protokoll")).'
            '.ListItem($ctrListItems++,"account",langlib("Account")).'
            '.ListItem($ctrListItems++,"settings",langlib("Einstellungen")).'
            </div>
        <br><br><br>
        </div>


        <!--

        <div class="chat_wrapper">
            <div class="chat_container">
                <div class="chat_bubble">
                    <div class="chat_image_container"><img src="/files/content/user.png" alt="" class="chat_user_img"/></div>
                    <div class="chat_name_container">1<br>Hattinger</div>
                </div>
            </div>
            <div class="chat_container">
                <div class="chat_bubble">
                    <div class="chat_image_container"><img src="/files/content/user.png" alt="" class="chat_user_img"/></div>
                    <div class="chat_name_container">2<br>Hattinger</div>
                </div>
            </div>
            <div class="chat_container">
                <div class="chat_bubble">
                    <div class="chat_image_container"><img src="/files/content/user.png" alt="" class="chat_user_img"/></div>
                    <div class="chat_name_container">3<br>Hattinger</div>
                </div>
            </div>
            <div class="chat_container">
                <div class="chat_bubble">
                    <div class="chat_image_container"><img src="/files/content/user.png" alt="" class="chat_user_img"/></div>
                    <div class="chat_name_container">4<br>Hattinger</div>
                </div>
            </div>
        </div>

        -->

        <div class="chat_bubble_container">
            <a href="#chatID1">
                <div class="chat_bubble">
                    <div class="chat_notification_bubble">5</div>
                    <div class="chat_image_container"><img src="/files/content/user.png" alt="" class="chat_user_img"/></div>
                    <div class="chat_name_container">Tobias<br>Hattinger</div>
                </div>
            </a>

            <a href="#chatID2">
                <div class="chat_bubble">
                    <div class="chat_notification_bubble">1</div>
                    <div class="chat_image_container"><img src="/files/content/user.png" alt="" class="chat_user_img"/></div>
                    <div class="chat_name_container">Peter<br>Hattinger</div>
                </div>
            </a>

            <a href="#chatID3">
                <div class="chat_bubble">
                    <div class="chat_notification_bubble">3</div>
                    <div class="chat_image_container"><img src="/files/content/user.png" alt="" class="chat_user_img"/></div>
                    <div class="chat_name_container">Sabrina<br>Hattinger</div>
                </div>
            </a>

            <a href="#chatID4">
                <div class="chat_bubble">
                    <div class="chat_notification_bubble">8</div>
                    <div class="chat_image_container"><img src="/files/content/user.png" alt="" class="chat_user_img"/></div>
                    <div class="chat_name_container">Petra<br>Hattinger</div>
                </div>
            </a>
        </div>

        <div class="chat_window_container">
            <div class="chat_container" id="chatID1">
                <div class="chat_window_header">
                    <span style="color: #32CD32">&bull;</span> Tobias Hattinger
                    <div class="chat_window_buttons">
                        <a href="#close"><div class="chat_window_button">_</div></a>
                    </div>
                </div>
                <div class="chat_window_messages">
                    Hallo und so<br><br><br><br><br>asdasd<br><br><br><br><br><br><br><br>Asdadsadad<br><br><br>asdadsa
                    <br><br><br><br><br><br><br><br><br>Asdad
                </div>
                <div class="chat_window_inputfield">
                    <textarea class="chat_window_textarea">Halloooo</textarea>
                </div>
            </div>


            <div class="chat_container" id="chatID2">
                <div class="chat_window_header">
                    <span style="color: #32CD32">&bull;</span> Peter Hattinger
                    <div class="chat_window_buttons">
                        <a href="#close"><div class="chat_window_button">_</div></a>
                    </div>
                </div>
                <div class="chat_window_messages">
                    Hallo und so<br><br><br><br><br>asdasd<br><br><br><br><br><br><br><br>Asdadsadad<br><br><br>asdadsa
                    <br><br><br><br><br><br><br><br><br>Asdad
                </div>
                <div class="chat_window_inputfield">
                    <textarea class="chat_window_textarea">Halloooo</textarea>
                </div>
            </div>



            <div class="chat_container" id="chatID3">
                <div class="chat_window_header">
                    <span style="color: #32CD32">&bull;</span> Sabrina Hattinger
                    <div class="chat_window_buttons">
                        <a href="#close"><div class="chat_window_button">_</div></a>
                    </div>
                </div>
                <div class="chat_window_messages">
                    Hallo und so<br><br><br><br><br>asdasd<br><br><br><br><br><br><br><br>Asdadsadad<br><br><br>asdadsa
                    <br><br><br><br><br><br><br><br><br>Asdad
                </div>
                <div class="chat_window_inputfield">
                    <textarea class="chat_window_textarea">Halloooo</textarea>
                </div>
            </div>



            <div class="chat_container" id="chatID4">
                <div class="chat_window_header">
                    <span style="color: #32CD32">&bull;</span> Petra Hattinger
                    <div class="chat_window_buttons">
                        <a href="#close"><div class="chat_window_button">_</div></a>
                    </div>
                </div>
                <div class="chat_window_messages">
                    Hallo und so<br><br><br><br><br>asdasd<br><br><br><br><br><br><br><br>Asdadsadad<br><br><br>asdadsa
                    <br><br><br><br><br><br><br><br><br>Asdad
                </div>
                <div class="chat_window_inputfield">
                    <textarea class="chat_window_textarea">Halloooo</textarea>
                </div>
            </div>


        </div>





    ';



    /*
    if(isset($_SESSION['user_id']) AND $_SESSION['last_name']=="Hattinger") echo '
        <div class="side_menu_title"><strong>'.langlib("Admin").'</strong><div class="dropdown_arrow" id="mcat1" onclick="ChangeState(1,3);" style="transform: rotate(90deg);">&#x2BC8;</div></div>
            <div class="drop_section" id="mcatds1">
            '.ListItem(1,"user_register",langlib("Nutzer Registrieren")).'
            '.ListItem(2,"mail_recievers",langlib("Email Empfänger")).'
            '.ListItem(3,"department_assign",langlib("Abteilungszuweisung")).'
            </div>
        <br><br>';

    echo '
        <!--
        <div class="side_menu_title"><strong>'.langlib("SCRUM").'</strong><div class="dropdown_arrow" id="mcat2" onclick="ChangeState(2,2);" style="transform: rotate(90deg);">&#x2BC8;</div></div>
            <div class="drop_section" id="mcatds2">
            '.ListItem(4,"scrum_blackboard",langlib("Blackboard")).'
            '.ListItem(5,"scrum_setup",langlib("SCRUM-Settings")).'
            </div>
        <br><br>
        -->

        <div class="side_menu_title"><strong>'.langlib("Menü").'</strong><div class="dropdown_arrow" id="mcat3" onclick="ChangeState(3,3);" style="transform: rotate(90deg);">&#x2BC8;</div></div>
            <div class="drop_section" id="mcatds3">
            '.ListItem(6,"overview",langlib("Übersicht"),"personal_departments","none",1).'
            '.ListItem(7,"timestamp",langlib("Zeiterfassung"),"personal_timestamps","none",1).'
            '.ListItem(8,"products",langlib("Produkte"),"products","show",1).'
            </div>
        <br><br>

        <div class="side_menu_title"><strong>'.langlib("Bestellungen").'</strong><div class="dropdown_arrow" id="mcat4" onclick="ChangeState(4,5);" style="transform: rotate(90deg);">&#x2BC8;</div></div>
            <div class="drop_section" id="mcatds4">
            <b>'.ListItem(9,"orders",langlib("Bestellung Eintragen"),"add").'</b>
            '.ListItem(10,"orders",langlib("Neue Bestellungen"),"new","all").'
            '.ListItem(11,"orders",langlib("In Produktion"),"production","all").'
            '.ListItem(12,"orders",langlib("Verkaufsbereit"),"finished","all").'
            '.ListItem(13,"orders",langlib("Verkauft"),"sold","all").'
            </div>
        <br><br>

        <div class="side_menu_title"><strong>'.langlib("Abteilungen").'</strong><div class="dropdown_arrow" id="mcat5" onclick="ChangeState(5,7);" style="transform: rotate(90deg);">&#x2BC8;</div></div>
            <div class="drop_section" id="mcatds5">
            '.ListItem(14,"department",langlib("Einkauf"),"purchase","overview").'
            '.ListItem(15,"department",langlib("Konstruktion"),"construction","overview").'
            '.ListItem(16,"department",langlib("Produktion"),"production","overview").'
            '.ListItem(17,"department",langlib("Marketing"),"marketing","overview").'
            '.ListItem(18,"department",langlib("Administration"),"administration","overview").'
            '.ListItem(19,"department",langlib("Buchhaltung"),"accounting","overview").'
            '.ListItem(20,"department",langlib("Verkauf"),"sale","overview").'
            </div>
        <br><br>

        <div class="side_menu_title"><strong>'.langlib("Mehr").'</strong><div class="dropdown_arrow" id="mcat6" onclick="ChangeState(6,3);" style="transform: rotate(90deg);">&#x2BC8;</div></div>
            <div class="drop_section" id="mcatds6">
            '.ListItem(21,"protocol",langlib("Protokoll")).'
            '.ListItem(22,"account",langlib("Account")).'
            '.ListItem(23,"settings",langlib("Einstellungen")).'
            </div>
        <br><br><br>
        </div>
    ';
    */
?>
