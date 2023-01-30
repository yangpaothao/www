<?php
class PDOCON {
    private $conn = null;
    private $temp = null;
    function __construct() 
    {
        $temp_host = filter_input(INPUT_SERVER, 'SERVER_NAME');
        if($temp_host == "localhost")
        {
            try{
                //$this -> conn = new PDO("mysql:host='localhost';dbname=myDB", 'qantasislife', '/u2AMmus_iVJZsXa');
                $this -> conn = new PDO("mysql:host=localhost;dbname=qantas", 'yangthao', '@UyvRQ-fYmV--)11', [PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            }
            catch(PDOException $e)
            {
                echo "Connection failed: ".$e->getMessage();
            }
        }
        else
        {
            try{
                //$this -> conn = new PDO("mysql:host='localhost';dbname=myDB", 'qantasislife', '/u2AMmus_iVJZsXa');
                $this -> conn = new PDO("mysql:host=https://www.somehoest.com;dbname=qantas", 'yangthao', '@UyvRQ-fYmV--)11', [PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            }
            catch(PDOException $e)
            {
                echo "Connection failed: ".$e->getMessage();
            }
        }
    }
    function PDOQuery($thistable=null, $thisfields=null, $thiswhere=null, $thisorderby=null, $thisgroupby=null, $ordering=null, $ons=null, $distinct=null)
    {
        //0.  string - $thistype, INSERT, UPDATE, DELETE, QUERY
        //1.  string - $thistable, 
        //2.  array - $thisfields, ex: field1, field2,...fieldn OR 'All' for *, 
        //3.  asso array - $wheres, will be an associate array with key and value, key being the fieldname.
        //      -The key could be in format of 'something' => 'somevalue' OR if this WERE has 'LIKE' operans, it will come with 'Something LIKE' => 'somevalue'
        //      -so we have to check for LIKES to handle LIKES
        //      -NOTE: IF YOU HAVE A OPERAN LIKES, PUT THE LIKES AT THE END OF ARRAY
        //4.  array - $thisorderby, will be ORDER BY, 
        //5.  array - $tghisgroupby, GROUP BY and such
        //6.  array - $ordering will be either ASC or DESC, defaulting to ASC
        //7.  string - $ons is for joins
        $realfields = "";
        if($thisfields[0]=='All')
        {
            $realfields = "*";
        }
        else
        {
            $realfields = implode(', ', $thisfields);
        }
        $sql = "SELECT ";
        if(!is_null($distinct))
        {
            $sql .= "DISTINCT "; //Can be $distinct or can be explicite, doesn't matter.
        }
        $sql .= "$realfields FROM $thistable ";
        //So this is saying, if $thisfields = 'All', then we will use the asterisk '*' otherwise if it is not 'All' and it contains some fields, then we will do the implode.
        if(isset($ons))
        {
            $sql .= "$ons ";
        }
        $sql .= "WHERE 1=1 "; //If array is 1 or null the ', ' delimiter will not be present.
        $isOnce = false;
        if(isset($thiswhere) > 0)
        {
            //We should have atleast 1 where clause
            foreach($thiswhere as $key => $value)
            {
                if(strpos($key, ' LIKE', 0))
                {
                    if($isOnce == false)
                    {
                        $isOnce = true;
                        $sql .= "AND ";
                    }
                    //If $key contgains 'LIKE' ('field LIKE'), we know we are doing a LIKE operans, otherwise, a normal equal comparisons.
                    $likesarray = explode(' ', $key); //$likesarray should now be an array['realkey', 'LIKE'];
                    $sql .= "$key :$likesarray[0] OR ";  //$sql .= "AND $key :$key, in sql reality, be like $sql .= "AND $key LIKE :$field
                }
                else
                {
                    $sql .= "AND $key = :$key "; 
                }
            }
            if($isOnce == true)
            {
                //If we are here, we know we have atlest one 'LIKE' operans, we want to remove the last 'OR' from the sql.
                $sql = substr($sql, 0, -3);
            }
        }
        if(isset($thisorderby))
        {
            $sql .= "ORDER BY ".implode(', ', $thisorderby); 
        }
        if(isset($thisgroupby))
        {
            $sql .= "GROUP BY ".implode(', ', $thisgroupby); 
        }
        /*
        if(isset($thisorderby))
        {
            $sql .= "ORDER BY :".implode(', :', $thisorderby); 
        }
        if(isset($thisgroupby))
        {
            $sql .= "GROUP BY ".implode(', ', $thisgroupby); 
        }*/
        //echo $sql;
        //file_put_contents("./dodebug/debug.txt", $sql, FILE_APPEND);
        //Now we should have an sql that is ready to be prepared
        $q = $this->conn->prepare($sql);
        if(isset($thiswhere))
        {
            //We should have atleast 1 where clause
            foreach($thiswhere as $key => $value)
            {
                if(is_numeric($value) && !is_float($value))
                {
                    $q->bindValue(":$key", $value, PDO::PARAM_INT);
                }
                else if(is_bool($value))
                {
                    $q->bindValue(":$key", $value, PDO::PARAM_BOOL);
                }
                else if(is_null($value) || $value == "")
                {
                    $q->bindValue(":$key", $value, PDO::PARAM_NULL);
                }
                else
                {
                    if(strpos($key, ' LIKE', 0))
                    {
                        $likesarray = explode(' ', $key); //$likesarray should now be an array['realkey', 'LIKE'];
                        $tempval = "%$value%";
                        $q->bindValue(":$likesarray[0]", $tempval, PDO::PARAM_STR);  //$sql .= "AND $key :$key, in sql reality, be like $sql .= "AND $key LIKE :$field
                    }
                    else
                    {
                        $q->bindValue(":$key", $value, PDO::PARAM_STR);
                    }
                }
            }
        }/*
        if(isset($thisorderby))
        {
            foreach($thisorderby as $value)
            {
                $q->bindValue(":$value", $value, PDO::PARAM_STR);
            }
        }
        if(isset($thisgroupby))
        {
            foreach($thisgroupby as $value)
            {
                $q->bindValue(":$value", $value, PDO::PARAM_STR);
            }
        }*/
        $q->execute();
        if ($q->rowCount() > 0){
            return $q->fetchAll(PDO::FETCH_ASSOC);
        }
        else
        {
            return null;
        }
    }
    function PDOInsert($thistable=null, $thisdata=null, $thisrecno = null)
    {
        //0.  string - $thistable will just be the table name
        //1.  associative array - $thisdata will have associative arrayt key and values for the fields and the values,
        $historyfields = "";
        $historyvalues = "";
        $sql = "INSERT INTO $thistable (";
        $firsttime = false;
        foreach($thisdata as $key => $value)
        {
            if($firsttime == false)
            {
                $firsttime = true;
                $sql .= "$key";
                
                $historyfields = $key;
            }
            else
            {
                $sql .= ", $key";
                $historyfields .= ", $key";
            }
        }
        $sql .= ") VALUES(";
        $firsttime = false;
        foreach($thisdata as $key => $value)
        {
            if($firsttime == false)
            {
                $firsttime = true;
                $sql .= ":$key";
                
                $historyvalues = $value;
            }
            else
            {
                $sql .= ", :$key";
                
                $historyvalues .= "; $value";
            }
        }
        $sql .= ")";
        try 
        {
            $q = $this->conn->prepare($sql);
            foreach($thisdata as $key => $value)
            {
                if(is_numeric($value) && !is_float($value))
                {
                    $q->bindValue(":$key", $value, PDO::PARAM_INT);
                }
                else if(is_bool($value))
                {
                    $q->bindValue(":$key", $value, PDO::PARAM_BOOL);
                }
                else if(is_null($value) || $value == "")
                {
                    $q->bindValue(":$key", $value, PDO::PARAM_NULL);
                }
                else
                {
                    $q->bindValue(":$key", $value, PDO::PARAM_STR);
                }
            }
            $q ->execute();
            $insertid = $this->conn->lastInsertId();
            
            $thissql = "INSERT INTO history (tablename, associativerecno, action, field, value, modifiedby) VALUES(:tablename, :associativerecno, :action, :field, :value, :modifiedby)";
            $q = $this->conn->prepare($thissql);
            $q->bindValue(":tablename", "$thistable", PDO::PARAM_STR);
            $q->bindValue(":associativerecno", $insertid, PDO::PARAM_INT);
            $q->bindValue(":action", 'INSERT', PDO::PARAM_STR);
            $q->bindValue(":field", "$historyfields", PDO::PARAM_STR);
            $q->bindValue(":value", "$historyvalues", PDO::PARAM_STR);
            $q->bindValue(":modifiedby", $_SESSION['employee_master_recno'], PDO::PARAM_INT);
            $q ->execute();
            
            if($thistable == "flow")
            {
                //If we are doing INSERTS for table flow, that means we are adding new flights to flow and any flightg will come with a SERVICE ORDER.
                //This is where we also INSERT into SERVICE ORDER table.
                $thissosql = "INSERT INTO service_orders (foreignkey_flow_recno) VALUES(:foreignkey_flow_recno)";
                $q = $this->conn->prepare($thissosql);
                $q->bindValue(":foreignkey_flow_recno", $insertid, PDO::PARAM_INT);
                $q ->execute();
            }
            if($thistable == "signature")
            {
                $thissql = "INSERT INTO history (tablename, associativerecno, action, field, value, modifiedby) VALUES(:tablename, :associativerecno, :action, :field, :value, :modifiedby)";
                $q = $this->conn->prepare($thissql);
                $q->bindValue(":tablename", "$thistable", PDO::PARAM_STR);
                $q->bindValue(":associativerecno", $thisrecno, PDO::PARAM_INT);
                $q->bindValue(":action", 'INSERT', PDO::PARAM_STR);
                $q->bindValue(":field", "$historyfields", PDO::PARAM_STR);
                $q->bindValue(":value", "$historyvalues", PDO::PARAM_STR);
                $q->bindValue(":modifiedby", $_SESSION['employee_master_recno'], PDO::PARAM_INT);
                $q ->execute();
            }
            if($thistable == "signatures")
            {
                return($insertid);
            }
            else
            {
                return('Success');
            }
        } 
        catch (Exception $ex) {
            throw $ex;
        }  
    }
    function PDOInsertup($thistable=null, $thisdata=null, $thisrecno = null)
    {
        //0.  string - $thistable will just be the table name
        //1.  associative array - $thisdata will have associative arrayt key and values for the fields and the values,
        $historyfields = "";
        $historyvalues = "";
        $sql = "INSERT INTO $thistable (";
        $firsttime = false;
        foreach($thisdata as $key => $value)
        {
            if($firsttime == false)
            {
                $firsttime = true;
                $sql .= "$key";
                
                $historyfields = $key;
            }
            else
            {
                $sql .= ", $key";
                $historyfields .= ", $key";
            }
        }
        $sql .= ") VALUES(";
        $firsttime = false;
        foreach($thisdata as $key => $value)
        {
            if($firsttime == false)
            {
                $firsttime = true;
                $sql .= ":$key";
                
                $historyvalues = $value;
            }
            else
            {
                $sql .= ", :$key";
                
                $historyvalues .= "; $value";
            }
        }
        $sql .= ")";
        try 
        {
            $q = $this->conn->prepare($sql);
            foreach($thisdata as $key => $value)
            {
                if(is_numeric($value) && !is_float($value))
                {
                    $q->bindValue(":$key", $value, PDO::PARAM_INT);
                }
                else if(is_bool($value))
                {
                    $q->bindValue(":$key", $value, PDO::PARAM_BOOL);
                }
                else if(is_null($value) || $value == "")
                {
                    $q->bindValue(":$key", $value, PDO::PARAM_NULL);
                }
                else
                {
                    $q->bindValue(":$key", $value, PDO::PARAM_STR);
                }
            }
            $q ->execute();
            $insertid = $this->conn->lastInsertId();
            
            $thissql = "INSERT INTO history (tablename, associativerecno, action, field, value, modifiedby) VALUES(:tablename, :associativerecno, :action, :field, :value, :modifiedby)";
            $q = $this->conn->prepare($thissql);
            $q->bindValue(":tablename", "$thistable", PDO::PARAM_STR);
            $q->bindValue(":associativerecno", $insertid, PDO::PARAM_INT);
            $q->bindValue(":action", 'INSERT', PDO::PARAM_STR);
            $q->bindValue(":field", "$historyfields", PDO::PARAM_STR);
            $q->bindValue(":value", "$historyvalues", PDO::PARAM_STR);
            $q->bindValue(":modifiedby", $_SESSION['employee_master_recno'], PDO::PARAM_INT);
            $q ->execute();
            
            if($thistable == "flow")
            {
                //If we are doing INSERTS for table flow, that means we are adding new flights to flow and any flightg will come with a SERVICE ORDER.
                //This is where we also INSERT into SERVICE ORDER table.
                $thissosql = "INSERT INTO service_orders (foreignkey_flow_recno) VALUES(:foreignkey_flow_recno)";
                $q = $this->conn->prepare($thissosql);
                $q->bindValue(":foreignkey_flow_recno", $insertid, PDO::PARAM_INT);
                $q ->execute();
            }
            if($thistable == "signature")
            {
                $thissql = "INSERT INTO history (tablename, associativerecno, action, field, value, modifiedby) VALUES(:tablename, :associativerecno, :action, :field, :value, :modifiedby)";
                $q = $this->conn->prepare($thissql);
                $q->bindValue(":tablename", "$thistable", PDO::PARAM_STR);
                $q->bindValue(":associativerecno", $thisrecno, PDO::PARAM_INT);
                $q->bindValue(":action", 'INSERT', PDO::PARAM_STR);
                $q->bindValue(":field", "$historyfields", PDO::PARAM_STR);
                $q->bindValue(":value", "$historyvalues", PDO::PARAM_STR);
                $q->bindValue(":modifiedby", $_SESSION['employee_master_recno'], PDO::PARAM_INT);
                $q ->execute();
            }
            if($thistable == "signatures")
            {
                return($insertid);
            }
            else
            {
                return('Success');
            }
        } 
        catch (Exception $ex) {
            throw $ex;
        }  
    }
    function PDOUpdate($thistable=null, $thisdata = null, $thiswhere = null, $thisrecno = null)
    {
        //0.  string - $thistype, INSERT, UPDATE, DELETE, QUERY
        //1.  string - $thistable, 
        //2.  array - $thisfields, ex: field1, field2,...fieldn OR 'All' for *, 
        //3.  asso array - $wheres, will be an associate array with key and value, key being the fieldname.
        //4.  array - $thisorderby, will be ORDER BY, 
        //5.  array - $tghisgroupby, GROUP BY and such
        //6.  array - $ordering will be either ASC or DESC, defaulting to ASC
        $historyfields = "";
        $historyvalues = "";
        $sql = "UPDATE $thistable SET "; //If array is 1 or null the ', ' delimiter will not be present.
        //So this is saying, if $thisfields = 'All', then we will use the asterisk '*' otherwise if it is not 'All' and it contains some fields, then we will do the implode.
        if(count($thisdata) > 0)
        {
            //We should have atleast 1 where clause
            foreach($thisdata as $key => $value)
            {
                $sql .= "$key = :$key,"; 
                if($historyfields == "")
                {
                    $historyfields = $key;
                    $historyvalues = $value;
                }
                else
                {
                    $historyfields .= ", $key";
                    $historyvalues = ", $value";
                }
            }
            //We will need to remove the last char, ',' from the string
            $sql = rtrim($sql, ",");
        }
        $sql .= " WHERE ";
        if(isset($thiswhere))
        {
            //We should have atleast 1 where clause
            foreach($thiswhere as $key => $value)
            {
                $sql .= "$key = :$key AND "; 
            }
            $sql = rtrim($sql, " AND ");
        }
        //file_put_contents("./dodebug/debug.txt", 'update: '.$sql, FILE_APPEND);
         try {
            $q = $this->conn->prepare($sql);
            foreach($thisdata as $key => $value)
            {
                if(is_numeric($value) && !is_float($value))
                {
                    //file_put_contents("./dodebug/debug.txt", 'in num', FILE_APPEND);
                    $q->bindValue(":$key", $value, PDO::PARAM_INT);
                }
                else if(is_bool($value))
                {
                   //file_put_contents("./dodebug/debug.txt", 'in bool', FILE_APPEND);
                    $q->bindValue(":$key", $value, PDO::PARAM_BOOL);
                }
                else if(is_null($value) || $value == "")
                {
                    //file_put_contents("./dodebug/debug.txt", 'in null', FILE_APPEND);
                    $q->bindValue(":$key", $value, PDO::PARAM_NULL);
                }
                else
                {
                    //file_put_contents("./dodebug/debug.txt", 'in string', FILE_APPEND);
                    $q->bindValue(":$key", $value, PDO::PARAM_STR);
                }
            }
            //file_put_contents("./dodebug/debug.txt", 'value?: '.$value, FILE_APPEND);
            foreach($thiswhere as $key => $value)
            {
                if(is_numeric($value) && !is_float($value))
                {
                    $q->bindValue(":$key", $value, PDO::PARAM_INT);
                }
                else if(is_bool($value))
                {
                    $q->bindValue(":$key", $value, PDO::PARAM_BOOL);
                }
                else if(is_null($value) || $value == "")
                {
                    $q->bindValue(":$key", $value, PDO::PARAM_NULL);
                }
                else
                {
                    $q->bindValue(":$key", $value, PDO::PARAM_STR);
                }
            }
            //file_put_contents("./dodebug/debug.txt", 'value: '.$value, FILE_APPEND);
            $q ->execute();   
            if($thisrecno != null)
            {
                $thissql = "INSERT INTO history (tablename, associativerecno, action, field, value, modifiedby) VALUES(:tablename, :associativerecno, :action, :field, :value, :modifiedby)";
                $q = $this->conn->prepare($thissql);
                $q->bindValue(":tablename", "$thistable", PDO::PARAM_STR);
                $q->bindValue(":associativerecno", $thisrecno, PDO::PARAM_INT);
                $q->bindValue(":action", 'UPDATE', PDO::PARAM_STR);
                $q->bindValue(":field", "$historyfields", PDO::PARAM_STR);
                $q->bindValue(":value", "$historyvalues", PDO::PARAM_STR);
                $q->bindValue(":modifiedby", $_SESSION['employee_master_recno'], PDO::PARAM_INT);
                $q ->execute();
            }
            return('Success');
        } 
        catch (Exception $ex) 
        {
            return($ex);
        }
    }
    function PDOMiniquery($sql, $recno=null)
    {
        //echo $sql;
        $q = $this->conn->query($sql, PDO::FETCH_ASSOC);
        return($q);
    }
    function PDOMiniinsert($sql, $recno=null)
    {
        //echo $sql;
        $q = $this->conn->exec($sql);
        return($q);
    }
    function PDORowcount($result)
    {
        $thiscount = $result->rowCount();
        return($thiscount);
    }
}?>
