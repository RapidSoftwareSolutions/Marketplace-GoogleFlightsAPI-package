<?php

$app->post('/api/GoogleFlightsAPI/searchTrips', function ($request, $response, $args) {
    $settings =  $this->settings;
    
    $data = $request->getBody();

    if($data=='') {
        $data = $request->getParsedBody();
        $data['args']['slice'] = json_decode(substr($data['args']['slice'], 1, -1), true);
        $post_data = $data;        
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
        $result['contextWrites']['to'] = implode(',', $error);
        return $response->withHeader('Content-type', 'application/json')->withStatus(200)->withJson($result);
    }
    
    $error = [];
    if(empty($post_data['args']['apiKey'])) {
        $error[] = 'apiKey is required';
    }
    if(empty($post_data['args']['passengersKind'])) {
        $error[] = 'passengersKind is required';
    }
    if($post_data['args']['passengersAdultCount']=='') {
        $error[] = 'passengersAdultCount is required';
    }
    if($post_data['args']['passengersChildCount']=='') {
        $error[] = 'passengersChildCount is required';
    }
    if($post_data['args']['passengersInfantInLapCount']=='') {
        $error[] = 'passengersInfantInLapCount is required';
    }
    if($post_data['args']['passengersInfantInSeatCount']=='') {
        $error[] = 'passengersInfantInSeatCount is required';
    }
    if($post_data['args']['passengersSeniorCount']=='') {
        $error[] = 'passengersSeniorCount is required';
    }
    if(empty($post_data['args']['slices'])) {
        $error[] = 'slices is required';
    }
    if(empty($post_data['args']['saleCountry'])) {
        $error[] = 'saleCountry is required';
    }
    if(empty($post_data['args']['ticketingCountry'])) {
        $error[] = 'ticketingCountry is required';
    }
    
    if(!empty($error)) {
        $result['callback'] = 'error';
        $result['contextWrites']['to']['message'] = "There are incomplete fields in your request";
        $result['contextWrites']['to']['fields'] = $error;
        return $response->withHeader('Content-type', 'application/json')->withStatus(200)->withJson($result);
    }
    
    $query_str = 'https://www.googleapis.com/qpxExpress/v1/trips/search';
    
    $query['key'] = $post_data['args']['apiKey'];
    if(!empty($post_data['args']['fields'])) {
        $query['fields'] = $post_data['args']['fields'];
    }
    if(!empty($post_data['args']['quotaUser'])) {
        $query['quotaUser'] = $post_data['args']['quotaUser'];
    }
    if(!empty($post_data['args']['userIp'])) {
        $query['userIp'] = $post_data['args']['userIp'];
    }
    
    $body = [];
    if(!empty($post_data['args']['passengersKind'])) {
        $body['request']['passengers']['kind'] = $post_data['args']['passengersKind'];
    }
    if(!empty($post_data['args']['passengersAdultCount'])) {
        $body['request']['passengers']['adultCount'] = $post_data['args']['passengersAdultCount'];
    }
    if(!empty($post_data['args']['passengersChildCount'])) {
        $body['request']['passengers']['childCount'] = $post_data['args']['passengersChildCount'];
    }
    if(!empty($post_data['args']['passengersInfantInLapCount'])) {
        $body['request']['passengers']['infantInLapCount'] = $post_data['args']['passengersInfantInLapCount'];
    }
    if(!empty($post_data['args']['passengersInfantInSeatCount'])) {
        $body['request']['passengers']['infantInSeatCount'] = $post_data['args']['passengersInfantInSeatCount'];
    }
    if(!empty($post_data['args']['passengersSeniorCount'])) {
        $body['request']['request']['passengers']['seniorCount'] = $post_data['args']['passengersSeniorCount'];
    }
    if(!empty($post_data['args']['slices'])) {
        $body['request']['slice'] = $post_data['args']['slices'];
    }
    if(!empty($post_data['args']['maxPrice'])) {
        $body['request']['maxPrice'] = $post_data['args']['maxPrice'];
    }
    if(!empty($post_data['args']['saleCountry'])) {
        $body['request']['saleCountry'] = $post_data['args']['saleCountry'];
    }
    if(!empty($post_data['args']['ticketingCountry'])) {
        $body['request']['ticketingCountry'] = $post_data['args']['ticketingCountry'];
    }
    if(!empty($post_data['args']['refundable'])) {
        $body['request']['refundable'] = $post_data['args']['refundable'];
    }
    if(!empty($post_data['args']['solutions'])) {
        $body['request']['solutions'] = $post_data['args']['solutions'];
    }
   
    $client = $this->httpClient;

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
            $result['contextWrites']['to'] = is_array($responseBody) ? $responseBody : json_decode($responseBody);
        }

    } catch (\GuzzleHttp\Exception\ClientException $exception) {

        $responseBody = $exception->getResponse()->getBody();
        $result['callback'] = 'error';
        $result['contextWrites']['to'] = json_decode($responseBody);

    } catch (GuzzleHttp\Exception\ServerException $exception) {

        $responseBody = $exception->getResponse()->getBody(true);
        $result['callback'] = 'error';
        $result['contextWrites']['to'] = json_decode($responseBody);

    } catch (GuzzleHttp\Exception\BadResponseException $exception) {

        $responseBody = $exception->getResponse()->getBody(true);
        $result['callback'] = 'error';
        $result['contextWrites']['to'] = json_decode($responseBody);

    }
    
    return $response->withHeader('Content-type', 'application/json')->withStatus(200)->withJson($result);
});
