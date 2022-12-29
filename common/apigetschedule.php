<?php
    echo getAPIschedule();
    function getAPIschedule()
    {
        require_once("./page.php");
        require_once("./pdocon.php");
        /*Flight Aware only allows for 2 future days schedule search.  Can probably get farther out dates but will cost more
         */
        $apiKey = "gAQm6Gz1h4nNN23sO7nKitfq1OMczW53";
        $url = "https://aeroapi.flightaware.com/aeroapi/";

        $db = new PDOCON();
        $nextstartmonth = date('Y-m-d');
        $nextendmonth = date('Y-m-d', strtotime('+21 days')); 
        /*
        $sql = "SELECT recno, customer, icaocode FROM customer_master WHERE isactive = true";
        $result = $db->PDOMiniquery($sql);
        $custarray = [];
        if($result)
        {
            foreach($result as $rs)
            {
                $custarray[] = $rs['icaocode']; //Should have at least 1
            }
        }*/
        $thiscustomer = "Qantas Airways Limited";
        $thisicao = "QFA";
        //We will start to get the last availabler allowable date schedule, for now 2 days in advance, that way we
        //do not get the data we already got which would be today and tomorrow.
        $queryParams = array(
            'origin' => 'KLAX',
            'airline' => 'QFA',
            'max_pages' => 1
        );
        //curl -X GET "https://aeroapi.flightaware.com/aeroapi/schedules/2022-12-29/2023-01-19?origin=KLAX&airline=QFA&max_pages=2"

        $url = "https://aeroapi.flightaware.com/aeroapi/schedules/$nextstartmonth/$nextendmonth?".http_build_query($queryParams);
        //echo $url;
        //return(false);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
        "Accept: application/json; charset=UTF-8",
        "x-apikey: $apiKey",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        //echo $resp;
        curl_close($curl);

        $thisjson = json_decode($resp, true);
        $bigarrayschedule = [];
        $bigarrayflow = [];
        $temparray = [];
        $i=0;
        $thistable = "flight_schedule";
        foreach($thisjson['scheduled'] as $val)
        {
            if(is_array($val))
            {
                $sqlqry = "SELECT recno FROM $thistable WHERE customer = 'Qantas Airways Limited' AND flightnumber= '".substr($thisjson['scheduled'][$i]['ident'], 3)."'";
                //echo $sqlqry;
                $resultqry = $db->PDOMiniquery($sqlqry);
                if($db->PDORowcount($resultqry) == 0)
                {
                    $sqlins = "INSERT INTO $thistable (customer, actype, flightnumber, fromstation, schedulearrival, tostation, scheduledeparture, dates) VALUES(";
                    $sqlins .= "'Qantas Airways Limited', ";
                    $sqlins .= "'".$thisjson['scheduled'][$i]['aircraft_type']."', ";
                    $sqlins .= "'".substr($thisjson['scheduled'][$i]['ident'], 3)."', ";
                    $sqlins .= "'".$thisjson['scheduled'][$i]['origin']."', ";
                    $sqlins .= "'".date('H:i:s', strtotime($thisjson['scheduled'][$i]['scheduled_in']))."', ";
                    $sqlins .= "'".$thisjson['scheduled'][$i]['destination']."', ";
                    $sqlins .= "'".date('H:i:s', strtotime($thisjson['scheduled'][$i]['scheduled_out']))."', ";
                    $sqlins .= "'".date('Y-m-d', strtotime($thisjson['scheduled'][$i]['scheduled_in']))."')";
                    echo $sqlins.'<br>';
                    $db->PDOMiniquery($sqlins);
                }
                else
                {
                    foreach($resultqry as $rs)
                    {
                        $sqlupdate = "UPDATE $thistable SET dates = CONCAT_WS(',', dates, '".date('Y-m-d', strtotime($thisjson['scheduled'][$i]['scheduled_in']))."')";
                        $db->PDOMiniquery($sqlupdate);
                    }
                }
                /*
                $temparray['customer'] = "Qantas Airways Limited";
                $thisident = $thisjson['scheduled'][$i]['ident'];
                $temparray['flightnumber'] = substr($thisident, 3);
                $temparray['actype'] =  $thisjson['scheduled'][$i]['aircraft_type'];
                $temparray['dates'] = date('m/d/Y', strtotime($thisjson['scheduled'][$i]['scheduled_in']));            //2022-12-27T13:55:00Z
                $temparray['fromstation'] =  $thisjson['scheduled'][$i]['origin'];
                $temparray['scheduledepature'] = date('H:i:s', strtotime($thisjson['scheduled'][$i]['scheduled_out']));                
                $temparray['tostation'] =  $thisjson['scheduled'][$i]['destination'];
                $temparray['schedulearrival'] = date('H:i:s', strtotime($thisjson['scheduled'][$i]['scheduled_in']));
                $bigarrayschedule[] = $temparray;*/
            }
            $i++;
        }
        
        
        //Now that we have an array with our data, we will now iterate through it and update/insert
        

        /*
        foreach($bigarrayschedule as $key => $val)
        {
            $thisdata = $val;

            foreach($val as $key2 => $val2)
            {
                echo $key2." => ".$val2."<br>";
            }
            echo "<br>";

        }*/
    }
    
?>
    

