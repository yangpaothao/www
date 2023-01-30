<?php
    echo getAPIschedule();
    $date1 = "2023-01-04T00:00:00Z";
    $date2 = "2023-01-04T23:59:59Z";
    echo $date1.'--- in else <br>';
    echo $date2.'<br>';    
    getAPIschedulearrival($date1, $date2, $pageno);
    getAPIscheduledeparture($date1, $date2, $pageno);
    function getAPIschedulearrival($startdate, $enddate, $pageno)
    {
        global $db;
        /*Flight Aware only allows for 2 future days schedule search.  Can probably get farther out dates but will cost more
         */
        $apiKey = "gAQm6Gz1h4nNN23sO7nKitfq1OMczW53";
        $url = "https://aeroapi.flightaware.com/aeroapi/";

        $thiscustomer = "Qantas Airways Limited";
        $thisicao = "QFA";
        //We will start to get the last availabler allowable date schedule, for now 2 days in advance, that way we
        //do not get the data we already got which would be today and tomorrow.
        $queryParams = array(
            'origin' => 'KLAX',
            'airline' => 'QFA',
            'max_pages' => $pageno
        );
        //curl -X GET "https://aeroapi.flightaware.com/aeroapi/schedules/2022-12-29/2023-01-19?origin=KLAX&airline=QFA&max_pages=2"

        $url = "https://aeroapi.flightaware.com/aeroapi/schedules/$startdate/$enddate?".http_build_query($queryParams);
        echo $url."<br>";
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
        echo $resp;
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
                $sqlqry = "SELECT recno, dates FROM $thistable WHERE customer = 'Qantas Airways Limited' AND flightnumber= '".substr($thisjson['scheduled'][$i]['ident'], 3)."' ";
                $sqlqry .= "AND fromstation = '".$thisjson['scheduled'][$i]['origin']."'";
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
                    $sqlins .= "'".date('Y/m/d', strtotime($thisjson['scheduled'][$i]['scheduled_in']))."')";
                    //echo $sqlins.'<br>';
                    $db->PDOMiniquery($sqlins);
                }
                else
                {
                    foreach($resultqry as $rs)
                    {
                        $sqlupdate = "UPDATE $thistable SET dates = CONCAT_WS(',', dates, '".date('Y/m/d', strtotime($thisjson['scheduled'][$i]['scheduled_in']))."')";
                        $sqlupdate .= "WHERE recno = ".$rs['recno']." AND dates NOT LIKE '%".date('Y/m/d', strtotime($thisjson['scheduled'][$i]['scheduled_in']))."%'";
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
                $bigarrayschedule[] = $temparray;
                 */
            }
            $i++;
        }
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
    function getAPIscheduledeparture($startdate, $enddate, $pageno)
    {
        global $db;
        /*Flight Aware only allows for 2 future days schedule search.  Can probably get farther out dates but will cost more
         */
        $apiKey = "gAQm6Gz1h4nNN23sO7nKitfq1OMczW53";
        $url = "https://aeroapi.flightaware.com/aeroapi/";

        $thiscustomer = "Qantas Airways Limited";
        $thisicao = "QFA";
        //We will start to get the last availabler allowable date schedule, for now 2 days in advance, that way we
        //do not get the data we already got which would be today and tomorrow.
        $queryParams = array(
            'origin' => 'KLAX',
            'airline' => 'QFA',
            'max_pages' => $pageno
        );
        //curl -X GET "https://aeroapi.flightaware.com/aeroapi/schedules/2022-12-29/2023-01-19?origin=KLAX&airline=QFA&max_pages=2"

        $url = "https://aeroapi.flightaware.com/aeroapi/schedules/$startdate/$enddate?".http_build_query($queryParams);
        echo $url."<br>";
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
        echo $resp;
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
                $sqlqry = "SELECT recno, dates FROM $thistable WHERE customer = 'Qantas Airways Limited' AND flightnumber= '".substr($thisjson['scheduled'][$i]['ident'], 3)."' ";
                $sqlqry .= "AND fromstation = '".$thisjson['scheduled'][$i]['origin']."'";
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
                    $sqlins .= "'".date('Y/m/d', strtotime($thisjson['scheduled'][$i]['scheduled_in']))."')";
                    //echo $sqlins.'<br>';
                    $db->PDOMiniquery($sqlins);
                }
                else
                {
                    foreach($resultqry as $rs)
                    {
                        $sqlupdate = "UPDATE $thistable SET dates = CONCAT_WS(',', dates, '".date('Y/m/d', strtotime($thisjson['scheduled'][$i]['scheduled_in']))."')";
                        $sqlupdate .= "WHERE recno = ".$rs['recno']." AND dates NOT LIKE '%".date('Y/m/d', strtotime($thisjson['scheduled'][$i]['scheduled_in']))."%'";
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
                $bigarrayschedule[] = $temparray;
                 */
            }
            $i++;
        }
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
    

