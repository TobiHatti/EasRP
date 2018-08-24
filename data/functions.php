<?php
    function DataBaseBackup()
    {
        include("mysql_connect.php");
        if(!file_exists('backup/dbbu_'.$database.'_'.date("Y-m-d").'.sql'))
        {
            $dbhost     = $servername;
            $dbuser     = $username;
            $dbpwd      = $password;
            $dbname     = $database;
            $dbbackup   = 'backup/dbbu_'.$dbname.'_'.date("Y-m-d").'.sql';

            error_reporting(0);
            set_time_limit(0);

            // ab hier nichts mehr ändern
            $conn = mysql_connect($dbhost, $dbuser, $dbpwd) or die(mysql_error());
            mysql_select_db($dbname);
            $f = fopen($dbbackup, "w");

            $tables = mysql_list_tables($dbname);
            while ($cells = mysql_fetch_array($tables))
            {
                $table = $cells[0];
                $res = mysql_query("SHOW CREATE TABLE `".$table."`");
                if ($res)
                {
                    $create = mysql_fetch_array($res);
                    $create[1] .= ";";
                    $line = str_replace("\n", "", $create[1]);
                    fwrite($f, $line."\n");
                    $data = mysql_query("SELECT * FROM `".$table."`");
                    $num = mysql_num_fields($data);
                    while ($row = mysql_fetch_array($data))
                    {
                        $line = "INSERT INTO `".$table."` VALUES(";
                        for ($i=1;$i<=$num;$i++)
                        {
                            $line .= "'".mysql_real_escape_string($row[$i-1])."', ";
                        }
                        $line = substr($line,0,-2);
                        fwrite($f, $line.");\n");
                    }
                }
            }
            fclose($f);

            DataBasePurge();
        }
    }

    function DataBasePurge()
    {
        //Purging Orders/OrderContains/CustomerOrders

        require("mysql_connect.php");
        $secureDate = date('Y-m-d H:i:s', strtotime("-2 days"));
        $now = Now(1);

        $strSQL = "SELECT * FROM orders WHERE confirmed = '0' AND order_date < '$secureDate'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            // If entry is Un-Confirmed for at least 2 days it gets deleted
            $selectedOrder = $row['order_number'];
            MySQLNonQuery("DELETE FROM order_contains WHERE order_number = '$selectedOrder'");
            MySQLNonQuery("DELETE FROM customer_order WHERE order_number = '$selectedOrder'");
            MySQLNonQuery("DELETE FROM orders WHERE order_number = '$selectedOrder'");
        }

        $strSQL = "SELECT * FROM orders WHERE hidden = '1' AND delete_date < '$now'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            $selectedOrder = $row['order_number'];
            MySQLNonQuery("DELETE FROM orders WHERE order_number = '$selectedOrder'");
            MySQLNonQuery("DELETE FROM order_contains WHERE order_number = '$selectedOrder'");
            MySQLNonQuery("DELETE FROM customer_order WHERE order_number = '$selectedOrder'");
        }
    }

    function Now($dateTime = '0')
    {
        return ($dateTime=='0') ? date("Y-m-d") : date("Y-m-d H:i:s");
    }

    function seek($seeker,$search_val,$opt1 = '')
    {
        include("mysql_connect.php");

        if($seeker == 'mail')
        {
            $strSQL = "SELECT * FROM users WHERE id LIKE '$search_val'";
            $rs=mysqli_query($link,$strSQL);
            while($row=mysqli_fetch_assoc($rs)) return $row['email'];
        }
        if($seeker == 'mailres')
        {
            $strSQL = "SELECT * FROM emails WHERE id LIKE '$search_val'";
            $rs=mysqli_query($link,$strSQL);
            while($row=mysqli_fetch_assoc($rs)) return $row['email'];
        }

    }


    function fetch($db,$get,$col,$like)
    {
        include("mysql_connect.php");

        $strSQL = "SELECT * FROM $db WHERE $col LIKE '$like'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs)) return $row[$get];

        return '';
    }

    function fetch_count($db,$col,$like)
    {
        include("mysql_connect.php");

        $strSQL = "SELECT * FROM $db WHERE $col LIKE '$like'";
        $rs=mysqli_query($link,$strSQL);
        return mysqli_num_rows($rs);
    }


    function get_last_timestamp($user_id)
    {
        include("mysql_connect.php");
        $start_time = '';
        $leave_time = '';
        $strSQL = "SELECT * FROM timestamp WHERE user_id LIKE '$user_id' AND go NOT LIKE '0000-00-00 00:00:00' ORDER BY come ASC";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            $start_time = strtotime($row['come']);
            $leave_time = strtotime($row['go']);
        }
        return $leave_time - $start_time;
    }

    function get_current_timestamp($user_id)
    {
        include("mysql_connect.php");

        $strSQL = "SELECT * FROM timestamp WHERE user_id LIKE '$user_id' ORDER BY come ASC";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            $start_time = strtotime($row['come']);
            $leave_time = strtotime(date("Y-m-d H:i:s"));
        }
        return $leave_time - $start_time;
    }

    function last_left($user_id)
    {
        include("mysql_connect.php");

        $strSQL = "SELECT * FROM timestamp WHERE user_id LIKE '$user_id' AND go NOT LIKE '0000-00-00 00:00:00' ORDER BY come DESC";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            return strtotime($row['go']);
        }
    }

    function last_come($user_id)
    {
        include("mysql_connect.php");

        $strSQL = "SELECT * FROM timestamp WHERE user_id LIKE '$user_id' ORDER BY come DESC";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            return strtotime($row['come']);
        }
    }

    function convert_s_to_date($seconds)
    {
        $s = $seconds%60;
        $m = floor(($seconds%3600)/60);
        $h = floor(($seconds%86400)/3600);
        $d = floor(($seconds%2592000)/86400);
        $M = floor($seconds/2592000);

        // Ensure all values are 2 digits, prepending zero if necessary.
        $s = $s < 10 ? '0' . $s : $s;
        $m = $m < 10 ? '0' . $m : $m;
        $h = $h < 10 ? '0' . $h : $h;
        $d = $d < 10 ? '0' . $d : $d;
        $M = $M < 10 ? '0' . $M : $M;

        return $h.'h '.$m.'min '.$s.'s - '.$d.' Tage';
    }

    function IsDepartment($user_id,$department)
    {
        include("mysql_connect.php");
        $dep_id = fetch("departments","id","department",$department);
        $strSQL = "SELECT * FROM department_assigns WHERE department_id LIKE '$dep_id' AND user_id LIKE '$user_id'";
        $rs=mysqli_query($link,$strSQL);
        if(mysqli_num_rows($rs)!=0) return 1;
        else return 0;
    }

    function LetterCorrection($input_string)
    {
        $input_string = str_replace("Ã¤","ä",$input_string);
        $input_string = str_replace("Ã„","Ä",$input_string);
        $input_string = str_replace("Ã¶","ö",$input_string);
        $input_string = str_replace("Ã–","Ö",$input_string);
        $input_string = str_replace("Ã¼","ü",$input_string);
        $input_string = str_replace("Ãœ","Ü",$input_string);
        $input_string = str_replace("ÃŸ","ß",$input_string);

        return $input_string;
    }

    function ProtocolEntry($string)
    {
        include("mysql_connect.php");

        $id=uniqid();
        $user = $_SESSION['user_id'];
        $date = date("D M j Y G:i:s ");

        $strSQL = "INSERT INTO protocol (id,user,date,description) VALUES ('$id','$user','$date','$string')";
        $rs=mysqli_query($link,$strSQL);
    }

    function file_upload($use,$opt1=0)
    {
        include("mysql_connect.php");
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
        // Check if image file is a actual image or fake image
        if(isset($_POST["submit"]) OR isset($_POST['update_marketing_element_img']))
        {
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if($check !== false)
            {
                $uploadOk = 1;
            }
            else
            {
                echo "<br>Der Ausgew&auml;hlte Dateityp wird nicht unterst&uuml;tzt!.";
                $uploadOk = 0;
            }
        }
        // Check if file already exists
        if (file_exists($target_file))
        {
            echo "<br>Fehler - Die Datei besteht bereits [Drastic File Overflow - Please contact an Administrator].";
            $uploadOk = 0;
        }
        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 10485760) // 5242880 = 5MB  10485760 = 10MB
        {
            echo "<br>Ihr Bild ist leider zu gro&szlig.<br>Bitte Skalieren Sie es ungef&auml;hr auf die gr&ouml;&szlige 200x200px";
            $uploadOk = 0;
        }
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "JPG" && $imageFileType != "PNG" && $imageFileType != "JPEG")
        {
            echo "<br>Es sind nur die Dateitypen JPG, JPEG, PNG & GIF erlaubt.";
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0)
        {
            echo "<br>Ein Fehler ist aufgetreten, bitte versuchen Sie es sp&auml;ter noch einmal (1)";
        // if everything is ok, try to upload file
        }
        else
        {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) echo 'Die Datei "'. basename( $_FILES["fileToUpload"]["name"]). '" wurde erfolgreich hochgeladen!';
            else echo "<br>Ein Fehler ist aufgetreten, bitte versuchen Sie es sp&auml;ter noch einmal (2)";
        }
        if($uploadOk==1)
        {
            $uid = uniqid();
            if($use == "marketing")
            {
                rename('uploads/'.basename($_FILES["fileToUpload"]["name"]), 'files/content/marketing/'.$uid.'.'.$imageFileType);

                $filename_end = '/files/content/marketing/'.$uid.'.'.$imageFileType;
                $element_id = $opt1;

                $strSQL = "UPDATE marketing_elements SET image = '$filename_end' WHERE id LIKE '$element_id'";
                $rs=mysqli_query($link,$strSQL);
            }



            // Make image Grayscaled
            /*
            $im = imagecreatefrompng($_SESSION['uploaded_image']);
            if($im && imagefilter($im, IMG_FILTER_GRAYSCALE))
            {
                echo 'Image converted to grayscale.';
                imagepng($im, $_SESSION['uploaded_image']);
            }
            else echo 'Conversion to grayscale failed.';

            imagedestroy($im);
            */
        }
    }

    function langlib($keyword)
    {
        include("mysql_connect.php");
        $retval=fetch("language_lib",$_SESSION['language'],"keyword",$keyword);
        if($retval!='') return $retval;
        else
        {
            if(fetch_count("language_lib","keyword",$keyword)==0)
            {
                $strSQL = "INSERT INTO language_lib (keyword,DE,EN) VALUES ('$keyword','$keyword','')";
                $rs=mysqli_query($link,$strSQL);
            }
            return $keyword;
        }
    }

    function RegisterCards($parent)
    {
        /*
        USAGE:
        Unlimited parameters, every parameter is a register card.
        SYNTAX:
        ("Show All Products||all",...)
        - Show All Products ... The text displayed at the register card
        - ||                ... delimiter: splizes up display-part from code-part
        - all               ... $_GET-redirection. for multiple get values use "...||all&new"
        DELIMITERS:
        - ||    ... Default
        - |!|   ... Standart selected
        - |||   ... Placeholder (no args left or right)
        */

        // Get Card Arguments
        $card_count = func_num_args();
        $card_names = func_get_args();

        // Get Current URL and Test for Subpage
        $full_page = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $filename = basename($_SERVER["SCRIPT_FILENAME"], '.php');
        $filenameplus = basename($_SERVER["REQUEST_URI"], '.php');

        if($parent=="") $cardSelected = ($filename == $filenameplus) ? 0 : 1;
        else $cardSelected = ($filename.'?'.$parent == $filenameplus OR $filename == $filenameplus) ? 0 : 1;


        $retval = '';

        for($i=1;$i<$card_count;$i++)
        {
            // Test for Special Cases
            $isDefault = (str_replace('|!|','',$card_names[$i])==$card_names[$i]) ? 0 : 1;
            $isBSpace = (str_replace('|||','',$card_names[$i])==$card_names[$i]) ? 0 : 1;

            if(!$isBSpace)
            {
                $card_combo = explode('||',str_replace('|!|','||',$card_names[$i]));
                $card_display = $card_combo[0];
                $card_value = $card_combo[1];

                if($isDefault AND !$cardSelected)
                {
                    if($parent == "") Redirect($filename.'?'.$card_value);
                    else Redirect($filename.'?'.$parent.'&'.$card_value);
                }

                if($parent=="")
                {
                    $isActive = (SubStringFind($full_page,'?'.$card_value)==1 OR SubStringFind($full_page,'?'.$card_value.'&')==1) ? 'id="selected_register"' : '';
                    $retval.= '<a href="'.$filename.'?'.$card_value.'"><button type="button" class="button_m t_button" '.$isActive.' style="margin-bottom:0px; border-bottom:none; vertical-align:bottom">'.langlib($card_display).'</button></a>';
                }
                else
                {
                    $isActive = (SubStringFind($full_page,'&'.$card_value)==1 OR SubStringFind($full_page,'&'.$card_value.'&')==1) ? 'id="selected_register"' : '';
                    $retval.= '<a href="'.$filename.'?'.$parent.'&'.$card_value.'"><button type="button" class="button_m t_button" '.$isActive.' style="margin-bottom:0px; border-bottom:none; vertical-align:bottom">'.langlib($card_display).'</button></a>';
                }
            }
            else $retval .= '<div style="display:inline">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>';
        }
        $retval.='<hr style="margin-top:0px;"><hr>';
        return $retval;
    }

    function MultiFileUpload($path)
    {
        // File Upload
        //$valid_formats = array("jpg", "png", "gif", "zip", "bmp","JPG");
        $max_file_size = 10485760; //10 MB
        $count = 0;
        if(!is_dir($path)) mkdir($path, 0750);

        if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST")
        {
            // Loop $_FILES to exeicute all files
            foreach ($_FILES['files']['name'] as $f => $name)
            {
                if ($_FILES['files']['error'][$f] == 4)
                {
                    continue; // Skip file if any error found
                }
                if ($_FILES['files']['error'][$f] == 0)
                {
                    if ($_FILES['files']['size'][$f] > $max_file_size)
                    {
                        $message[] = "$name is too large!.";
                        continue; // Skip large files
                    }
                    /*
                    else if(!in_array(pathinfo($name, PATHINFO_EXTENSION), $valid_formats))
                    {
                        $message[] = "$name is not a valid format";
                        continue; // Skip invalid file formats
                    }
                    */
                    else
                    {
                        // No error found! Move uploaded files
                        if(move_uploaded_file($_FILES["files"]["tmp_name"][$f], $path.$name))
                        $count++; // Number of successfully uploaded file
                    }
                }
            }
        }
    }

    function ThumbnailUpload($path,$pnumber)
    {
        // File Upload
        $valid_formats = array("jpg","JPG","png","PNG","gif","GIF","bmp","BMP","jpeg","JPEG");
        $max_file_size = 10485760; //10 MB
        $count = 0;
        if(!is_dir($path)) mkdir($path, 0750);

        if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST")
        {
            // Loop $_FILES to exeicute all files
            foreach ($_FILES['filesthumb']['name'] as $f => $name)
            {
                if ($_FILES['filesthumb']['error'][$f] == 4)
                {
                    continue; // Skip file if any error found
                }
                if ($_FILES['filesthumb']['error'][$f] == 0)
                {
                    if ($_FILES['filesthumb']['size'][$f] > $max_file_size)
                    {
                        $message[] = "$name is too large!.";
                        continue; // Skip large files
                    }
                    else if(!in_array(pathinfo($name, PATHINFO_EXTENSION), $valid_formats))
                    {
                        $message[] = "$name is not a valid format";
                        continue; // Skip invalid file formats
                    }
                    else
                    {
                        // No error found! Move uploaded files
                        if(move_uploaded_file($_FILES["filesthumb"]["tmp_name"][$f], $path.$name))
                        MySQLNonQuery("UPDATE products SET product_image = '$name' WHERE number = '$pnumber'");
                        $count++; // Number of successfully uploaded file
                    }
                }
            }
        }
    }

    function Redirect($path,$delay=0)
    {
        echo '<meta http-equiv="refresh" content="'.$delay.'; url=/'.$path.'" />';
    }

    function GetProperty($key)
    {
        return MySQLSkalar("SELECT value AS x FROM settings WHERE setting = '$key'");
    }

    function SetProperty($key,$value)
    {
        if(fetch_count("settings","setting",$key) != 0) MySQLNonQuery("UPDATE settings SET value = '$value' WHERE setting = '$key'");
        else MySQLNonQuery("INSERT INTO settings (setting,value) VALUES ('$key','$value')");
    }

    function InkrementProperty($key,$resetLimit = "none")
    {
        if($resetLimit != "none" AND GetProperty($key)>=$resetLimit) SetProperty($key,0);
        SetProperty($key,GetProperty($key) + 1);
        return GetProperty($key);
    }

    function DekrementProperty($key)
    {
        SetProperty($key,GetProperty($key) - 1);
        return GetProperty($key);
    }

    function MySQLNonQuery($strSQL)
    {
        require("mysql_connect.php");
        $rs=mysqli_query($link,$strSQL);
        return $rs;
    }

    function MySQLQuery($strSQL)
    {
        require("mysql_connect.php");
        return mysqli_fetch_assoc(mysqli_query($link,$strSQL));
    }

    function MySQLResultExists($strSQL)
    {
        require("mysql_connect.php");
        $rs=mysqli_query($link,$strSQL);
        return (mysqli_num_rows($rs)!=0) ? 1 : 0 ;
    }

    function MySQLResultCount($strSQL)
    {
        require("mysql_connect.php");
        $rs=mysqli_query($link,$strSQL);
        return mysqli_num_rows($rs);
    }

    function MySQLSkalar($strSQL)
    {
        require("mysql_connect.php");
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs)) return $row['x'];
    }

    function NumberFormatEx($number, $fixedLenght)
    {
        $add0s = $fixedLenght - strlen($number);
        for($i=0; $i<$add0s;$i++)
        {
            $number = '0'.$number;
        }
        return $number;

    }

    function SubStringFind($string,$search)
    {
        if(str_replace($search,'',$string)==$string) return false;
        else return true;
    }

    function SubStringMultiFind()
    {
        $amt = func_num_args();
        $search = func_get_args();
        $string = strtolower($search[0]);
        $retval=false;

        for($i=1;$i<$amt;$i++) if(str_replace(strtolower($search[$i]),'',$string)!=$string) $retval = true;
        return $retval;
    }

    function DirectoryListing($path)
    {
        $retval='';
        foreach (glob($path.'*.*') as $filename)
        {

            $file_short = str_replace($path,'',$filename);

            if(SubStringMultiFind($file_short,'.rar','.zip','.gz','.7z')) $retval.= '<i class="fa fa-file-archive-o"></i>';
            else if(SubStringMultiFind($file_short,'.wav','.mp3','.aif','.m4r','.m4a','.mid')) $retval.= '<i class="fa fa-file-audio-o"></i>';
            else if(SubStringMultiFind($file_short,'.c','.cpp','.cs','.js','.php','.html','.css','.htm','.exe','.app','.bat','.cmd','.jar','.asp','.accdb','.db','.dbf','.mdb','.pdb','.sql','.apk','.cgi','.com','.gadget','.wsf','.aspx','.cer','.cfm','.csr','.jsp','.rss','.xhtml'.'.crx'.'.plugin')) echo '<i class="fa fa-file-code-o"></i>';
            else if(SubStringMultiFind($file_short,'.xls','.xlsx','.xlsm','.xlr')) $retval.= '<i class="fa fa-file-excel-o"></i>';
            else if(SubStringMultiFind($file_short,'.png','.jpg','.jpeg','.gif','.tiff','.bmp','.svg','.tif','.ico')) $retval.= '<i class="fa fa-file-image-o"></i>';
            else if(SubStringMultiFind($file_short,'.mpg','.mp4','.mov','.wmv','.avi','.rm','.3gp','.aaf')) $retval.= '<i class="fa fa-file-movie-o"></i>';
            else if(SubStringMultiFind($file_short,'.pdf','.pct','.indd')) $retval.= '<i class="fa fa-file-pdf-o"></i>';
            else if(SubStringMultiFind($file_short,'.ppt','.pptx','.pptm')) $retval.= '<i class="fa fa-file-powerpoint-o"></i>';
            else if(SubStringMultiFind($file_short,'.txt','.rtf','.log')) $retval.= '<i class="fa fa-file-text-o"></i>';
            else if(SubStringMultiFind($file_short,'.doc','.docx','.docm')) $retval.= '<i class="fa fa-file-word-o"></i>';
            else $retval.= '<i class="fa fa-file-o"></i> ';

            $retval.= ' <a href="/'.$filename.'" download>'.$file_short.'</a><br>';
        }

        return $retval;
    }

    function ProductContains($number,$specific='0')
    {
        require("mysql_connect.php");
        $retval='';

        if($specific=='0')
        {
            $strSQL = "SELECT * FROM product_contains WHERE parent = '$number'";
            $rs=mysqli_query($link,$strSQL);
            while($row=mysqli_fetch_assoc($rs))
            {
                if(SubStringFind($row['child'],GetProperty("prefix_support"))) $path="/products?support&show#".$row['child'];
                if(SubStringFind($row['child'],GetProperty("prefix_raw"))) $path="/products?raw&show#".$row['child'];
                if(SubStringFind($row['child'],GetProperty("prefix_semiproduct"))) $path="/products?semiproducts&show#".$row['child'];

                $retval.= $row['quantity'].'x <a href="'.$path.'"><u>'.$row['child'].'</u></a>: '.fetch("products","name","number",$row['child']).'<br>';
            }
        }
        else
        {
            $prefix = GetProperty('prefix_'.$specific);
            $strSQL = "SELECT * FROM product_contains WHERE parent = '$number' AND child LIKE '$prefix%'";

            $rs=mysqli_query($link,$strSQL);
            while($row=mysqli_fetch_assoc($rs))
            {
                if(SubStringFind($row['child'],GetProperty("prefix_support"))) $path="/products?support&show#".$row['child'];
                if(SubStringFind($row['child'],GetProperty("prefix_raw"))) $path="/products?raw&show#".$row['child'];
                if(SubStringFind($row['child'],GetProperty("prefix_semiproduct"))) $path="/products?semiproducts&show#".$row['child'];

                $retval.= $row['quantity'].'x <a href="'.$path.'"><u>'.$row['child'].'</u></a>: '.fetch("products","name","number",$row['child']).'<br>';
            }
        }

        return $retval;
    }

    function DeleteFolder($path)
    {
        $files = glob($path.'*');
        foreach($files as $file)
        {
            if(is_file($file)) unlink($file);
        }
        rmdir($path);
    }

    function ListAttributes($string)
    {
        $retval='';
        $attributes = explode(';',$string);
        foreach($attributes as $attribute) $retval .= ($attribute!=null) ? ('&bull; '.$attribute.'<br>') : '';
        return $retval;
    }

    function OrderAttributes($orderNumber,$productNumber)
    {
        require("mysql_connect.php");
        $strSQL = "SELECT * FROM order_contains WHERE order_number = '$orderNumber' AND product_number = '$productNumber'";
        $retval='';
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            if($row['attributes']!="")
            {
                $attributeCombos = explode(';',$row['attributes']);
                foreach($attributeCombos as $attrComb)
                {
                    $attrSingles = explode('##',$attrComb);
                    $retval .= '<b>'.$attrSingles[0].':</b> '.$attrSingles[1].'<br>';
                }
            }
            else $retval .= '<i>Keine Attribute verf&uuml;gbar</i>';
        }

        return $retval;
    }

    function ChildCount($parent)
    {
        require("mysql_connect.php");
        $childs = 0;

        $prefix_semiproduct =  GetProperty("prefix_semiproduct");
        $prefix_raw =  GetProperty("prefix_raw");
        $prefix_support =  GetProperty("prefix_support");

        $strSQL = "SELECT * FROM product_contains WHERE parent = '$parent' AND child LIKE '$prefix_support%'";
        $rs=mysqli_query($link,$strSQL);
        $childs += mysqli_num_rows($rs);

        $strSQL = "SELECT * FROM product_contains WHERE parent = '$parent' AND child LIKE '$prefix_raw%'";
        $rs=mysqli_query($link,$strSQL);
        $childs += mysqli_num_rows($rs);

        $strSQL = "SELECT * FROM product_contains WHERE parent = '$parent' AND child LIKE '$prefix_semiproduct%'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            $subparent = $row['child'];

            $strSQLc = "SELECT * FROM product_contains WHERE parent = '$subparent' AND child LIKE '$prefix_support%'" ;
            $rsc=mysqli_query($link,$strSQLc);
            $childs += mysqli_num_rows($rsc);

            $strSQLc = "SELECT * FROM product_contains WHERE parent = '$subparent' AND child LIKE '$prefix_raw%'" ;
            $rsc=mysqli_query($link,$strSQLc);
            $childs += mysqli_num_rows($rsc);
        }

        return $childs;
    }

    function ProductPrice($number)
    {
        require("mysql_connect.php");
        $price = 0;
        $prefix_semiproduct =  GetProperty("prefix_semiproduct");
        $strSQL = "SELECT * FROM product_contains WHERE parent = '$number' AND child LIKE '$prefix_semiproduct%'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            $price += SemiProductPrice($row['child']) * $row['quantity'];
        }

        $prefix_raw =  GetProperty("prefix_raw");
        $strSQL = "SELECT * FROM product_contains WHERE parent = '$number' AND child LIKE '$prefix_raw%'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            $quant = $row['quantity'];
            $child = $row['child'];
            $strSQLp = "SELECT * FROM products WHERE number = '$child'";
            $rsp=mysqli_query($link,$strSQLp);
            while($rowp=mysqli_fetch_assoc($rsp))
            {
                $price += ($rowp['price'] / $rowp['per_item']) * $quant;
            }
        }

        return $price;
    }

    function SemiProductPrice($number)
    {
        require("mysql_connect.php");
        $price = 0;
        $prefix_raw =  GetProperty("prefix_raw");
        $strSQL = "SELECT * FROM product_contains WHERE parent = '$number' AND child LIKE '$prefix_raw%'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            $quant = $row['quantity'];
            $child = $row['child'];
            $strSQLp = "SELECT * FROM products WHERE number = '$child'";
            $rsp=mysqli_query($link,$strSQLp);
            while($rowp=mysqli_fetch_assoc($rsp))
            {
                $price += ($rowp['price'] / $rowp['per_item']) * $quant;
            }
        }

        return $price;
    }

    function RawPrice($number)
    {
        require("mysql_connect.php");
        $strSQL = "SELECT * FROM products WHERE number = '$number'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs))
        {
            $price = ($row['price'] / $row['per_item']);
        }

        return $price;
    }

    function ListStyleSelect()
    {
        if(!isset($_SESSION['list_style'])) $_SESSION['list_style'] = 'detail';
        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $active_detail = ($_SESSION['list_style']=='detail') ? "disabled" : "";
        $active_list = ($_SESSION['list_style']=='list') ? "disabled" : "";
        $active_grid = ($_SESSION['list_style']=='grid') ? "disabled" : "";

        $retval ='
            <form action="'.$actual_link.'" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="list_style_container">
                    <button type="submit" name="change_liststyle" value="detail" class="button_s t_button" '.$active_detail.'>Detail</button>
                    <button type="submit" name="change_liststyle" value="list" class="button_s t_button" '.$active_list.'>Liste</button>
                    <!--<button type="submit" name="change_liststyle" value="grid" class="button_s t_button" '.$active_grid.'>Raster</button>-->
                </div>
            </form>
        ';
        return $retval;
    }

    function ListItem($index,$link,$title)
    {
        // window.stop() is for a faster change to another page when the current page has very long load times
        $retval = "<a href=\"$link\" onclick='SetCurrentListItem($index,\"$title\");window.stop();'><li class=\"side_menu_option\" id=\"mListItem$index\">$title</li></a>";
        return $retval;
    }

    function CountrySelectList($mostCommonlyUsed="0")
    {
        $retval = '';
        require("mysql_connect.php");
        if($mostCommonlyUsed != "0")
        {
            $cp = explode(',',$mostCommonlyUsed);
            foreach($cp as $country) $retval.='<option value="'.$country.'">'.fetch("country_list","name","alpha2",$country).'</option>';
            $retval .= '<option disabled>-------------------------------------------</option>';
        }

        $strSQL = "SELECT * FROM country_list ORDER BY name ASC";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs)) $retval .= '<option value="'.$row['alpha2'].'">'.$row['name'].'</option>';
        return $retval;
    }


    function IfSetFill($table,$field,$id,$idvalue,$default_value="")
    {
        require("mysql_connect.php");
        $retval = "|!NOVALUESET!|";
        $strSQL = "SELECT * FROM $table WHERE $id = '$idvalue'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs)) $retval = $row[$field];

        return ($retval == "|!NOVALUESET!|") ? $default_value : $retval;
    }

    function SalutationCode($code)
    {
        if($code == "SN") return '';
        else if($code == "SM") return 'Herr';
        else if($code == "SF") return 'Frau';
        else if($code == "SC") return 'Firma';
    }

    function PaymentCode($code)
    {
        if($code == "directDebit") return 'Sofort&uuml;berweisung';
        else if($code == "kreditCard") return 'Kredidkarte';
        else if($code == "cashOnDelivery") return 'Nachnahme';
        else if($code == "paypal") return 'PayPal';
        else if($code == "cahs") return 'Barzahlung';
        else if($code == "other") return 'Andere';
        else if($code == "none") return '<i>Nicht ausgewählt</i>';
    }

    function GenerateCustomerNumber()
    {
         return GetProperty("prefix_customer").'-'.NumberFormatEx(InkrementProperty("customer_ctr",99999999),8);
    }

    function NotificationBanner($message,$icon='')
    {
        $_SESSION['notificationMessage']=$message;
        $_SESSION['notificationIcon'] = ($icon=='check' OR $icon=='cross' OR $icon=='info') ? $icon : '';
    }

    function DateFormat($dateString,$format,$sp='-',$direction='B')
    {
        $strParts = explode(' ',$dateString);
        $dateParts = explode('-',$strParts[0]);
        $timeParts = explode(':',$strParts[1]);
        $Y = $dateParts[0];
        $M = $dateParts[1];
        $D = $dateParts[2];
        $h = $timeParts[0];
        $m = $timeParts[1];
        $s = $timeParts[2];

        if($format="date") return (($direction=='B') ? ($Y.$sp.$M.$sp.$D) : ($D.$sp.$M.$sp.$Y));
        else if($format="time") return (($direction=='B') ? ($h.$sp.$m.$sp.$s) : ($s.$sp.$m.$sp.$h));
        //else if($format="datetime") return (($direction=='B') ? ($h.$sp.$m.$sp.$s) : ($s.$sp.$m.$sp.$h));
    }

    function OrderPrice($orderNumber,$addShipping='0',$addDiscount='0')
    {
        require("mysql_connect.php");
        $price = 0;
        $strSQL = "SELECT * FROM order_contains INNER JOIN products ON order_contains.product_number = products.number WHERE order_number = '$orderNumber'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs)) $price += $row['resell_price']*$row['quantity'];

        return $price;
    }

    function ProductsAmount($orderNumber)
    {
        require("mysql_connect.php");
        $amt = 0;
        $strSQL = "SELECT * FROM order_contains WHERE order_number = '$orderNumber'";
        $rs=mysqli_query($link,$strSQL);
        while($row=mysqli_fetch_assoc($rs)) $amt += $row['quantity'];
        return $amt;
    }

    function PreventAutoScroll()
    {
        return '
            <script language="JavaScript" type="text/javascript">
                //Prevent Autoscroll for target-anchors
                function bgenScroll() {
                 if (window.pageYOffset!= null){
                  st=window.pageYOffset+"";
                 }
                 if (document.body.scrollWidth!= null){
                  if (document.body.scrollTop){
                  st=document.body.scrollTop;
                  }
                  st=document.documentElement.scrollTop;
                 }
                  setTimeout("window.scroll(0,st)",10);
                }
            </script>';
    }

    function SearchBar()
    {
        echo '
        <div class="searchbar_container_min" id="searchContainer">
            <form action="/search" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                <script>
                    document.addEventListener("keydown", function(event) {

                        // This function only selects the searchbar when no other textbox or textfield is selected
                        if (!(document.activeElement.nodeName == "TEXTAREA" || document.activeElement.nodeName == "INPUT" || (document.activeElement.nodeName == "DIV" && document.activeElement.isContentEditable)))
                        {
                            if (!event.ctrlKey && event.keyCode!=116 && /[a-zA-Z0-9-_ ]/.test(String.fromCharCode(event.keyCode)))
                            {
                                document.getElementById("codereader").focus();
                            }
                        }
                    });

                    function SearchEnterCheck(e)
                    {
                        if (e.keyCode == 13)
                        {
                            document.getElementById("codereader").readOnly = true;

                            document.getElementById("searchContainer").className = "searchbar_container_min";
                            document.getElementById("codereader").className = "search_textbox_min";
                            document.getElementById("searchbtn").className = "search_button_min";
                            document.getElementById("outSearchButton").value = String.fromCodePoint(0x1F50D);
                        }
                    }

                    function SearchBoxIncreaseSize()
                    {
                        document.getElementById("searchContainer").className = "searchbar_container_max";
                        document.getElementById("codereader").className = "search_textbox_max";
                        document.getElementById("searchbtn").className = "search_button_max";
                        document.getElementById("outSearchButton").value = "Suchen";
                    }

                    function SearchBoxDecreaseSize()
                    {
                        document.getElementById("searchContainer").className = "searchbar_container_min";
                        document.getElementById("codereader").className = "search_textbox_min";
                        document.getElementById("searchbtn").className = "search_button_min";
                        document.getElementById("outSearchButton").value = String.fromCodePoint(0x1F50D);
                    }


                </script>
                <input name="search_query" autocomplete="'.GetProperty("search_autocomplete").'" id="codereader" placeholder="Suche..." onfocus="SearchBoxIncreaseSize();" onblur="SearchBoxDecreaseSize();" onkeypress="SearchEnterCheck(event)" class="search_textbox_min"><button id="searchbtn" style="font-size: 12pt;" class="search_button_min"><output id="outSearchButton">&#x1F50D;</output></button>
            </form>
        </div>
        ';
    }

    function FullScreenError($size,$title,$subtitle="",$addCode="")
    {
        switch($size)
        {
            case 's': $fst = "40pt"; $fsst = "20pt"; break;
            case 'm': $fst = "60pt"; $fsst = "25pt"; break;
            case 'l': $fst = "80pt"; $fsst = "30pt"; break;
        }


        return '
            <style>
            @keyframes fadeError{
                0% {opacity:0;}
                30% {opacity:0;}
                100%{opacity:1;}
            }
            .errorTitle{
                font-size:'.$fst.';
                animation: fadeError;
                animation-duration:0.8s;
            }
            .errorSubTitle{
                font-size:'.$fsst.';
                animation: fadeError;
                animation-duration:1.5s;
            }
            .errorAddCode{
                animation: fadeError;
                animation-duration:2s;
            }
            </style>
            <div style="position:fixed; top:40%;width:80%;">
                <center>
                    <span class="errorTitle">'.$title.'</span><br>
                    <span class="errorSubTitle">'.$subtitle.'</span><br>
                    <div class="errorAddCode">'.$addCode.'</div>
                </center>
            </div>
        ';
    }



    function BarCode($string,$showString = false)
    {
        require ("barcode/vendor/autoload.php");

        $Bar = new Picqer\Barcode\BarcodeGeneratorHTML();
        $code = $Bar->getBarcode($string, $Bar::TYPE_CODE_128);

        return '<div id="qrbox">'.$code.' '.(($showString) ? $string : '').'</div>';
    }

    function BarCodeImg($string,$showString = false)
    {
        require ("barcode/vendor/autoload.php");

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        return '<img src="data:image/png;base64,'.base64_encode($generator->getBarcode($string, $generator::TYPE_CODE_128)).'">'.(($showString) ? ('<br>'.$string) : '').'';
    }

    function OrderTable()
    {
        require("mysql_connect.php");

        // WARNING: FIRST PARAMETER MUST BE A SQL-SELECT QUERY-STRING

        // Fetching Parameters for operation-row
        //
        // Syntax: Symbol|Title|Color|Operation-Type|<Additional>
        //
        // Symbols: X:  X-Icon                      (Preset)
        //          P:  Production-Icon             (Preset)
        //          C:  Cash-Icon                   (Preset)
        //          S:  Lock-Icon                   (Preset)
        //          L:  Arrow-Icon (Left)           (Preset)
        //          R:  Arrow-Icon (Right)          (Preset)
        //          U:  Arrow-Icon (Up)             (Preset)
        //          D:  Arrow-Icon (Down)           (Preset)
        //          E:  Edit (Hand+Pen)             (Preset)
        //       Else: HTML-Formated Icon-String    (Custom - &#x2BC8;)
        //
        // Title:   Title of the button
        //
        // Color:   R:  #C20000 Red         (Preset)
        //          Y:  #FFA500 Yellow      (Preset)
        //          G:  #32CD32 Green       (Preset)
        //          B:  #1E90FF Blue        (Preset)
        //       Else: HEX-Formated Color   (Custom - #XXXXXX)
        // Operation-Type:  PF: Production-Step Forwards
        //                  PB: Production-Step Backwards
        //                  RM: Remove/Hide Order
        //                  PS: Change Payment Status
        //                  LC: Locking Options
        //                  RL: Redirect Link   - Use <Additional>-Tag
        //                  BL: Blank Link      - Use <Additional>-Tag
        //                  DL: Download Link   - Use <Additional>-Tag
        //                  BR: Line Break      (use as "X|X|X|BR")
        //                  NO: No Action
        //                Else: Error
        // Additional (Optional):   When using RL,BL or DL. Contains Link.
        $operation_count = func_num_args();
        $operations = func_get_args();

        $operationGroup='';
        for($i=1;$i<$operation_count;$i++)
        {
            $operation_parts = explode('|',$operations[$i]);

            // Operation-Icon
            switch($operation_parts[0])
            {
                case 'X': $operation_icon = "&#x2718;"; break;
                case 'P': $operation_icon = "&#128736;"; break;
                case 'C': $operation_icon = "&#128176;"; break;
                case 'S': $operation_icon = "&#128274;"; break;
                case 'L': $operation_icon = "&#9664;"; break;
                case 'R': $operation_icon = "&#9654;"; break;
                case 'U': $operation_icon = "&#9650;"; break;
                case 'D': $operation_icon = "&#9660;"; break;
                case 'E': $operation_icon = "&#9997;"; break;
                default : $operation_icon = $operation_parts[0]; break;
            }

            // Operation-Title
            $operation_title = $operation_parts[1];

            // Operation-Color
            switch($operation_parts[2])
            {
                case 'R': $operation_color = "#C20000"; break;
                case 'G': $operation_color = "#32CD32"; break;
                case 'B': $operation_color = "#1E90FF"; break;
                case 'Y': $operation_color = "#FFA500"; break;
                default : $operation_color = $operation_parts[2]; break;
            }

            // Operation-Type
            switch($operation_parts[3])
            {
                case 'PF': $operation_type = '<button class="text_button" type="submit" name="orderMoveFW" value="|!ORDERNUMBER-PLACEHOLDER!|">|!TEXT-PLACEHOLDER!|</button><br>'; break;
                case 'PB': $operation_type = '<button class="text_button" type="submit" name="orderMoveBW" value="|!ORDERNUMBER-PLACEHOLDER!|">|!TEXT-PLACEHOLDER!|</button><br>'; break;
                case 'RM': $operation_type = '<a href="#remove|!ORDERNUMBER-PLACEHOLDER!|"><button class="text_button" type="button" onclick="bgenScroll();">|!TEXT-PLACEHOLDER!|</button></a><br>'; break;
                case 'PS': $operation_type = '<a href="#paymentStatus|!ORDERNUMBER-PLACEHOLDER!|"><button class="text_button" type="button" onclick="bgenScroll();">|!TEXT-PLACEHOLDER!|</button></a><br>'; break;
                case 'LC': $operation_type = '<a href="#lock|!ORDERNUMBER-PLACEHOLDER!|"><button class="text_button" type="button" onclick="bgenScroll();">|!TEXT-PLACEHOLDER!|</button></a><br>'; break;
                case 'RL': $operation_type = '<a href="'.$operation_parts[4].'"><button class="text_button" type="button">|!TEXT-PLACEHOLDER!|</button></a><br>'; break;
                case 'BL': $operation_type = '<a href="'.$operation_parts[4].'" target="_blank"><button class="text_button" type="button">|!TEXT-PLACEHOLDER!|</button></a><br>'; break;
                case 'DL': $operation_type = '<a href="'.$operation_parts[4].'" target="_blank" download><button class="text_button" type="button">|!TEXT-PLACEHOLDER!|</button></a><br>'; break;
                case 'BR': $operation_type = '<br>'; break;
                case 'NO': $operation_type = ''; break;
                default  : $operation_type = ''; break;
            }

            $operationGroup .= str_replace('|!TEXT-PLACEHOLDER!|','<span style="color: '.$operation_color.'">'.$operation_icon.' '.$operation_title.'</span>',$operation_type);
        }

        $strSQL = $operations[0];
        // Creating the Table
        if(MySQLResultCount($strSQL)!=0)
        {
            $i=0;
            $retval= '<form action="/'.basename($_SERVER["REQUEST_URI"], '.php').'" method="post" accept-charset="utf-8" enctype="multipart/form-data"><center><table class="orders_table">';
            $rs=mysqli_query($link,$strSQL);
            while($row=mysqli_fetch_assoc($rs))
            {

                $style_color = ($i%2==0) ? 'style="background:#DBEDFF;"' : 'style="background:#F2F2F2;"';
                $style_id = ($i++%2==0) ? 'id="shaded_cell"' : '';
                if(isset($_GET['selected']) AND $_GET['selected']==$row['order_number']) $style_id = 'id="marked_cell"';

                $retval.= '
                    <tr>
                        <td '.$style_id.'>
                            '.str_replace('|!ORDERNUMBER-PLACEHOLDER!|',$row['order_number'],$operationGroup).'
                        </td>
                        <td '.$style_id.'>
                            <u><b>Kunde:</b></u><br>
                            '.(($row['salutation']!= "SN") ? (SalutationCode($row['salutation']).'<br>') : '').'
                            '.(($row['title']!='') ? ($row['title'].' ') : '').$row['first_name'].' '.$row['last_name'].'<br>
                            '.$row['adressline1'].'<br>
                            '.(($row['adressline2']=='') ? ($row['zip'].' '.$row['city']) : $row['adressline2']).'<br>
                            '.(($row['adressline2']=='') ? $row['country'] : ($row['zip'].' '.$row['city'])).'<br>
                            '.(($row['adressline2']!='') ? $row['country'] : '').'
                        </td>
                        <td '.$style_id.'>
                            <br>
                            '.(($row['locked'])?'&#128274;':'').'<u><b>Bestelldaten:</b></u><br>
                            <b>Kundennummer:</b> '.$row['customer_number'].'<br>
                            <b>Bestellnummer:</b> '.$row['order_number'].'<br><br>
                            <b>Bestelldatum:</b> '.DateFormat($row['order_date'],'date','.','F').'<br>
                            <b>Artikel:</b> '.ProductsAmount($row['order_number']).' Stk.
                            <br><br>
                        </td>
                        <td '.$style_id.'>
                            <u><b>Zahlung:</b></u><br>
                            <b>Zahlungsmethode:</b> '.PaymentCode($row['payment_type']).'<br>
                            <b>Zahlungsstatus:</b> '.(($row['payment_status']=='paid') ? 'Bezahlt' : 'Ausstehend').'<br><br>
                            <b>Preis:</b> &euro; '.number_format(OrderPrice($row['order_number']),2).'
                        </td>
                        <td '.$style_id.'>
                            <a href="#orderdetails'.$row['order_number'].'" onclick="bgenScroll();"><button class="button_m t_button" type="button">Auftragsdaten</button></a><br>
                            <a href="#documents'.$row['order_number'].'" onclick="bgenScroll();"><button class="button_m t_button" type="button">Dokumente</button></a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=5 class="foldup_cell" id="orderdetails'.$row['order_number'].'" '.$style_color.'>
                            <center>
                                <table style="width:1100px;" class="classic_table">
                                    <tr>
                                        <td class="classicCell"><b>Menge:</b></td>
                                        <td class="classicCell"><b>Artikelnummber:</b></td>
                                        <td class="classicCell"><b>Bezeichnung:</b></td>
                                        <td class="classicCell"><b>Attribute:</b></td>
                                        <td class="classicCell"><b>Einzelpreis:</b></td>
                                        <td class="classicCell"><b>Summe:</b></td>
                                    </tr>
                                    ';
                                    $order_number = $row['order_number'];
                                    $strSQLP = "SELECT * FROM order_contains INNER JOIN products ON order_contains.product_number = products.number WHERE order_contains.order_number = '$order_number';";
                                    $rsP=mysqli_query($link,$strSQLP);
                                    while($rowP=mysqli_fetch_assoc($rsP))
                                    {
                                        $retval.= '
                                            <tr>
                                                <td class="classicCell">'.$rowP['quantity'].' '.$rowP['unit'].'</td>
                                                <td class="classicCell">'.$rowP['number'].'</td>
                                                <td class="classicCell">'.$rowP['name'].'</td>
                                                <td class="classicCell">'.OrderAttributes($rowP['order_number'],$rowP['product_number']).'</td>
                                                <td class="classicCell">&euro; '.number_format($rowP['resell_price'],2).'</td>
                                                <td class="classicCell">&euro; '.number_format($rowP['resell_price'] * $rowP['quantity'],2).'</td>
                                            </tr>
                                        ';
                                    }
                                    $retval.= '
                                </table>
                                <br>
                                Bestell-Kennung<br>
                                '.BarCodeImg($row['order_number'],true).'
                                <br>
                                <a href="#close">Schlie&szlig;en</a>
                            </center>
                        </td>
                    </tr>
                    ';
                    $orderConfirmationExists = (file_exists('files/customers/orderConfirmations/orderConfirmation_'.$row['order_number'].'.pdf')) ? 1 : 0 ;
                    $billExists = (file_exists('files/customers/bills/bill_'.$row['order_number'].'.pdf')) ? 1 : 0 ;
                    $deliveryNoteExists = (file_exists('files/customers/deliveryNotes/deliveryNote_'.$row['order_number'].'.pdf')) ? 1 : 0 ;
                    $orderConfirmationLink = ($orderConfirmationExists) ? ('/files/customers/orderConfirmations/orderConfirmation_'.$row['order_number'].'.pdf') : '#';
                    $billLink = ($billExists) ? ('/files/customers/bills/bill_'.$row['order_number'].'.pdf') : '#';
                    $deliveryNoteLink = ($deliveryNoteExists) ? ('/files/customers/deliveryNotes/deliveryNote_'.$row['order_number'].'.pdf') : '#';
                    $retval.= '
                    <tr>
                        <td colspan=5 class="foldup_cell" id="documents'.$row['order_number'].'" '.$style_color.'>
                            <table style="width:100%">
                                <tr>
                                    <td><b><u>Auftragsbest&auml;tigung:</u></b></td>
                                    <td>
                                        <b>Ausgestellt am:</b><br>
                                        '.(($row['order_date'] == '0000-00-00 00:00:00') ? 'Ausstehend...' : DateFormat($row['order_date'],"date",".","F")).'
                                    </td>
                                    <td>
                                        <b>An Kunden gesendet:</b><br>
                                        '.(($row['order_confirmation_status']=='sent') ? 'Versendet' : 'Ausstehend').'
                                    </td>
                                    <td>
                                        <a href="'.$orderConfirmationLink.'" target="_blank">
                                            <button type="button" class="button_m t_button" style="width:200px;height:55px;vertical-align:top;" '.((!$orderConfirmationExists) ? 'title="Nicht vorhanden" disabled' : '').'>
                                                Auftragsbest&auml;tigung<br>Anzeigen
                                            </button>
                                        </a>
                                        <a href="'.$orderConfirmationLink.'" target="_blank" '.(($orderConfirmationExists) ? 'download' : '').'>
                                            <button type="button" class="download_button t_button" style="width:40px;height:55px;vertical-align:top;" '.((!$orderConfirmationExists) ? 'title="Nicht vorhanden" disabled' : '').'></button>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b><u>Rechnung:</u></b></td>
                                    <td>
                                        <b>Ausgestellt am:</b><br>
                                        '.(($row['bill_date'] == '0000-00-00 00:00:00') ? 'Ausstehend...' : DateFormat($row['bill_date'],"date",".","F")).'
                                    </td>
                                    <td>
                                        <b>An Kunden gesendet:</b><br>
                                        '.(($row['bill_status']=='sent') ? 'Versendet' : 'Ausstehend').'
                                    </td>
                                    <td>
                                        <a href="'.$billLink.'" target="_blank">
                                            <button type="button" class="button_m t_button" style="width:200px;height:55px;vertical-align:top;" '.((!$billExists) ? 'title="Nicht vorhanden" disabled' : '').'>
                                                Rechnung<br>Anzeigen
                                            </button>
                                        </a>
                                        <a href="'.$billLink.'" target="_blank" '.(($billExists) ? 'download' : '').'>
                                            <button type="button" class="download_button t_button" style="width:40px;height:55px;vertical-align:top;" '.((!$billExists) ? 'title="Nicht vorhanden" disabled' : '').'></button>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b><u>Lieferschein:</u></b></td>
                                    <td>
                                        <b>Lieferung versendet am:</b><br>
                                         '.(($row['shipping_date'] == '0000-00-00 00:00:00') ? 'Ausstehend...' : DateFormat($row['shipping_date'],"date",".","F")).'
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        <a href="'.$deliveryNoteLink.'" target="_blank">
                                            <button type="button" class="button_m t_button" style="width:200px;height:55px;vertical-align:top;" '.((!$deliveryNoteExists) ? 'title="Nicht vorhanden" disabled' : '').'>
                                                Lieferschein<br>Anzeigen
                                            </button>
                                        </a>
                                        <a href="'.$deliveryNoteLink.'" target="_blank" '.(($deliveryNoteExists) ? 'download' : '').'>
                                            <button type="button" class="download_button t_button" style="width:40px;height:55px;vertical-align:top;" '.((!$deliveryNoteExists) ? 'title="Nicht vorhanden" disabled' : '').'></button>
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <br>
                            <center><a href="#close">Schlie&szlig;en</a></center>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=5 class="foldup_cell" id="paymentStatus'.$row['order_number'].'" '.$style_color.'>
                            <center>
                                Zahlungsmethode: '.PaymentCode($row['payment_type']).'<br>
                                Zahlungsstatus Aktuell: '.(($row['payment_status']=='paid') ? 'Bezahlt' : 'Ausstehend').'<br><br>

                                <h3 style="margin:0px;">Zahlungsstatus &auml;ndern:</h3><br>
                                <button class="button_m t_button" name="orderSetPaid" type="submit" value="'.$row['order_number'].'" '.(($row['payment_status']=='paid') ? 'disabled' : '').'><span style="color: #32CD32">Zahlung<br>eingegangen</span></button>
                                <button class="button_m t_button" name="orderSetPaymentPending" type="submit" value="'.$row['order_number'].'" '.(($row['payment_status']=='pending') ? 'disabled' : '').'><span style="color: #C20000">Zahlung<br>ausstehend</span></button>
                            </center>
                            <br>
                            <center><a href="#close"><button class="button_m t_button" name="orderSetPaymentPending" type="button">Abbrechen</button></a></center>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=5 class="foldup_cell" id="remove'.$row['order_number'].'" '.$style_color.'>
                            <center>
                               <h3 style="margin:0px;">Diese Bestellung verstecken?</h3><br>
                               <button class="button_m t_button_warning" name="orderRemove" type="submit" value="'.$row['order_number'].'">Bestellung l&ouml;schen</button><br>
                               <a href="#close"><button class="button_m t_button" name="orderSetPaymentPending" type="button">Abbrechen</button></a><br>
                                    Gel&ouml;schte Bestellungen k&ouml;nnen bis zu 30 Tage nach dem l&ouml;schen wiederhergestellt werden, danach werden sie dauerhaft gel&ouml;scht.<br>
                                    Bestellungen k&ouml;nnen unter "Mehr > Papierkorb" wieder hergestellt werden.
                            </center>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=5 class="foldup_cell" id="lock'.$row['order_number'].'" '.$style_color.'>
                            <center>
                                <h3 style="margin:0px;">Bestellung Sperren:</h3><br>
                                <button class="button_m t_button" name="orderLock" type="submit" value="'.$row['order_number'].'" '.(($row['locked']=='1') ? 'disabled' : '').'><span style="color: #32CD32">Sperren</span></button>
                                <button class="button_m t_button" name="orderUnlock" type="submit" value="'.$row['order_number'].'" '.(($row['locked']=='0') ? 'disabled' : '').'><span style="color: #C20000">Entsperren</span></button>
                                <br>
                                Gesperrte Bestellungen k&ouml;nnen nicht bearbeitet oder ge&auml;ndert werden.
                                <br><br>
                            </center>
                            <center><a href="#close"><button class="button_m t_button" name="orderSetPaymentPending" type="button">Abbrechen</button></a></center>
                        </td>
                    </tr>
                ';
            }
            $retval.= '</table></center>';
        }
        else
        {
            $retval= FullScreenError("s","Keine Bestellungen vorhanden.",'F&uuml;r diesen Bereich wurden keine Bestellungen gefunden.');
        }
        return $retval;
    }

    function ThisPage()
    {
        return  basename($_SERVER["REQUEST_URI"], '.php');
    }
?>