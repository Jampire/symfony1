<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function countSearchResults($search_string)
    {
        $search_string = str_replace(' ', '+', $search_string);

        $response = $this->client->request(
          'GET',
          'https://api.github.com/search/repositories?q='.$search_string
        );
        $result = json_decode($response->getContent());

        return $result->total_count ?? 0;
    }

}