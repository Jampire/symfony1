<?php

namespace App\Controller;

use App\Service\SearchService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{

    private SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    #[Route(path: '/score', name: 'score', methods: ['GET'])]
    public function score(Request $request)
    {
        $data = ['term' => ''];
        $term = $request->query->get('term');

        if (!empty($term)) {
            $positive_results = $this->searchService->countSearchResults($term.'+rocks');
            $negative_results = $this->searchService->countSearchResults($term.'+sucks');
            $score = round(10 * $positive_results / ($positive_results + $negative_results), 2);
            $data = ['term' => $term, 'score' => $score];
        }

        return $this->json($data, headers: ['Content-Type' => 'application/json;charset=UTF-8']);
    }

}