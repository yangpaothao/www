<?php
session_start();
//date_default_timezone_set('America/Chicago'); //THIS MAKES THE WEBSITE USE THIS TIMEZONE AS THE TIME.
date_default_timezone_set('Australia/Sydney'); //THIS MAKES THE WEBSITE USE THIS TIMEZONE AS THE TIME.
$temp_page = filter_input(INPUT_SERVER, 'PHP_SELF'); // will look like /index.php or /somedir/somepage.php
$explode_page = explode("/", $temp_page); //This variable will now be an array and the page name is the last element of this array
$this_page = end($explode_page);
if(!isset($_SESSION['user']) && $this_page != "verifyme.php" && $this_page != "index.php" && $this_page != "retrievepassword.php" && $this_page != "resetpassword.php"  && $this_page != "apicurl.php" )
{
    header("Location: /index.php"); //Unless this is the main/front page, if user does not have a logged session, they will be forced to login first.
    exit();
}
if(!function_exists('GetTimes'))
{
    function GetTimes()
    {
        $thisutctime = gmdate('H:i:s');
        $thisdate = date('d M y');
        $thislocaltime = localtime(time(), true);
        $thislocalactualtime = ($thislocaltime['tm_hour'] < 10 ? "0".$thislocaltime['tm_hour'] : $thislocaltime['tm_hour']).":".($thislocaltime['tm_min'] < 10 ? "0".$thislocaltime['tm_min'] : $thislocaltime['tm_min']).":".($thislocaltime['tm_sec'] < 10 ? "0".$thislocaltime['tm_sec'] : $thislocaltime['tm_sec']);
        $thisarray = Array();
        $thisarray = array('thisutctime' => $thisutctime,
                           'thisdate' => $thisdate,
                           'thislocaltime' => $thislocalactualtime);
        echo json_encode($thisarray);
    }
}
if(!function_exists('Logout'))
{
    function Logout()
    {
        if(isset($_SESSION))
        {
            session_unset();
            session_destroy();
            echo 'Success';
        }
        else
        {
           echo 'Failed'; 
        }
    }
}
class Page_Loader {
    const DEV = 'localhost';
    const PROD = 'https://www.aviontracker.com';
    
    function __construct() {}
    
    static function Load_Header($page)
    {
        ?>
        <meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=0">
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <meta content=IE=edge, chrome="1" http-equiv="X-UA-Compatible">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Avion Tracker</title>
        <script type="text/javascript" src="./jquery-ui-1.13.2/external/jquery/jquery.js"></script>
        <script type="text/javascript" src="./jquery-ui-1.13.2/jquery-ui.js"></script>
        <script type="text/javascript" src="./Multiple-Dates-Picker-for-jQuery-UI/jquery-ui.multidatespicker.js"></script>
        <script type="text/javascript" charset="utf8" src="./datatables/datatables.js"></script> <!-- https://datatables.net/ -->
        <script type="text/javascript" src="./common/common.js"></script>
        <link href="./jquery-ui-themes-1.13.2/themes/base/jquery-ui.css" rel="stylesheet" />
        <link href="./jquery-ui-themes-1.13.2/themes/base/jquery-ui.min.css" rel="stylesheet" />
        <link href="./jquery-ui-themes-1.13.2/themes/base/theme.css" rel="stylesheet" />
        <link href="./Multiple-Dates-Picker-for-jQuery-UI/jquery-ui.multidatespicker.css" rel="stylesheet" />
        <script src="./select2-4.1.0-rc.0/dist/js/select2.min.js"></script>
        <link href="./css/all.css" rel="stylesheet" type="text/css" />
        <link href="./select2-4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="./datatables/datatables.css"> <!-- https://datatables.net/ --><?php
        if(isset($_SESSION['user']))
        {
            $thistable = "pages";
            //$thisfields = array("All");
            //$thiswheres = array("isactive" => true, "isdeleted" => false, 'pagename' => $page);
            $sqlmenu = "SELECT ma.* FROM menu_authorized ma INNER JOIN pages p ON ma.pagerecno = p.recno INNER JOIN profiles pro ON ma.profilerecno=pro.recno ";
            $sqlmenu .= "WHERE pro.profile = '".$_SESSION['profile']."' AND p.isdeleted = false AND isactive = true AND ma.isdeleted = false AND p.pagename='$page'";
            //file_put_contents("./dodebug/debug.txt", 'menuresult: '.$sqlmenu, FILE_APPEND);

            require_once("./common/pdocon.php");
            $db = new PDOCON();
            $menuresult = $db->PDOMiniquery($sqlmenu);
            $_SESSION['thisauth'] = [];
            if($db->PDORowcount($menuresult))
            {
                foreach($menuresult as $rsmenu)
                {
                    //$thisauth will get reinitialize everytimes it come to a new page, it will know whether it can read, write, modify or delete in this page by this array below.
                    $_SESSION['thisauth'] = explode(',', $rsmenu['action']); //$thisauth is short for $thisauthorization, it should now be an array of 'Read', 'Write', 'Modify', 'Delete'
                }
            }
        }
        if($page == "serviceorder")
        {?>
            <!-- http://keith-wood.name/signature.html -->
            <script src="./jquery-signature/jquery.ui.touch-punch.min.js"></script>
            <link type="text/css" href="./jquery-signature/jquery.signature.css" rel="stylesheet"> 
            <script type="text/javascript" src="./jquery-signature/jquery.signature.js"></script><?php
        }?>
        <script type="text/javascript">
            $(document).ready(function() {
                setInterval(getTimes, 1000);
            });
            function getTimes(){
                $.post('<?=$_SERVER['PHP_SELF']; ?>', 'cmd=GetTimes', function(result){
                    result = $.parseJSON(result);
                    $("#div_utctime").text(result.thisutctime+" (UTC)");
                    $("#div_date").text(result.thisdate);
                    $("#div_localtime").text(result.thislocaltime+" (Local)");
                });
            }
            function logout(){
                $.post('<?=$_SERVER['PHP_SELF']; ?>', 'cmd=Logout', function(result){
                    if(result == "Success"){
                        window.location.href = "./index.php";
                    }
                    else{
                        alert(result);
                    }
                });
            }
            function doProfile()
            {
                window.location.href = "profile.php";
            }
            function doAdmin()
            {
                window.location.href = "admin.php";
            }
            function doFlow(){
                window.location.href = "flow.php";
            }
            function doFid(){
                window.location.href = "fid.php";
            }
            function doFidnote(){
                window.location.href = "fidnote.php";
            }
            function doManagefidnote(){
               window.location.href = "fidnote.php?from=Modify"; 
            }
            function manageAircraft(){
               window.location.href = "manageaircraft.php"; 
            }
            function manageFlight(){
               window.location.href = "manageflight.php"; 
            }
            function manageServiceorders(){
                window.location.href = "manageServiceorders.php"; 
            }
            function manageAnnouncement(){
                window.location.href = "manageAnnouncement.php"; 
            }
            function manageImportant(){
                window.location.href = "manageImportant.php"; 
            }
        </script><?php
        if(file_exists("./js/$page.js"))
        {?>
            <script type="text/javascript" src="./js/<?=$page?>.js"></script><?php
        }
    }
    static function Load_Header_Logo_Marquee()
    {?>
        <div class="header-flightpath">
            <div class="header-main-logo"></div>
            <div class="header-airplane"><marquee direction="right" scrollamount="1"><a href="/index.php"><img src="../images/headers/qantaplane.webp" style="width: 200px; height:100%;"></a></marquee></div>
        </div><?php
    }
    static function Load_Header_Logo_Main()
    {?>
        <div class="main-div-header"  style="z-index: 1000;">
            <div id="div_utctime" class="header-time-bar"></div><div id="div_date" class="header-time-bar header-time-bar-date"></div><div id="div_localtime" class="header-time-bar"></div><?php
            $displayname = "";
            if(isset($_SESSION['fullname']))
            {
                if(isset($_SESSION['fullname']))
                {
                    $displayname = $_SESSION['fullname'];
                }?>
                <nav class="navmenu" id='navlist'>
                    <ul class="ul-parent">
                        <li class="li-menu">
                            <div style="white-space: nowrap;"><?php echo $displayname?></div>
                            Menu<!-- Will have a menu that will allow users to navigate to other pages and functions -->
                            <ul class="ul-child"><?php
                                if(in_array('Delete', $_SESSION['thisauth']))
                                {?>
                                    <li onclick="doAdmin();" class="li-menu-sub">Admin</li><?php
                                }?>
                                <li onclick="doProfile();" class="li-menu-sub">Profile</li>
                                <li href="#" onclick="manageAnnouncement();" class="li-menu-sub">Manage Announcement</li>
                                <li href="#" onclick="manageImportant();" class="li-menu-sub">Manage Important</li>
                                <li href="#" onclick="manageServiceorders();" class="li-menu-sub">Manage Service Orders</li>
                                <li href="#" onclick="manageFlight();" class="li-menu-sub">Manage Flight Schedule</li><?php                                
                                if(in_array('Write', $_SESSION['thisauth']) || in_array('Modify', $_SESSION['thisauth']) || in_array('Delete', $_SESSION['thisauth']))
                                {?> 
                                    
                                    <li href="#" onclick="manageAircraft();" class="li-menu-sub">Manage Aircraft</li>
                                    <li onclick="doFlow();" class="li-menu-sub">Flow</li><?php
                                            
                                }?>                                
                                <li onclick="doFid();" class="li-menu-sub">Flight Display</li>
                                <li onclick="logout();" class="li-menu-sub">Log Out</li>
                            </ul>
                        </li>
                    </ul>
                </nav><?php
            }?>
        </div><?php
    }
    static function Load_Footer()
    {?>
        <div class="main-div-footer">
            &copy;&nbsp;2022-2022, Avion Tracker, LLC.
        </div><?php
    }
    static function GET_THIS_SERVER()
    {
        $temp_host = filter_input(INPUT_SERVER, 'SERVER_NAME'); // will get 'localhost'
        return($temp_host);
    }
    static function Hash_Me_Password($temppassword = null)
    {
        if(is_null($temppassword))
        {
            //We need to add the vericode to add to the row and also add password
            $temppassword = md5(time()); 
            $temppw1 = substr($temppassword, 0, 3); //Get first 3 of the string
            $temppw2 = substr($temppassword, -3); //Get last 3 of the string
            $realpassword = $temppw2.((int)$temppw2+(int)$temppw1).$temppw1;  //realpasswod will be the last3 and then the sumb of the two and the first 3.
            return($realpassword);
        }
        else
        {
            //When we hash real pw we use sha1
            $realpassword = sha1($temppassword); 
            return($realpassword);
        }
    }
    static function Hash_Me_Vericode()
    {   
        $realvericode = sha1(microtime());  //This vericode will get sent to user and they will have to click on the link to verify.
        return( $realvericode); 
    }
    static function Hash_Me_Questionniare_Answers($tempanswer)
    {   
        $realanswer = sha1($tempanswer);  //This vericode will get sent to user and they will have to click on the link to verify.
        return( $realanswer); 
    }
    static function Check_Time_Conflict($time1, $time2)
    {
        //$time1 (arrival time) and $time2 (depature time) comes in format of 00:00 in 24 hours format.  EX: 01:01 or 23:01
        if(strtotime($time1) > strtotime($time2))
        {
            return("Failed");
        }
        else
        {
            return("Sucess");
        }
    }
}?>
