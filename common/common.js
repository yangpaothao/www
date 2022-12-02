function getJDate(obj){
    $(obj).datepicker({
        dateFormat: "mm/dd/yy",
        changeMonth: true,
        changeYear: true,
        onClose: function(){
            checkDate(obj);
        }
    }).datepicker("show"); 
}
function checkDate(obj){
    if($(obj).val() == "")
    {
        return(false);
    }
    //thisfrom will be 'Change' or 'Load'
    regexdate = "^[0-9]{2}/[0-9]{2}/[0-9]{4}$";
    if(!$(obj).val().match(regexdate)){
        alert('You have entered an invalide date.  Please check and try again.');
        $(obj).val('');
        $(obj).focus();
        return(false);
    }
}
function validateEmail(obj){
    let regex = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if(!regex.test($(obj).val())){
        alert("Please enter a valid email.");
        $(obj).select();
        return(false);
    }
}
function checkTime(obj){
    thistime = $(obj).val();
    if(thistime.indexOf(':') == -1)
    {
        temptime1 = thistime.substr(0,2);
        temptime2 = thistime.substr(2);
        thistime = temptime1+":"+temptime2;
    }
    var regexp = /^(?:[01][0-9]|2[0-3]):[0-5][0-9](?::[0-5][0-9])?$/;
    if(regexp.test(thistime) == false){
        alert('Please enter a correct time in format of military time 00:00 or 0000 between 00:00 - 23:59.');
        $(obj).val($('body').data($(obj).prop('id')));
        $(obj).focus();
        return(false);
    } 
    else{
        $(obj).val(thistime);
    }
        
}
function saveThisdata(obj){
    $('body').data($(obj).prop('id'), $(obj).val());
}
function isNumbercheck(obj){
    if(!$.isNumeric($(obj).val())){
        alert('Please enter an interger value.');
        $(obj).val();
        return(false);
    }
}

