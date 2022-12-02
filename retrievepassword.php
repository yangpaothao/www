<?php
require("./common/page.php");
require("./common/pdocon.php");
require("./common/sendmail.php");
$load_headers = new Page_Loader();
$db = new PDOCON();
if(count($_POST) > 0 && isset($_POST['cmd']))
{
    $_REQUEST['cmd']();
    exit();
}
if(count($_GET) > 0)
{
    $keys = array_keys($_GET);
    foreach($keys as $value)
    {
        $_POST[$value] = $_GET[$value];
    }
    if(isset($_GET['cmd']))
    {
        $_REQUEST['cmd']();
        exit();
    }
}?>
<!DOCTYPE html>
<html>
    <head>
        <?php
            $temp_host = filter_input(INPUT_SERVER, 'SERVER_NAME'); // will get 'localhost'
            $temp_page = filter_input(INPUT_SERVER, 'PHP_SELF'); // will look like /index.php or /somedir/somepage.php
            $explode_page = explode("/", $temp_page); //This variable will now be an array and the page name is the last element of this array
            $this_page = end($explode_page); //this variable will hold the page name like index.php
            $load_headers::Load_Header(strtok($this_page, ".")); //by using strtok($this_page, "."), we will get just 'index'.
        ?>
        <script type="text/javascript">
            $( document ).ready(function() {
                $("#txtlogin").val("");
                $("#txtemail").val("");
            });
            function showRetrieves(obj){
               
                $.post('<?=$_SERVER['PHP_SELF']; ?>', 'cmd=ShowRetrieves&thisid='+$(obj).prop('id'), function(result){
                    $("#divemail").html(result);
                });
               
            }
            function validateLogin(){
                $.post('<?=$_SERVER['PHP_SELF']; ?>', 'cmd=ValidateLogin&txtlogin='+$("#txtlogin").val(), function(result){
                    if(result != "EXISTS"){
                        alert("This login does not exist.  Please try again.");
                        $("#txtlogin").select();
                        return(false);
                    }
                    getQuestions();
                }); 
            }
            function validateThisemail(obj){
                if($(obj).val() == ""){
                    return(false);
                }
                if(validateEmail(obj) == false){
                    return(false);
                }
                $.post('<?=$_SERVER['PHP_SELF']; ?>', 'cmd=ValidateThisemail&txtemail='+$("#txtemail").val(), function(result){
                    if(result != "EXISTS"){
                        alert("This email does not exist.  Please try again.");
                        $("#txtemail").focus();
                        $("#txtemail").select();
                        $("#btnsubmitretrieve").prop('disabled', true);
                    }
                    else{
                        $("#btnsubmitretrieve").prop('disabled', false);
                    }
                        
                });
            }
            function submitRetrieveform(){
                if($("#txtlogin").val() != "" && $("#txtemail").val() != "")
                {
                    $.post('<?=$_SERVER['PHP_SELF']; ?>', 'cmd=SubmitRetrieveform&txtlogin='+$("#txtlogin").val()+'&txtemail='+$("#txtemail").val(), function(result){
                        alert(result);
                        window.location.replace("./index.php");
                    }); 
                }
            }
            function getQuestions(){
                $.post('<?=$_SERVER['PHP_SELF']; ?>', 'cmd=GetQuestions&txtlogin='+$("#txtlogin").val(), function(result){
                    $("#div_questions_container").html(result);
                });
            }
            function selectStr(obj){
                if($(obj).val() == "**********")
                {
                    $(obj).val('');
                }
                $(obj).focus();
            }
            function checkQuestionanswer(obj){
                if($(obj).val().length < 3)
                {
                    alert('Password must be more at least 3 characters long.  Please try again.');
                    $(obj).focus();
                    $(obj).select();
                    return(false);
                }
                $.post('<?=$_SERVER['PHP_SELF']; ?>', 'cmd=CheckQuestionanswer&txtanswer='+$(obj).val()+'&txtanswerno='+$(obj).prop('id').slice(-1)+'&txtlogin='+$("#txtquestionlogin").val(), function(result){
                    if(result == "Failed")
                    {
                        alert("Answer does not match what is in the system.  Please try again.");
                        $(obj).val('');
                        $(obj).focus();
                        $('body').data($(obj).prop('id'), false);
                        return(false);
                    }
                    $('body').data($(obj).prop('id'), true);
                    if($('body').data($("#txtanswer1").prop('id')) == true & $('body').data($("#txtanswer2").prop('id')) == true & $('body').data($("#txtanswer3").prop('id')) == true){
                        $.post('<?=$_SERVER['PHP_SELF']; ?>', 'cmd=ShowPasswordreset', function(result){
                            $("#frmretrievepasword").html(result);
                        });
                    }
                });
            }
            function submitNewquestionanswers(){
                $.post('<?=$_SERVER['PHP_SELF']; ?>', 'cmd=SubmitNewquestionanswers&txtpassword='+$("#txtnewpassword").val()+'&txtrecno='+$("#hidemrecno").val(), function(result){
                    if(result == "Success"){
                        alert("Your password has been updated successfully.");
                        window.location.replace("./index.php");
                    }
                    else{
                       alert(result); 
                    }
                });
            }
            function validatePassword(obj){
               var regex = /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{6,16}$/;
                if(!regex.test($(obj).val())){
                    alert("Make sure your password meets the minimum requirements.");
                    $(obj).select();

                    return(false);
                }
                if($("#txtnewpassword").val() != "" && $("#txtconfirmnewpassword").val() != ""){
                    if($("#txtnewpassword").val() != $("#txtconfirmnewpassword").val()){
                        alert("Password does not match, please try again.");
                        $(obj).select();
                        $("#btnsubmitquestionanswer").prop('disabled', true);
                        return(false);
                    }
                    else
                    {
                        $("#btnsubmitquestionanswer").prop('disabled', false);
                    }
                }
            }
        </script>
    </head>
    <body>
        <?php
            Main();
        ?>
    </body>
</html>
<?php
function ShowPasswordreset()
{?>
   <div id="div_restpassword" style="padding: 20px;">
        <table>
            <tr><td class="user-profile-lbl" style="color: black;">New Password:</td><td><input class="user-profile-input required" type="password" id="txtnewpassword" name="txtnewpassword" onchange="validatePassword(this);" value=""/></td></tr>
            <tr><td class="user-profile-lbl" style="color: black;">Confirm New Password:</td><td><input class="user-profile-input required" type="password" id="txtconfirmnewpassword" name="txtconfirmnewpassword" onchange="validatePassword(this);" value=""/></td></tr>
            <tr><td colspan="2" style="padding: 10px; text-align: center;"><button id="btnsubmitquestionanswer" onclick="submitNewquestionanswers();" value="Submit" disabled>Submit</button></td></tr>
        </table>
    </div><?php
}
function ShowRetrieves()
{
    if($_POST['thisid'] == "rdoemail")
    {?>
        <div class="div-loginname">
            <div class="div-namelbl">Login: </div>
            <div class="div-user"><input type="text" class="input-login-user required" id="txtlogin" name="txtlogin" value="" placeholder="Type in your login" autofocus onchange="validateLogin();" /></div>
        </div>
        <div class="div-loginpassword">
            <div class="div-passwordlbl">Email: </div>
            <div class="div-password"><input type="text" class="input-login-user required" id="txtemail" name="txtemail" value="" placeholder="Type in email" onchange="validateThisemail(this);" /></div>
        </div>
        <div class="div-buttons">
            <button name="btnsubmitretrieve" id="btnsubmitretrieve" onclick="submitRetrieveform();" value="Submit" disabled>Submit</button>
        </div><?php
    }
    else
    { ?>
        <div id="divquestions">
             <div class="div-loginname">
                 <div class="div-namelbl">Login: </div>
                 <div class="div-user"><input type="text" class="input-login-user required" id="txtlogin" name="txtlogin" value="" onchange="validateLogin();" autocomplete="off" placeholder="Type in your login" /></div>
             </div>
             <div name="div-questions-container" id="div_questions_container">  
             </div>
         </div><?php
    }
}
function SubmitNewquestionanswers()
{
    global $db, $load_headers;
    $thisfields = Array();
    $thistable = "employee_master";
    $realpassword = "";
    $getpasssword = $load_headers -> Hash_Me_Password($_POST['txtpassword']); //we hash user's entered pw.
    $thisdata = array("password" => $getpasssword);       
    $thiswhere = array("recno" => $_POST['txtrecno']);
    //file_put_contents("./dodebug/debug.txt", $tempstr, FILE_APPEND);  
    $result = $db->PDOUpdate($thistable, $thisdata, $thiswhere, $_POST['txtrecno']);
    if(isset($result))
    {
        echo "Success";
    }
    else
    {
        echo "Failed";
    }
}
function CheckQuestionanswer()
{
    //file_put_contents("./dodebug/debug.txt", "dump: ".var_dump($_POST), FILE_APPEND);
    global $db, $load_headers;
    $thisfields = Array("answer".$_POST['txtanswerno']);
    $thistable = "employee_master";
    $getpasssword = $load_headers -> Hash_Me_Password($_POST['txtanswer']); //we hash user's entered pw.     
    $thiswhere = array("login" => $_POST['txtlogin'], "answer".$_POST['txtanswerno'] => $getpasssword);
    $result = $db->PDOQuery($thistable, $thisfields, $thiswhere);
    //file_put_contents("./dodebug/debug.txt", "dump: ".var_dump($result), FILE_APPEND);
    if(($result->rowCount() == 0))
    {
        echo "Failed";
    }
}
function GetQuestions()
{
    global $db; // PDOQuery($thistable=null, $thisfields=null, $thiswhere=null, $thisorderby=null, $thisgroupby=null, $ordering=null, $ons=null)

    //$thisorderby = array('question');
    $sql = "SELECT em.recno as emrecno, qn.recno, qn.question FROM employee_master em INNER JOIN questionniares qn ON 
            em.question1 = qn.recno OR em.question2 = qn.recno OR em.question3 = qn.recno WHERE em.login = '".$_POST['txtlogin']."' ORDER BY qn.question";
    //file_put_contents("./dodebug/debug.txt", $sql."\n", FILE_APPEND);
    $rows = $db->PDOMiniquery($sql);
    $tempemrecno = "";
    if(isset($rows))
    {?>
        <table id="tblquestionniare"><?php
            $i=1;
            foreach($rows as $rs)
            {
                $tempemrecno = $rs['emrecno'];?>
                <tr>
                    <td class="user-profile-lbl" style="color: black;">Question <?= $i ?>:</td>
                    <td class="user-profile-input" style="color: black;"><?= $rs["question"] ?></td>
                </tr>
                <tr>
                    <td class="user-profile-lbl" style="color: black;">Answer <?= $i ?>:</td>
                    <td class="user-profile-lbl"><input type="password" name="txtanswer<?= $i ?>" id="txtanswer<?= $i ?>" class="user-profile-input required" value="**********" onclick="selectStr(this);" onchange="checkQuestionanswer(this);" placeholder="Type your answer here." /></td>
                </tr><?php
                $i++;
            }?>
        </table>
        <input type="hidden" name="hidemrecno" id="hidemrecno" value="<?= $tempemrecno ?>" />    
        <?php
    }
    else
    {
        echo "Login does not exist.  Please check and try again."; 
    }
}
function SubmitRetrieveform()
{
    global $db, $load_headers;
    
    $thisserver = $load_headers -> GET_THIS_SERVER(); //This will be 'localhost' or the webhosting domain, ex:  https://www.somedomain.com
    $sendstatus= "";
    $thisfields = array('firstname', 'lastname');
    $thistable = "employee_master";
    $thiswhere = array('login' => strtolower($_POST['txtlogin']), 'email' => strtolower($_POST['txtemail']));
    //PDOQuery($thistable=null, $thisfields=null, $thiswhere=null, $thisorderby=null, $thisgroupby=null, $ordering=null, $ons=null)
    $result = $db->PDOQuery($thistable, $thisfields, $thiswhere);
    if(isset($result))
    {
        //file_put_contents("./dodebug/debug.txt", var_dump($result), FILE_APPEND);
        foreach($result as $key => $rs)
        {
            $thisfirstname = $rs['firstname'];
            $thislastname = $rs['lastname'];
        }
        //We need to add the vericode to add to the row and also add password
        $realvericode = $load_headers ->Hash_Me_Vericode();
        $thisdata = array('vericode' => $realvericode); //We only need to update vericode cuz user my remember their current password and decided to ignore.
        $result = $db->PDOUpdate($thistable, $thisdata, $thiswhere);
        if($result == "Success")
        {
            //We want to send user an email and allow them to verify the email and change their password once they clicked on 
            //the link in the email.
            $sentto = Array();
            $replyto = Array();
            $ccto = Array();
            $bccto = Array();
            $attachment = Array();
            $subject = "";
            $body = "";
            $sendto[] = array($_POST['txtemail'] => $thisfirstname." ".$thislastname);
            $subject = "Account Reset Request";
            $body = "Please follow the link below to reset your account and change your password.<br><br>";
            $body .= "<a href='$thisserver/resetpassword.php?vericode=".$realvericode."'>Click here to verify your email and change your password.</a>";
            $sendstatus = sendmail($sendto, $replyto, $ccto, $bccto, $subject, $body, $attachment);
            echo "An email as been sent to the email we have on record.  Please check your email and follow the link to reset your password.";
        }
    }
    else
    {
        echo "The login and email given does not match.  Please try agian.";
    }
}
function ValidateLogin()
{
    global $db;
    $thisfields = Array();
    $thiswheres = Array();
    //QueryMe($thistype=null, $thistable=null, $thisfields=null, $thiswheres=null, $thisorderby=null, $thisgroupby=null, $ordering=null)
    $thistable = "employee_master";
    $thisfields = array('login');
    $thiswhere = array('login' => strtolower($_POST['txtlogin']));
    $result = $db->PDOQuery($thistable, $thisfields, $thiswhere);
    if(isset($result)) //Nott sure if isset() will check if some items is returned or at least something in asso array.
    {
       echo "EXISTS"; 
    }
}
function ValidateThisemail()
{
    global $db;
    $thisfields = Array();
    $thiswheres = Array();
    //QueryMe($thistype=null, $thistable=null, $thisfields=null, $thiswheres=null, $thisorderby=null, $thisgroupby=null, $ordering=null)
    $thistable = "employee_master";
    $thisfields = array('email');
    $thiswhere = array('email' => strtolower($_POST['txtemail']));
    $result = $db->PDOQuery($thistable, $thisfields, $thiswhere);
    if(isset($result)) //Nott sure if isset() will check if some items is returned or at least something in asso array.
    {
       echo "EXISTS"; 
    }
}
function Main()
{
    global $load_headers;?>
    <div class="main-div">
        <?php
        $load_headers::Load_Header_Logo_Marquee();?>
        <br><br> <?php
        $load_headers::Load_Header_Logo_Main();?>
        <br>
        <div class="main-div-body">
            <form name="frmretrievepasword" id="frmretrievepasword" autocomplete="off" method="post">
                <div class="main-div-body-retrieve-header">Retrieve Password</div><br><br>
                <div>
                    <div class="div-loginname">
                        <div class="div-namelbl"><input type="radio" id="rdoemail" name="rdoretrievebypassword" class="required" value="Email" style="cursor: pointer; height: 20px; width: 20px;" onclick="showRetrieves(this);" checked />Email</div>
                        <div class="div-namelbl"><input type="radio"  id="rdoquestions" name="rdoretrievebypassword" class="required" value="Questions" style="cursor: pointer; height: 20px; width: 20px;" onclick="showRetrieves(this);" />Questions</div>
                    </div>
                </div>
                <br><br>
                <div id="divemail">
                    <div class="div-loginname">
                        <div class="div-namelbl">Login: </div>
                        <div class="div-user"><input type="text" class="input-login-user required" id="txtlogin" name="txtlogin" value="" placeholder="Type in your login" autofocus onchange="validateLogin();" /></div>
                    </div>
                    <div class="div-loginpassword">
                        <div class="div-passwordlbl">Email: </div>
                        <div class="div-password"><input type="text" class="input-login-user required" id="txtemail" name="txtemail" value="" placeholder="Type in email" onchange="validateThisemail(this);" /></div>
                    </div>
                    <div class="div-buttons">
                        <button name="btnsubmitretrieve" id="btnsubmitretrieve" onclick="submitRetrieveform();" value="Submit" disabled>Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div><?php
}