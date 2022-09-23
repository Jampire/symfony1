<?php

namespace App\Service;

class SearchService
{

    public function countSearchResults($search_string)
    {
        $curl_url = 'https://api.github.com/search/repositories?q='.$search_string;
        $curl = curl_init($curl_url);
        curl_setopt_array(
          $curl,
          [CURLOPT_RETURNTRANSFER => 1, CURLOPT_USERAGENT => 'saladiphp']
        );
        $result = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($result);

        return $result->total_count ?? 0;
    }

}