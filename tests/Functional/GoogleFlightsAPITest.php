<?php

namespace Tests\Functional;

require_once __DIR__ . '/../../src/Models/normalizeJson.php';

class GoogleFlightsAPITest extends BaseTestCase {
    
    public function testSearchTrips() {
        
        $datetime = new \DateTime('tomorrow');
        $datetime->modify('+5 day');
        $day1 = $datetime->format('Y-m-d');
        $day2 = $datetime->format('Y-m-d');
        $var = '{
                    "args": {
                            "apiKey": "AIzaSyAzZh-8USDutBTnBWMoH6VADYn_qXXR7pA",
                            "passengersKind": "qpxexpress#passengerCounts",
                            "passengersAdultCount": "2",
                            "passengersChildCount": "0",
                            "passengersInfantInLapCount": "0",
                            "passengersInfantInSeatCount": "0",
                            "passengersSeniorCount": "0",
                            "slice": "\"[{\"kind\": \"qpxexpress#sliceInput\", \"origin\": \"BOS\", \"destination\": \"LAX\", \"date\": \"'.$day1.'\"}, {\"kind\": \"qpxexpress#sliceInput\", \"origin\": \"LAX\", \"destination\": \"BOS\", \"date\": \"'.$day2.'\"}]\"",
                            "saleCountry": "US",
                            "ticketingCountry": "US",
                            "solutions": "1"
                    }
                }';
        $post_data = json_decode($var, true);

        $response = $this->runApp('POST', '/api/GoogleFlightsAPI/searchTrips', $post_data);
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());
        $this->assertEquals('success', json_decode($response->getBody())->callback);
    }
    
}
