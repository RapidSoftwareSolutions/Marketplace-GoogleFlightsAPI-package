<?php

$app->post('/api/GoogleFlightsAPI/searchSingleTrip', function ($request, $response, $args) {
    $settings =  $this->settings;
    
    $data = $request->getBody();

    if($data=='') {
        $post_data = $request->getParsedBody();
    } else {
        $toJson = $this->toJson;
        $data = $toJson->normalizeJson($data); 
        $data = str_replace('\"', '"', $data);
        $post_data = json_decode($data, true);
    }
    
    if(json_last_error() != 0) {
        $error[] = json_last_error_msg() . '. Incorrect input JSON. Please, check fields with JSON input.';
    }
    
    if(!empty($error)) {
        $result['callback'] = 'error';
        $result['contextWrites']['to']['status_code'] = 'JSON_VALIDATION';
        $result['contextWrites']['to']['status_msg'] = implode(',', $error);
        return $response->withHeader('Content-type', 'application/json')->withStatus(200)->withJson($result);
    }
    
    $error = [];
    if(empty($post_data['args']['apiKey'])) {
        $error[] = 'apiKey';
    }
    if($post_data['args']['origin']=='') {
        $error[] = 'origin';
    }
    if($post_data['args']['destination']=='') {
        $error[] = 'destination';
    }
    if($post_data['args']['passengersAdultCount']=='') {
        $error[] = 'passengersAdultCount';
    }
    if($post_data['args']['passengersChildCount']=='') {
        $error[] = 'passengersChildCount';
    }
    if($post_data['args']['fromDate']=='') {
        $error[] = 'fromDate';
    }
    
    if(!empty($error)) {
        $result['callback'] = 'error';
        $result['contextWrites']['to']['status_code'] = "REQUIRED_FIELDS";
        $result['contextWrites']['to']['status_msg'] = "Please, check and fill in required fields.";
        $result['contextWrites']['to']['fields'] = $error;
        return $response->withHeader('Content-type', 'application/json')->withStatus(200)->withJson($result);
    }
    
    $query_str = 'https://www.googleapis.com/qpxExpress/v1/trips/search';
    
    $query['key'] = $post_data['args']['apiKey'];
    $body = [];
    $body['request']['passengers']['kind'] = 'qpxexpress#passengerCounts';
    $body['request']['passengers']['adultCount'] = $post_data['args']['passengersAdultCount'];
    $body['request']['passengers']['childCount'] = $post_data['args']['passengersChildCount'];
    $body['request']['passengers']['infantInLapCount'] = 0;
    $body['request']['passengers']['infantInSeatCount'] = 0;
    $body['request']['request']['passengers']['seniorCount'] = 0;
    if(!empty($post_data['args']['maxPrice'])) {
        $body['request']['maxPrice'] = $post_data['args']['maxPrice'];
    }
    if(!empty($post_data['args']['refundable'])) {
        $body['request']['refundable'] = $post_data['args']['refundable'];
    }
    
    $client = $this->httpClient;
    
    $resp = $client->get( 'https://iatacodes.org/api/v6/cities?api_key=b5f45262-a803-4834-863b-26ed4f9f02c8&code='.$post_data['args']['origin'] );
    $responseBody = $resp->getBody()->getContents();
    $country = json_decode($responseBody, true)['response']['country_code'];
    $body['request']['saleCountry'] = $country;
    $body['request']['ticketingCountry'] = $country;
    
    $slice = [];
    $origin['kind'] = 'qpxexpress#sliceInput';
    $origin['origin'] = $post_data['args']['origin'];
    $origin['destination'] = $post_data['args']['destination'];
    $origin['date'] = $post_data['args']['fromDate'];
    $slice[] = $origin;
    if(!empty($post_data['args']['toDate'])) {
        $origin['kind'] = 'qpxexpress#sliceInput';
        $origin['origin'] = $post_data['args']['destination'];
        $origin['destination'] = $post_data['args']['origin'];
        $origin['date'] = $post_data['args']['toDate'];
        $slice[] = $origin;
    }
    $body['request']['slice'] = $slice;
    if(!empty($post_data['args']['solutions'])) {
        $body['request']['solutions'] = $post_data['args']['solutions'];
    }
    

    try {

        $resp = $client->post( $query_str, 
            [
                'json' => $body,
                'query' => $query
            ]);
        $responseBody = $resp->getBody()->getContents();
  
        if($resp->getStatusCode() == '200') {
            $result['callback'] = 'success';
            $result['contextWrites']['to'] = is_array($responseBody) ? $responseBody : json_decode($responseBody);
        } else {
            $result['callback'] = 'error';
            $result['contextWrites']['to']['status_code'] = 'API_ERROR';
            $result['contextWrites']['to']['status_msg'] = is_array($responseBody) ? $responseBody : json_decode($responseBody);
        }

    } catch (\GuzzleHttp\Exception\ClientException $exception) {

        $responseBody = $exception->getResponse()->getBody()->getContents();
        if(empty(json_decode($responseBody))) {
            $out = $responseBody;
        } else {
            $out = json_decode($responseBody);
        }
        $result['callback'] = 'error';
        $result['contextWrites']['to']['status_code'] = 'API_ERROR';
        $result['contextWrites']['to']['status_msg'] = $out;

    } catch (GuzzleHttp\Exception\ServerException $exception) {

        $responseBody = $exception->getResponse()->getBody()->getContents();
        if(empty(json_decode($responseBody))) {
            $out = $responseBody;
        } else {
            $out = json_decode($responseBody);
        }
        $result['callback'] = 'error';
        $result['contextWrites']['to']['status_code'] = 'API_ERROR';
        $result['contextWrites']['to']['status_msg'] = $out;

    } catch (GuzzleHttp\Exception\ConnectException $exception) {

        $responseBody = $exception->getResponse()->getBody(true);
        $result['callback'] = 'error';
        $result['contextWrites']['to']['status_code'] = 'INTERNAL_PACKAGE_ERROR';
        $result['contextWrites']['to']['status_msg'] = 'Something went wrong inside the package.';

    }
    
    return $response->withHeader('Content-type', 'application/json')->withStatus(200)->withJson($result);
});
