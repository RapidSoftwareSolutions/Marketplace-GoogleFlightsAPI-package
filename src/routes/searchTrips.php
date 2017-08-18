<?php

$app->post('/api/GoogleFlightsAPI/searchTrips', function ($request, $response, $args) {
    $settings = $this->settings;

    $data = $request->getBody();

    if ($data == '') {
        $data = $request->getParsedBody();
        $data['args']['slice'] = json_decode(substr($data['args']['slice'], 1, -1), true);
        $post_data = $data;
    } else {
        $toJson = $this->toJson;
        $data = $toJson->normalizeJson($data);
        $data = str_replace('\"', '"', $data);
        $post_data = json_decode($data, true);
    }

    if (json_last_error() != 0) {
        $error[] = json_last_error_msg() . '. Incorrect input JSON. Please, check fields with JSON input.';
    }

    if (!empty($error)) {
        $result['callback'] = 'error';
        $result['contextWrites']['to']['status_code'] = 'JSON_VALIDATION';
        $result['contextWrites']['to']['status_msg'] = implode(',', $error);
        return $response->withHeader('Content-type', 'application/json')->withStatus(200)->withJson($result);
    }

    $error = [];
    if (empty($post_data['args']['apiKey'])) {
        $error[] = 'apiKey';
    }
    if (empty($post_data['args']['passengersKind'])) {
        $error[] = 'passengersKind';
    }
    if (empty($post_data['args']['passengersAdultCount'])) {
        $error[] = 'passengersAdultCount';
    }
    if (empty($post_data['args']['passengersChildCount']) && $post_data['args']['passengersChildCount'] != 0) {
        $error[] = 'passengersChildCount';
    }
    if (empty($post_data['args']['passengersInfantInLapCount']) && $post_data['args']['passengersInfantInLapCount'] != 0) {
        $error[] = 'passengersInfantInLapCount';
    }
    if (empty($post_data['args']['passengersInfantInSeatCount']) && $post_data['args']['passengersInfantInSeatCount'] != 0) {
        $error[] = 'passengersInfantInSeatCount';
    }
    if (empty($post_data['args']['passengersSeniorCount']) && $post_data['args']['passengersSeniorCount'] != 0) {
        $error[] = 'passengersSeniorCount';
    }
    if (empty($post_data['args']['slices'])) {
        $error[] = 'slices';
    }
    if (empty($post_data['args']['saleCountry'])) {
        $error[] = 'saleCountry';
    }
    if (empty($post_data['args']['ticketingCountry'])) {
        $error[] = 'ticketingCountry';
    }

    if (!empty($error)) {
        $result['callback'] = 'error';
        $result['contextWrites']['to']['status_code'] = "REQUIRED_FIELDS";
        $result['contextWrites']['to']['status_msg'] = "Please, check and fill in required fields.";
        $result['contextWrites']['to']['fields'] = $error;
        return $response->withHeader('Content-type', 'application/json')->withStatus(200)->withJson($result);
    }

    $query_str = 'https://www.googleapis.com/qpxExpress/v1/trips/search';

    $query['key'] = $post_data['args']['apiKey'];
    if (!empty($post_data['args']['fields'])) {
        $query['fields'] = is_array($post_data['args']['fields']) ? implode(',', $post_data['args']['fields']) : $post_data['args']['fields'];
    }
    if (!empty($post_data['args']['quotaUser'])) {
        $query['quotaUser'] = $post_data['args']['quotaUser'];
    }
    if (!empty($post_data['args']['userIp'])) {
        $query['userIp'] = $post_data['args']['userIp'];
    }

    $body = [];
    if (!empty($post_data['args']['passengersKind'])) {
        $body['request']['passengers']['kind'] = $post_data['args']['passengersKind'];
    }
    if (!empty($post_data['args']['passengersAdultCount'])) {
        $body['request']['passengers']['adultCount'] = $post_data['args']['passengersAdultCount'];
    }
    if (!empty($post_data['args']['passengersChildCount'])) {
        $body['request']['passengers']['childCount'] = $post_data['args']['passengersChildCount'];
    }
    if (!empty($post_data['args']['passengersInfantInLapCount'])) {
        $body['request']['passengers']['infantInLapCount'] = $post_data['args']['passengersInfantInLapCount'];
    }
    if (!empty($post_data['args']['passengersInfantInSeatCount'])) {
        $body['request']['passengers']['infantInSeatCount'] = $post_data['args']['passengersInfantInSeatCount'];
    }
    if (!empty($post_data['args']['passengersSeniorCount'])) {
        $body['request']['request']['passengers']['seniorCount'] = $post_data['args']['passengersSeniorCount'];
    }
    if (!empty($post_data['args']['slices'])) {
        $body['request']['slice'] = $post_data['args']['slices'];
    }
    if (!empty($post_data['args']['maxPrice'])) {
        $body['request']['maxPrice'] = $post_data['args']['maxPrice'];
    }
    if (!empty($post_data['args']['saleCountry'])) {
        $body['request']['saleCountry'] = $post_data['args']['saleCountry'];
    }
    if (!empty($post_data['args']['ticketingCountry'])) {
        $body['request']['ticketingCountry'] = $post_data['args']['ticketingCountry'];
    }
    if (!empty($post_data['args']['refundable'])) {
        $body['request']['refundable'] = $post_data['args']['refundable'];
    }
    if (!empty($post_data['args']['solutions'])) {
        $body['request']['solutions'] = $post_data['args']['solutions'];
    }

    $client = $this->httpClient;

    try {

        $resp = $client->post($query_str,
            [
                'json' => $body,
                'query' => $query
            ]);
        $responseBody = $resp->getBody()->getContents();

        if ($resp->getStatusCode() == '200') {
            $result['callback'] = 'success';
            $result['contextWrites']['to'] = is_array($responseBody) ? $responseBody : json_decode($responseBody);
        } else {
            $result['callback'] = 'error';
            $result['contextWrites']['to']['status_code'] = 'API_ERROR';
            $result['contextWrites']['to']['status_msg'] = is_array($responseBody) ? $responseBody : json_decode($responseBody);
        }

    } catch (\GuzzleHttp\Exception\ClientException $exception) {

        $responseBody = $exception->getResponse()->getBody()->getContents();
        if (empty(json_decode($responseBody))) {
            $out = $responseBody;
        } else {
            $out = json_decode($responseBody);
        }
        $result['callback'] = 'error';
        $result['contextWrites']['to']['status_code'] = 'API_ERROR';
        $result['contextWrites']['to']['status_msg'] = $out;

    } catch (GuzzleHttp\Exception\ServerException $exception) {

        $responseBody = $exception->getResponse()->getBody()->getContents();
        if (empty(json_decode($responseBody))) {
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
