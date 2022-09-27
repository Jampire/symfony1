<?php

namespace App\Controller;

use App\Entity\ScoreResult;
use App\Service\ApiService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{

    private ApiService $apiService;
    private $entityManager;

    public function __construct(ApiService $apiService, ManagerRegistry $doctrine)
    {
        $this->apiService = $apiService;
        $this->entityManager = $doctrine->getManager();
    }

    #[Route(path: '/score', name: 'score', methods: ['GET'])]
    public function score(Request $request)
    {
        if ($request->headers->get('Authorization') != $this->getParameter('api_token')) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $data = ['term' => ''];
        $term = $request->query->get('term');

        if (!empty($term)) {
            $currentDateTime = new \DateTime();
            $scoreResultRepo = $this->entityManager->getRepository(ScoreResult::class);
            $scoreResult = $scoreResultRepo->findOneBy(['word' => $term]);
            if (empty($scoreResult)) {
                $positive_results = $this->apiService->countSearchResults($term . ' rocks');
                $negative_results = $this->apiService->countSearchResults($term . ' sucks');
                $score = round(10 * $positive_results / ($positive_results + $negative_results), 2);

                $scoreResult = new ScoreResult();
                $scoreResult->setWord($term);
                $scoreResult->setScore($score);
                $scoreResult->setDate($currentDateTime);
                $this->entityManager->persist($scoreResult);
                $this->entityManager->flush();

                $data = ['term' => $term, 'score' => $score];
            } else {
                $data = ['term' => $scoreResult->getWord(), 'score' => $scoreResult->getScore()];
            }
        }

        return $this->json($data, headers: ['Content-Type' => 'application/json;charset=UTF-8']);
    }

}