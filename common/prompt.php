<?php
class PROMPT 
{
    //PDOQuery($thistable=null, $thisfields=null, $thiswhere=null, $thisorderby=null, $thisgroupby=null, $ordering=null, $ons=null, $distinct=null)
    private $db = null;
    private $thisarray = [];
    function __construct() 
    {
        $this->db = $this->GetDBcon();
    }
    private function GetDBcon()
    {
        return new PDOCON();  //Return a connection
    }
    function SltEngineer()
    {
        $this->thisarray = [];
        $thistable = "employee_master";
        $thisfields = array("recno", "firstname", "middlename", "lastname");
        $thiswheres = array("isengineer" => true, "isactive" => true);
        //->PDOQuery($thistable, $thisfields, $thiswhere);
        $result = $this->db->PDOQuery($thistable, $thisfields, $thiswheres);
        $tempname = "";
        if(!is_null($result))
        {
            foreach($result as $rs)
            {
                $tempname = $rs['firstname'];
                if(!is_null($rs['middlename']))
                {
                    $tempname .= " ".substr($rs['middlename'], 0, 1).".";
                }
                $tempname .= " ".$rs['lastname']; 
                //file_put_contents("./dodebug/debug.txt", 'recno: '.$rs['recno'].' AND name: '.$tempname, FILE_APPEND);
                $this->thisarray[$rs['recno']]= $tempname;
                //$this->thisarray is now a multi array
            }
        }
        return($this);
    }
    function SltCustomer($thisvalue=null)
    {
        //if $thisvaslue has no parameter coming in then we assume default to recno as the KEY, ex key = value
        //and if there is a parameter, it has to be a field that user wants to use as value.    //value = value,
        
        $this->thisarray = [];
        $thistable = "customer_master";
        $thisfields = array("recno", "customer");
        $thiswheres = array("iscargo" => true, "isactive" => true);
        //->PDOQuery($thistable, $thisfields, $thiswhere);
        $result = $this->db->PDOQuery($thistable, $thisfields, $thiswheres, array('customer'), null, null, null, 'DISTINCT');
        $tempname = "";
        $tempkey = $thisvalue;
        if(!is_null($result))
        {
            foreach($result as $rs)
            {
                $tempname = $rs['customer'];
                if(is_null($thisvalue))
                {
                    $tempkey = 'recno';
                }
                //file_put_contents("./dodebug/debug.txt", 'recno: '.$rs['recno'].' AND name: '.$tempname, FILE_APPEND);
                $this->thisarray[$rs[$tempkey]]= $tempname;
                //$this->thisarray is now a multi array
            }
        }
        return($this);
    }
    function SltAircraft($thisvalue=null)
    {
        $this->thisarray = [];
        $thistable = "aircraft";
        $thisfields = array("recno", "name", 'actype');
        $thiswheres = array("isdeleted" => false);
        //->PDOQuery($thistable, $thisfields, $thiswhere);
        $result = $this->db->PDOQuery($thistable, $thisfields, $thiswheres, array('name'), null, null, null, 'DISTINCT');
        $tempname = "";
        $tempkey = $thisvalue;
        if(!is_null($result))
        {
            foreach($result as $rs)
            {
                $tempname = $rs['name'];
                if(is_null($thisvalue))
                {
                    $tempkey = 'recno';
                }
                //file_put_contents("./dodebug/debug.txt", 'recno: '.$rs['recno'].' AND name: '.$tempname, FILE_APPEND);
                $this->thisarray[$rs[$tempkey]]= $tempname;
                //$this->thisarray is now a multi array
            }
        }
        return($this);
    }
    function JSONEncode($data)
    {
        return(json_encode($data, true));
    }
    function JSONDecode($data)
    {
        return(json_decode($data));
    }
    function GetSelect($thisid, $thisdefault, $isrequired, $ismultiple, $thisonchange="", $isdisable=false, $isdummy = false)
    {
        //string - $thisid - will be the id of this element
        //      if it has more than 1 item in this array, that means we want to combined the values into one string, firstg ex: string1 + string2 + string3 = string1string2string3.
        //      if there is only 1 item in this array, that means we will juse ust the value of this field as is for the text.
        //string - $thisdefault - is if there is a default select that user wants, it can be a string fo something or "", empty.
        //boolen - $isrequired - if this feild is required.
        //string - $thisonchange - the onchange function in format of nameoffunction(parameter1, parameter2,......,paramtern)
        //$isdummy - will include the default select
        
        $tempquired = "";
        $temponchange = "";
        $tempmultiple = "";
        $tempdisabled = "";
        $tempdefault = array();
        if($ismultiple == true)
        {
            $tempmultiple = "multiple='multiple'";
        }
        if($thisonchange != "")
        {
            $temponchange = "onchange='$thisonchange'";
        }
        if($isrequired == true)
        {
            $tempquired = 'required';
        }
        if($thisdefault != "")
        {
            $tempdefault = explode(',', $thisdefault);
        }
        if($isdisable == true)
        {
            $tempdisabled = 'disabled';
        }?>
        <select class="promp-select2 <?=$tempquired?>" style="width: 100%; height: 100%; white-space: nowrap;" id="<?=$thisid?>" name="<?=$thisid?>" <?=$temponchange?> <?=$tempmultiple?> <?=$tempdisabled?>><?php
            if($isdummy == true)
            {?>
                <option value="Select">Select</option><?php
            }
            foreach($this->thisarray as $key => $value)
            {
                $tempselect = "";
                if(in_array($key, $tempdefault))
                {
                    $tempselect = "selected";
                }?>
                <option value="<?=$key?>" <?=$tempselect?>><?=$value?></option><?php
            }?>
        </select><?php
    }
    function GetString($thisdefault)
    {
        //string - $thisid - will be the id of this element
        //      if it has more than 1 item in this array, that means we want to combined the values into one string, firstg ex: string1 + string2 + string3 = string1string2string3.
        //      if there is only 1 item in this array, that means we will juse ust the value of this field as is for the text.
        //string - $thisdefault - is if there is a default select that user wants, it can be a string fo something or "", empty.
        //boolen - $isrequired - if this feild is required.
        //string - $thisonchange - the onchange function in format of nameoffunction(parameter1, parameter2,......,paramtern)
        $tempdefault = [];
        if($thisdefault != "")
        {
            $tempdefault = explode(',', $thisdefault);
        }
        $tempname = "";
        foreach($this->thisarray as $key => $value)
        {
            $tempselect = "";
            if(in_array($key, $tempdefault))
            {
                if($tempname == "")
                {
                    $tempname = "$value";
                }
                else
                {
                    $tempname .= ", $value";
                }
            }
        }
        echo $tempname;
    }
}?>