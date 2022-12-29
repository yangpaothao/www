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
        $thisday = date('m/d/Y', strtotime('+1 day'));
        $thistomorrow = date('m/d/Y', strtotime('+2 day'));
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

            'start' => date('Y-m-d', strtotime($thistomorrow)),
            'end' => date('Y-m-d', strtotime($thistomorrow))
        );
        //At this point we should get an array = ['flight1', 'flight2',...,'flightn'];

        //$thisflight = 'CPA520';
        //$url = $url . 'flights/' . $thisflight . '?' . http_build_query($queryParams);


        //$url = $url . 'SearchBirdseyeInFlight/' .$explodeident.'?'. http_build_query($queryParams);

        $url = "https://aeroapi.flightaware.com/aeroapi/operators/$thisicao/flights?". http_build_query($queryParams);
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
        //$resp = curl_exec($curl);
        curl_close($curl);
        $tempjson = '{
            "scheduled": [
              {
                "ident": "QFA940",
                "ident_icao": "QFA940",
                "ident_iata": "QF940",
                "fa_flight_id": "QFA940-1671960960-schedule-0647",
                "operator": "QFA",
                "operator_icao": "QFA",
                "operator_iata": "QF",
                "flight_number": "940",
                "registration": null,
                "atc_ident": null,
                "inbound_fa_flight_id": null,
                "codeshares": [
                  "ANZ7372",
                  "FJI5275",
                  "UAE5652"
                ],
                "codeshares_iata": [
                  "NZ7372",
                  "FJ5275",
                  "EK5652"
                ],
                "blocked": false,
                "diverted": false,
                "cancelled": true,
                "position_only": false,
                "origin": {
                  "code": "YPPH",
                  "code_icao": "YPPH",
                  "code_iata": "PER",
                  "code_lid": null,
                  "timezone": "Australia/Perth",
                  "name": "Perth Int",
                  "city": "Perth",
                  "airport_info_url": "/airports/YPPH"
                },
                "destination": {
                  "code": "YBBN",
                  "code_icao": "YBBN",
                  "code_iata": "BNE",
                  "code_lid": null,
                  "timezone": "Australia/Brisbane",
                  "name": "Brisbane",
                  "city": "Brisbane",
                  "airport_info_url": "/airports/YBBN"
                },
                "departure_delay": 3000,
                "arrival_delay": 3000,
                "filed_ete": 15000,
                "scheduled_out": "2022-12-27T09:25:00Z",
                "estimated_out": "2022-12-27T10:15:00Z",
                "actual_out": null,
                "scheduled_off": "2022-12-27T09:35:00Z",
                "estimated_off": "2022-12-27T10:25:00Z",
                "actual_off": null,
                "scheduled_on": "2022-12-27T13:45:00Z",
                "estimated_on": null,
                "actual_on": null,
                "scheduled_in": "2022-12-27T13:55:00Z",
                "estimated_in": "2022-12-27T14:45:00Z",
                "actual_in": null,
                "progress_percent": 100,
                "status": "Cancelled",
                "aircraft_type": "B738 ",
                "route_distance": 2244,
                "filed_airspeed": 469,
                "filed_altitude": null,
                "route": null,
                "baggage_claim": "2",
                "seats_cabin_business": 12,
                "seats_cabin_coach": 162,
                "seats_cabin_first": 0,
                "gate_origin": "14",
                "gate_destination": null,
                "terminal_origin": "4",
                "terminal_destination": "D",
                "type": "Airline"
              }
            ]
          }';
        //$thisjson = json_decode($resp);
        $thisjson = json_decode($tempjson, true);
        $bigarrayschedule = [];
        $bigarrayflow = [];
        $i=0;
        foreach($thisjson['scheduled'] as $val)
        {
            if(is_array($val))
            {
                $temparray['customer'] = "Qantas Airways Limited";
                $temparray['flightnumber'] =  $thisjson['scheduled'][0]['flight_number'];
                $temparray['actype'] =  $thisjson['scheduled'][0]['aircraft_type'];
                $temparray['dates'] = date('m/d/Y', strtotime($thisjson['scheduled'][0]['scheduled_in']));            //2022-12-27T13:55:00Z
                $temparray['fromstation'] =  $thisjson['scheduled'][0]['origin']['code_icao'];
                $temparray['schedulearrival'] = date('H:i:s', strtotime($thisjson['scheduled'][0]['scheduled_in']));
                $temparray['tostation'] =  $thisjson['scheduled'][0]['destination']['code_icao'];
                $temparray['scheduledepature'] = date('H:i:s', strtotime($thisjson['scheduled'][0]['scheduled_out']));
                $bigarrayschedule[] = $temparray;

                $temparray['estimatearrival'] = date('H:i:s', strtotime($thisjson['scheduled'][0]['estimated_in']));
                $temparray['estimatedeparture'] = date('H:i:s', strtotime($thisjson['scheduled'][0]['estimated_out']));
                $temparray['status'] =  $thisjson['scheduled'][0]['status'];
                $bigarrayflow[] = $temparray;
            }
            $i++;
        }
        //Now that we have an array with our data, we will now iterate through it and update/insert

        foreach($bigarrayschedule as $key => $val)
        {
            $thistable = "flight_schedule";
            /*
            foreach($val as $key2 => $val2)
            {
                echo $key2." => ".$val2."<br>";
            }
            echo "<br>";
            */
             
        }
        foreach($bigarrayflow as $key => $val)
        {
            
            /*
            foreach($val as $key2 => $val2)
            {
                echo $key2." => ".$val2."<br>";
            }
            echo "<br>";
            */
        }
    }
    
?>
    

