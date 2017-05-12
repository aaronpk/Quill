<?php
chdir(dirname(__FILE__).'/..');
require 'vendor/autoload.php';

$flights = ORM::for_table('flights')
  ->where('active', 1)
  ->find_many();
foreach($flights as $flight) {

  $user = ORM::for_table('users')
    ->where('id', $flight->user_id)
    ->where_not_equal('flightaware_apikey', '')
    ->find_one();
  if($user) {
    echo date('Y-m-d H:i:s')."\n";
    echo "Processing flight ".$flight->flight." for ".$user->url."\n";

    $ch = curl_init('http://flightxml.flightaware.com/json/FlightXML2/InFlightInfo?ident='.$flight->flight);
    curl_setopt($ch, CURLOPT_USERPWD, $user->flightaware_username.':'.$user->flightaware_apikey);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $json = curl_exec($ch);
    $data = json_decode($json, true);
    #$data = json_decode($flight->lastposition, true);
    $flightData = $data['InFlightInfoResult'];

    $flight->lastposition = $json;
    $flight->save();

    if($flightData['departureTime']) {

      if($flightData['departureTime'] < strtotime($flight->date_created)) {
        echo "This flight departed before the checkin was made so this is probably the wrong flight\n";
      } else {

        $has_new_location = false;
        $flight_ended = false;

        // Add this point to the list
        if($flight->positions)
          $positions = json_decode($flight->positions, true);
        else
          $positions = [];

        if($flightData['latitude']) {
          $positions[] = [
            'date' => date('Y-m-d H:i:s'), 
            'lat' => $flightData['latitude'], 
            'lng' => $flightData['longitude'],
            'altitude' => $flightData['altitude'],
            'heading' => $flightData['heading'],
            'speed' => $flightData['groundspeed'],
          ];
          $flight->positions = json_encode($positions);

          $has_new_location = true;
        }

        if($has_new_location) {
          $latitude = $flightData['latitude'];
          $longitude = $flightData['longitude'];
        } else {
          $latitude = $positions[count($positions)-1]['lat'];
          $longitude = $positions[count($positions)-1]['lng'];
        }

        if($flightData['arrivalTime']) {
          $flight->arrival_time = date('Y-m-d H:i:s', $flightData['arrivalTime']);
          $flight->active = 0;
          $flight_ended = true;
        }

        if($flight_ended || $has_new_location) {

          $flight->departure_time = date('Y-m-d H:i:s', $flightData['departureTime']);
          $flight->save();

          $checkin = [
            'type' => ['h-card'],
            'properties' => [
              'name' => [$flight->flight],
              'url' => ['http://flightaware.com/live/flight/'.$flight->flight],
              'latitude' => [$latitude],
              'longitude' => [$longitude],
            ]
          ];

          // Geocode the location
          $geocode = json_decode(file_get_contents('https://atlas.p3k.io/api/geocode?latitude='.$latitude.'&longitude='.$longitude), true);
          if($geocode) {
            $checkin['properties']['locality'] = [$geocode['locality']];
            $checkin['properties']['region'] = [$geocode['region']];
            $checkin['properties']['country-name'] = [$geocode['country']];
            $tz = new DateTimeZone($geocode['timezone']);
          } else {
            $tz = new DateTimeZone('UTC');
          }

          $departure = new DateTime($flight->departure_time);
          $departure->setTimeZone($tz);

          $trip = [
            'type' => ['h-trip'],
            'properties' => [
              'mode-of-transport' => ['plane'],
              'start' => [$departure->format('c')],
              'flight' => [$flightData['ident']],
              'flight-id' => [$flightData['faFlightID']],
              'aircraft' => [$flightData['type']],
              'origin' => [$flightData['origin']],
              'destination' => [$flightData['destination']],
              'speed' => [
                [
                  'type' => ['h-measure'],
                  'properties' => [
                    'num' => [$flightData['groundspeed']],
                    'unit' => ['mph'],
                  ]
                ]
              ],
              'route' => [Config::$base_url.'flight/'.$flight->id.'/'.$flightData['faFlightID'].'/route.json']
            ]
          ];

          if($flight->arrival_time) {
            $arrival = new DateTime($flight->arrival_time);
            $arrival->setTimeZone($tz);
            $trip['properties']['end'] = [$arrival->format('c')];
          }

          // Convert this to a Micropub request
          $micropub = [
            'action' => 'update',
            'url' => $flight->url,
            'replace' => [
              'checkin' => $checkin,
              'trip' => $trip,
            ]
          ];
          $r = micropub_post_for_user($user, $micropub, null, true);
          print_r($r['response']);

        }
      }

    } else {
      echo "It looks like the flight has not yet departed\n";
    }

    print_r($data);

  } else {
    echo "User ".$user->url." has no FlightAware credentials\n";
  }

}

