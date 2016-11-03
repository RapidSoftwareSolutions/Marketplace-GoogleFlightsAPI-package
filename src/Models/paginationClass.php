<?php
namespace Models;

Class NextPage {
    protected $all_data = [];
    
    public function page($url, $nextPage, $headers = '', $term = '') {
        
        if($url) {
            $query['page'] = $nextPage;
            
            $client = new \GuzzleHttp\Client();

            $resp = $client->get( $url, 
                [
                    'headers' => $headers,
                    'query' => $query
                ]);

            $rawBody = json_decode($resp->getBody());
            $this->all_data = array_merge($this->all_data, $rawBody->$term);
            $nextPage++;

            if($rawBody->pagination->page_count >= $nextPage) {
                $this->page($url, $nextPage, $headers, $term);
            }
        }
        return $this->all_data;
    }    
    
}

