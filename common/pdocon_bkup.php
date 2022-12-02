<?php
class PDOCON {
    private $conn = null;
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
    function PDOQuery($thistable=null, $thisfields=null, $thiswhere=null, $thisorderby=null, $thisgroupby=null, $ordering=null, $ons=null)
    {
        //0.  string - $thistype, INSERT, UPDATE, DELETE, QUERY
        //1.  string - $thistable, 
        //2.  array - $thisfields, ex: field1, field2,...fieldn OR 'All' for *, 
        //3.  asso array - $wheres, will be an associate array with key and value, key being the fieldname.
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
        $sql = "SELECT $realfields FROM $thistable ";
        //So this is saying, if $thisfields = 'All', then we will use the asterisk '*' otherwise if it is not 'All' and it contains some fields, then we will do the implode.
        if(isset($ons))
        {
            $sql .= "$ons ";
        }
        $sql .= "WHERE 1=1 "; //If array is 1 or null the ', ' delimiter will not be present.
        
        if(isset($thiswhere) > 0)
        {
            //We should have atleast 1 where clause
            foreach($thiswhere as $key => $value)
            {
                $sql .= "AND $key = :$key "; 
            }
        }
        if(isset($thisgroupby))
        {
            $sql .= "ORDER BY '".implode(', ', $thisgroupby)."'"; 
        }
        if(isset($thisorderby))
        {
            $sql .= "ORDER BY ".implode(', ', $thisorderby); 
        }
        //echo $sql;
        //Now we should have an sql that is ready to be prepared
        //$q = $db->prepare("SELECT id FROM table WHERE forename = :forename and surname = :surname");
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
                    $q->bindValue(":$key", $value, PDO::PARAM_STR);
                }
            }
        }
        if(isset($thisgroupby))
        {
            foreach($thisgroupby as $value)
            {
                $q->bindValue("$value", substr($value, 1), PDO::PARAM_STR);
            }
        }
        if(isset($thisorderby))
        {
            foreach($thisorderby as $value)
            {
                $q->bindValue("$value", substr($value, 1), PDO::PARAM_STR);
            }
        }
        $q->execute();
        if ($q->rowCount() > 0){
            return $q->fetchAll(PDO::FETCH_ASSOC);
        }
        else
        {
            return null;
        }
    }
    function PDOInsert($thistable=null, $thisdata=null)
    {
        //0.  string - $thistable will just be the table name
        //1.  associative array - $thisdata will have associative arrayt key and values for the fields and the values,
        
        $sql = "INSERT INTO $thistable (";
        $firsttime = false;
        foreach($thisdata as $key => $value)
        {
            if($firsttime == false)
            {
                $firsttime = true;
                $sql .= "$key";
            }
            else
            {
                $sql .= ", $key";
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
            }
            else
            {
                $sql .= ", :$key";
            }
        }
        $sql .= ")";
        try {
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
            return('Success');
        } catch (Exception $ex) {
            throw $ex;
        }
        
    }
    function PDOUpdate($thistable=null, $thisdata = null, $thiswhere = null)
    {
        //0.  string - $thistype, INSERT, UPDATE, DELETE, QUERY
        //1.  string - $thistable, 
        //2.  array - $thisfields, ex: field1, field2,...fieldn OR 'All' for *, 
        //3.  asso array - $wheres, will be an associate array with key and value, key being the fieldname.
        //4.  array - $thisorderby, will be ORDER BY, 
        //5.  array - $tghisgroupby, GROUP BY and such
        //6.  array - $ordering will be either ASC or DESC, defaulting to ASC
        
        $sql = "UPDATE $thistable SET "; //If array is 1 or null the ', ' delimiter will not be present.
        //So this is saying, if $thisfields = 'All', then we will use the asterisk '*' otherwise if it is not 'All' and it contains some fields, then we will do the implode.

        if(count($thisdata) > 0)
        {
            //We should have atleast 1 where clause
            foreach($thisdata as $key => $value)
            {
                $sql .= "$key = :$key,"; 
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
         try {
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
            $q ->execute();        
            return('Success');
        } catch (Exception $ex) 
        {
            return($ex);
        }
    }
    function PDOMiniquery($sql)
    {
        //echo $sql;
        $q = $this->conn->query($sql, PDO::FETCH_ASSOC);
        return($q);
    }
}

?>
