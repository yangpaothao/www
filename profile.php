<?php
require_once("./common/page.php");
require_once("./common/pdocon.php");
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
            function getUserprofile(obj){
                $(".div-menu-profile").each(function(){
                    $(this).css('background-color', '#1079B1');
                    $(this).css('color', 'white');
                })
                $(obj).css("background-color", "white");
                $(obj).css('color', 'black');
                $.post('<?=$_SERVER['PHP_SELF']; ?>', 'cmd=GetUserprofile', function(result){
                    $("#main_div_body_profile_right_container").html(result);
                });
            }
            function changePassword(obj){
                $(".div-menu-profile").each(function(){
                    $(this).css('background-color', '#1079B1');
                    $(this).css('color', 'white');
                })
                $(obj).css("background-color", "white");
                $(obj).css('color', 'black');
                $.post('<?=$_SERVER['PHP_SELF']; ?>', 'cmd=ChangePassword', function(result){
                    $("#main_div_body_profile_right_container").html(result);
                });
            }
            function setupAuthentication(obj){
                $(".div-menu-profile").each(function(){
                    $(this).css('background-color', '#1079B1');
                    $(this).css('color', 'white');
                })
                $(obj).css("background-color", "white");
                $(obj).css('color', 'black');
                $.post('<?=$_SERVER['PHP_SELF']; ?>', 'cmd=SetupAuthentication', function(result){
                    $("#main_div_body_profile_right_container").html(result);
                });
            }
            function setupQuestionniare(obj){
                $(".div-menu-profile").each(function(){
                    $(this).css('background-color', '#1079B1');
                    $(this).css('color', 'white');
                })
                $(obj).css("background-color", "white");
                $(obj).css('color', 'black');
                $.post('<?=$_SERVER['PHP_SELF']; ?>', 'cmd=SetupQuestionniare', function(result){
                    $("#main_div_body_profile_right_container").html(result);
                });
            }
            function clearOldpassword()
            {
                $("#txtpassword").val('');
            }
            function clearForm()
            {
                $("#textnewpassword").val('');
                $("#txtconfirmnewpassword").val('');
            }
            function validatePassword(obj){
               var regex = /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{6,16}$/;
               if(!regex.test($(obj).val())){
                   alert("Make sure your password meets the minimum requirements.");
                   $(obj).focus();
                   $(obj).select();

                   return(false);
               }
               if($(obj).prop('id') == "txtpassword"){
                   //We are going to check if the password user enter is correct.
                   $.post('<?=$_SERVER['PHP_SELF']; ?>', 'cmd=ValidatePassword&txtpassword='+$("#txtpassword").val(), function(result){
                        if(result == "Failed")
                        {
                            alert("The password you enter does not match the one in the system.  Please try again.");  //"The password you entered does not match the current password.  Please trya gain.";
                            $("#txtpassword").val('**********');
                            $("#txtpassword").focus();
                            $("#txtpassword").select();
                            return(false);
                        }
                    });
               }
               else
               {
                   if($("#txtnewpassword").val() != "" && $("#txtconfirmnewpassword").val() != ""){
                        if($("#txtnewpassword").val() != $("#txtconfirmnewpassword").val()){
                            alert("Password does not match, please try again.");
                            $(obj).focus();
                            $(obj).select();
                            return(false);
                        }
                    }
               }
            }
            function submitNewpassword(){
               $.post('<?=$_SERVER['PHP_SELF']; ?>', 'cmd=SubmitNewpassword&txtpassword='+$("#txtnewpassword").val(), function(result){
                   if(result == "Success")
                   {
                       alert("Password updated successfully.");
                   }
                   else
                   {
                       alert("ERROR in profile js line 110.  Failed to update password.  Contact I.T.");
                       return(false);
                   }
               });
            }
            function enableAuthentication(){
                $.post('<?=$_SERVER['PHP_SELF']; ?>', 'cmd=EnableAuthentication&btntext='+$("#btnauthentication").html(), function(result){
                    if(result == "Disabled")
                    {
                       $("#btnauthentication").html("Disabled")//#1D89D1
                       $("#btnauthentication").css("background-color", "#1D89D1");
                       $("#btnauthentication").prop('title', 'Click to enable.');
                    }
                    else
                    {
                       $("#btnauthentication").html("Enabled")//#1D89D1
                       $("#btnauthentication").css("background-color", "#288331;");
                       $("#btnauthentication").prop('title', 'Click to disable.');
                    }
                });
            }
            function validateAnswers(obj){
                if($(obj).val().length < 3){
                    alert("Answer must be atleast 3 character long.")
                    $(obj).focus();
                    $(obj).select();
                }
            }
            function validatQuestions(obj){
                
                var thisarray = [$("#sltquestion1").val(), $("#sltquestion2").val(), $("#sltquestion3").val()];
                countitem = 0;
                for(i=0; i<thisarray.length; i++)
                {
                    if(thisarray[i] == "Select")
                    {
                        countitem++;
                    }   
                }
                if(countitem < 2)
                {
                    if(thisarray.length !== new Set(thisarray).size)
                    {
                        alert("You can not select the same question twice.  Please check your questions and try again.");
                        $(obj).prop("selectedIndex", 0);
                        return(false);
                    }
                }
            }
            function clearAnswerform(){
                $('#tblquestionniare').find('input[type=text]').val('');
                $('#sltquestion1').prop("selectedIndex", 0);
                $('#sltquestion2').prop("selectedIndex", 0);
                $('#sltquestion3').prop("selectedIndex", 0);
            }
            function editQuestionniaresanswers(obj){
                //First we want to verify the user's password before we allow for the editing or renewing of these questions.
                var thispassword = prompt("Please Confirm your password", "Type your current password");
                if(thispassword == null)
                {
                    return(false);
                }
                else
                {
                    //User entered a password, we will now check this password.
                    $.post('<?=$_SERVER['PHP_SELF']; ?>', 'cmd=validatePassword&txtpassword='+thispassword, function(result){
                        if(result == "Failed"){
                            alert("Wrong password.  Please try again");
                            return(false);
                        }
                        else{
                            //enableQuestionniareedit();
                            $("#sltquestion1").prop('disabled', false);
                            $("#txtanswer1").prop('disabled', false);
                            $("#txtanswer1").prop('type', "text");
                            $("#sltquestion2").prop('disabled', false);
                            $("#txtanswer2").prop('disabled', false);
                            $("#txtanswer2").prop('type', "text");
                            $("#sltquestion3").prop('disabled', false);
                            $("#txtanswer3").prop('disabled', false);
                            $("#txtanswer3").prop('type', "text");
                            $(obj).hide();
                            $("#btnsubmitquestionanswer").show();
                        }   
                    });
                }
            }
            function enableQuestionniareedit(){
                $.post('<?=$_SERVER['PHP_SELF']; ?>', 'cmd=NewQuestionniares', function(result){
                    //User now has the option to redo their questions and answers.
                    $("#main_div_body_profile_right_container").html(result);
                });
            }
            function submitQuestionniaresanswers(obj){
                $(obj).hide();
                if($("#txtanswer1").val() == ""){
                    alert("Answer number 1 must not be empty.  Please type in a password and try again.");
                    $("#txtanswer1").focus();
                    $("#txtanswer1").select();
                    return(false);
                }
                if($("#txtanswer2").val() == ""){
                    alert("Answer number 2 must not be empty.  Please type in a password and try again.");
                    $("#txtanswer2").focus();
                    $("#txtanswer2").select();
                    return(false);
                }
                if($("#txtanswer3").val() == ""){
                    alert("Answer number 3 must not be empty.  Please type in a password and try again.");
                    $("#txtanswer3").focus();
                    $("#txtanswer3").select();
                    return(false);
                } //If we made it to here, we are ready to submit the new questions and answer.
                $.post('<?=$_SERVER['PHP_SELF']; ?>', 'cmd=SubmitQuestionniaresanswers&sltquestion1='+$("#sltquestion1").val()+'&sltquestion2='+$("#sltquestion2").val()+'&sltquestion3='+$("#sltquestion3").val()+'&txtanswer1='+
                                                       $("#txtanswer1").val()+'&txtanswer2='+$("#txtanswer2").val()+'&txtanswer3='+$("#txtanswer3").val(), function(result){
                    //User now has the option to redo their questions and answers.
                    $("#sltquestion1").prop('disabled', true);
                    $("#txtanswer1").val("**********");
                    $("#txtanswer1").prop('type', "password");
                    $("#txtanswer1").prop('disabled', true);
                    $("#sltquestion2").prop('disabled', true);
                    $("#txtanswer2").val("**********");
                    $("#txtanswer2").prop('type', "password");
                    $("#txtanswer2").prop('disabled', true);
                    $("#sltquestion3").prop('disabled', true);
                    $("#txtanswer3").val("**********");
                    $("#txtanswer3").prop('type', "password");
                    $("#txtanswer3").prop('disabled', true);
                    $("#btnsubmitquestions").hide();
                    $("#btnclearquestions").hide();
                    $("#btneditquestionanswer").show();
                    $("#btnsubmitquestionanswer").show();
                    alert("Successfully setup questionniares.");
                });
               
            }
            function emptyInputtext(obj){
                $(obj).select();
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
function Main()
{
    global $load_headers;?>
    <div class="main-div">
        <?php
        $load_headers::Load_Header_Logo_Marquee();?>
        <br><br> <?php
        $load_headers::Load_Header_Logo_Main();?>
        <div class="main-div-body-profile">
            <table>
                <tr>
                    <td>
                        <div class="main-div-body-profile-left" style="margin-top: -30px;">
                            <div class="main-div-body-profile-header">Profile</div>
                            <div style="float: left;">
                                <div class="div-menu-profile" onclick="getUserprofile(this);">User Profile</div><?php
                                if(in_array('Write', $_SESSION['thisauth']) || in_array('Modify', $_SESSION['thisauth']) || in_array('Delete', $_SESSION['thisauth']))
                                {?>
                                    <div class="div-menu-profile" onclick="changePassword(this);">Change Password</div>
                                    <div class="div-menu-profile" onclick="setupAuthentication(this);">Setup Two Steps Authentication</div>
                                    <div class="div-menu-profile" onclick="setupQuestionniare(this);">Setup Password Questionniares</div><?php
                                }?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div id="main_div_body_profile_right_container" class="main-div-body-profile-right-container"></div>   
                    </td>
                </tr>
            </table>
        </div>
        <?php
        $load_headers::Load_Footer();?>
    </div><?php
}
function SubmitQuestionniaresanswers()
{
    global $db, $load_headers; //PDOUpdate($thistable=null, $thisdata = null, $thiswhere = null)
    
    $thistable = "employee_master";
    $thisdata = array('question1' => $_POST['sltquestion1'], 'question2' => $_POST['sltquestion2'], 'question3' => $_POST['sltquestion3'], 
                      'answer1' => $load_headers->Hash_Me_Questionniare_Answers($_POST['txtanswer1']), 
                      'answer2' => $load_headers->Hash_Me_Questionniare_Answers($_POST['txtanswer2']), 
                      'answer3' => $load_headers->Hash_Me_Questionniare_Answers($_POST['txtanswer3']));
    $thiswhere = array('recno' => $_SESSION['employee_master_recno']);
    $rows = $db->PDOUpdate($thistable, $thisdata, $thiswhere, $_SESSION['employee_master_recno']);
    if(!isset($rows))
    {
        echo "Failed";
    }
    else
    {
        echo "Success";
    }
    
}
function EnableAuthentication()
{
    global $db; //PDOUpdate($thistable=null, $thisdata = null, $thiswhere = null)
    $temptext = $_POST['btntext']; //Enabled or Disabled
    $thistable = "employee_master";
    $istemptext = false;
    if($temptext == "Disabled")
    {
        $istemptext = true;
    }
    $thisdata = array('isauthenticated' => $istemptext);
    $thiswhere = array('recno' => $_SESSION['employee_master_recno']);
    $rows = $db->PDOUpdate($thistable, $thisdata, $thiswhere, $_SESSION['employee_master_recno']);
    if($temptext == "Disabled")
    {
        echo "Enabled";
    }
    else
    {
        echo "Disabled";
    }
}
function ValidatePassword()
{
    global $db, $load_headers;
    $thisfields = Array("password");
    $thistable = "employee_master";
    $getpasssword = $load_headers -> Hash_Me_Password($_POST['txtpassword']); //we hash user's entered pw.     
    $thiswhere = array("recno" => $_SESSION['employee_master_recno'], 'password' => $getpasssword);
    $result = $db->PDOQuery($thistable, $thisfields, $thiswhere);
    if(!isset($result))
    {
        echo "Failed";
    }
    else
    {
        echo "Success";
    }
}
function SubmitNewpassword()
{
    global $db, $load_headers; //PDOUpdate($thistable=null, $thisdata = null, $thiswhere = null)
    $thistable = "employee_master";
    $getpasssword = $load_headers -> Hash_Me_Password($_POST['txtpassword']); //we hash user's entered pw.
    $thisdata = array('password' => $getpasssword);
    $thiswhere = array('recno' => $_SESSION['employee_master_recno']);
    $rows = $db->PDOUpdate($thistable, $thisdata, $thiswhere, $_SESSION['employee_master_recno']);
    if(!isset($rows))
    {
        echo "Failed";
    }
    else
    {
        echo "Success";
    }
}
function GetUserprofile()
{
    global $db; //$thistable=null, $thisfields=null, $thiswhere=null, $thisorderby=null, $thisgroupby=null, $ordering=null)
    
    $thistable = "employee_master";
    $thisfields = array('recno', 'firstname', 'middlename', 'lastname', 'birthday', 'address', 'city', 'state', 'zipcode', 'login', 'email');
    $thiswhere = array('recno' => $_SESSION['employee_master_recno']);    
    $rows = $db->PDOQuery($thistable, $thisfields, $thiswhere);?>
    <table class="tbl-profile">
        <?php
        foreach($rows as $rs)
        {?>
            <tr><td class="user-profile-lbl tbl-profile-lbl">First Name:</td><td><input class="user-profile-input" type="text" id="txtfirstname" name="txtfirstname" value="<?= $rs['firstname'] ?>" readonly="readonly" /></td></tr>
            <tr><td class="user-profile-lbl tbl-profile-lbl">Middle Name:</td><td><input class="user-profile-input" type="text" id="txtfirstname" name="txtfirstname" value="<?= $rs['middlename'] ?>" readonly="readonly" /></td></tr>
            <tr><td class="user-profile-lbl tbl-profile-lbl">Last Name:</td><td><input class="user-profile-input" type="text" id="txtfirstname" name="txtfirstname" value="<?= $rs['lastname'] ?>" readonly="readonly" /></td></tr>
            <tr><td class="user-profile-lbl tbl-profile-lbl">Birthday:</td><td><input class="user-profile-input" type="text" id="txtfirstname" name="txtfirstname" value="<?= $rs['birthday'] ?>" readonly="readonly" /></td></tr>
            <tr><td class="user-profile-lbl tbl-profile-lbl">Address:</td><td><input class="user-profile-input" type="text" id="txtfirstname" name="txtfirstname" value="<?= $rs['address'] ?>" readonly="readonly" /></td></tr>
            <tr><td class="user-profile-lbl tbl-profile-lbl">City:</td><td><input class="user-profile-input" type="text" id="txtfirstname" name="txtfirstname" value="<?= $rs['city'] ?>" readonly="readonly" /></td></tr>
            <tr><td class="user-profile-lbl tbl-profile-lbl">State:</td><td><input class="user-profile-input" type="text" id="txtfirstname" name="txtfirstname" value="<?= $rs['state'] ?>" readonly="readonly" /></td></tr>
            <tr><td class="user-profile-lbl tbl-profile-lbl">Zip-Code:</td><td><input class="user-profile-input" type="text" id="txtfirstname" name="txtfirstname" value="<?= $rs['zipcode'] ?>" readonly="readonly"/></td></tr>
            <tr><td class="user-profile-lbl tbl-profile-lbl">Email:</td><td><input class="user-profile-input" type="text" id="txtfirstname" name="txtfirstname" value="<?= $rs['email'] ?>" readonly="readonly"/></td></tr><?php 
        }?>
    </table><?php
}
function ChangePassword()
{?>
    <table id="tblpassword" name="tblpassword" class="tbl-profile">
        <tr><td class="user-profile-lbl tbl-profile-lbl">Old Password:</td><td><input class="user-profile-input" type="password" id="txtpassword" name="txtpassword" onchange="validatePassword(this);" onclick="clearOldpassword();" value="**********" /></td></tr>
        <tr><td class="user-profile-lbl tbl-profile-lbl">New Password:</td><td><input class="user-profile-input" type="password" id="txtnewpassword" name="txtnewpassword" onchange="validatePassword(this);" value=""/></td></tr>
        <tr><td class="user-profile-lbl tbl-profile-lbl">Confirm New Password:</td><td><input class="user-profile-input" type="password" id="txtconfirmnewpassword" name="txtconfirmnewpassword" onchange="validatePassword(this);" value=""/></td></tr>
        <tr><td class="tbl-profile-lbl" colspan="2" style="width: 100%; text-align: center;">
            <button type="button" onclick="submitNewpassword();">Submit</button>
            <button type="button" onclick="clearForm();">Clear</button>
        </tr>    
    </table><?php
}
function SetupAuthentication()
{
    global $db; //$thistable=null, $thisfields=null, $thiswhere=null, $thisorderby=null, $thisgroupby=null, $ordering=null)
    $bgcolor = "";
    $thisstatus = "";
    $thistitle = "";
    $thistable = "employee_master";
    $thisfields = array('email', 'isauthenticated');
    $thiswhere = array('recno' => $_SESSION['employee_master_recno']);
    
    $rows = $db->PDOQuery($thistable, $thisfields, $thiswhere);?>
    <table class="tbl-profile"><?php
        foreach($rows as $rs)
        {
            if($rs['isauthenticated'] == true)
            {
                $bgcolor = "#288331;";
                $thisstatus = "Enabled";
                $thistitle = "Click to disable.";
            }
            else
            {
                $bgcolor = "";
                $thisstatus = "Disabled";
                $thistitle = "Click to enable.";
            }?>  
            <tr><td class="user-profile-lbl tbl-profile-lbl">Email:</td><td><input class="user-profile-input" type="text" id="txtemail" name="txtemail" value="<?= $rs['email'] ?>" readonly="readonly"/></td></tr>
            <tr><td class="user-profile-lbl tbl-profile-lbl">Two Steps Authentication:</td><td><button type="button" name="btnauthentication" id="btnauthentication" title="<?= $thistitle ?>" style=" float: left; background-color: <?= $bgcolor ?>;" onclick="enableAuthentication();"><?= $thisstatus ?></button></td></tr><?php 
        }?>
    </table><?php
}
function SetupQuestionniare()
{
    global $db;

    $sql = "SELECT qn.recno, qn.question FROM employee_master em INNER JOIN questionniares qn ON em.question1 = qn.recno OR em.question2 = qn.recno OR em.question3 = qn.recno   
            WHERE em.recno = ".$_SESSION['employee_master_recno']." ORDER BY qn.question";
    $rows = $db->PDOMiniquery($sql);
    $temparray = Array();
                                      
    if($rows->rowCount() > 0)
    {
        foreach($rows as $rs)
        {
            if(!is_null($rs['question']))
            {
                $temparray[] = $rs['recno'];
            }
        }
        NewQuestionniares($temparray);
    }
    else
    {
        NewQuestionniares();
    }
}
function NewQuestionniares($datarows=[])
{
    global $db; 
    $isdisabled = "";
    if(count($datarows) > 0)
    {
        $isdisabled = 'disabled="disabled"';
    }
    //$datarows is a single array
    $thistable = "questionniares";
    $thisfields = array('recno', 'question');
    $thisorderby = array("question");
    //function PDOQuery($thistable=null, $thisfields=null, $thiswhere=null, $thisorderby=null, $thisgroupby=null, $ordering=null, $ons=null)
    $rows = $db->PDOQuery($thistable, $thisfields, null, $thisorderby);?>
    <table id="tblquestionniare" class="tbl-profile"><?php
        $j=0;
        for($i=1; $i<4; $i++)
        {?>
            <tr>
                <td class="user-profile-lbl tbl-profile-lbl">Question <?= $i ?>:</td>
                <td>
                    <select class="user-profile-input required" id="sltquestion<?= $i ?>" name="sltquestion<?= $i ?>" onchange="validatQuestions(this);" <?=$isdisabled?>>
                        <option value="Select">-Select a question from list-</option><?php
                            foreach($rows as $rs)
                            {?>
                                <option value="<?= $rs['recno'] ?>" <?php
                                if(count($datarows) > 0)
                                {
                                    if($datarows[$j] == $rs['recno'])
                                    {?>
                                        selected<?php
                                    }
                                }?>
                                ><?= $rs['question']?></option><?php
                            }
                            $j++;?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="user-profile-lbl tbl-profile-lbl">Answer <?= $i ?>:</td>
                <td><?php
                    if(count($datarows) == 0)
                    {?>
                        <input type="text" class="user-profile-input required" id="txtanswer<?= $i ?>" name="txtanswer<?= $i ?>" value="" onchange="validateAnswers(this);"><?php
                    }
                    else
                    {?>
                        <input type="text" class="user-profile-input required" id="txtanswer<?= $i ?>" name="txtanswer<?= $i ?>" value="**********" onchange="validateAnswers(this);" onclick="emptyInputtext(this);" disabled="disabled"><?php
                    }?>
                </td>
            </tr><?php 
        }
        if(count($datarows) == 0)
        {?>
            <tr><td colspan="2" style="width: 100%; text-align: center;">
                <button id="btnsubmitquestions" type="button" onclick="submitQuestionniaresanswers();">Submit</button>
                <button id="btnclearquestions" type="button" onclick="clearAnswerform();">Clear</button>
                <button style="display: none;" type="button"  name="btneditquestionanswer"  id="btneditquestionanswer" onclick="editQuestionniaresanswers(this);">Click To Edit</button>
                <button style="display: none;" type="button" name="btnsubmitquestionanswer" id="btnsubmitquestionanswer" onclick="submitQuestionniaresanswers(this);">Submit</button>
            </tr><?php
        }
        else
        {?>
           <tr><td class="tbl-profile-lbl" colspan="2" style="width: 100%; text-align: center;">
                <button type="button"  name="btneditquestionanswer"  id="btneditquestionanswer" onclick="editQuestionniaresanswers(this);">Click To Edit</button>
                <button style="display: none;" type="button" name="btnsubmitquestionanswer" id="btnsubmitquestionanswer" onclick="submitQuestionniaresanswers(this);">Submit</button>
            </tr><?php 
        }?>
    </table><?php
}?>