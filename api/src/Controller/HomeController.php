<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController
{
  private $hash = "Zd9h5&TGL<U:Y6y";
  private $movieApiKey = "7ea5f490261a949e52930517e1b4657c";
  private $lang = "fr";


  /**
   * @Route("/", name="home")
   */
   public function index(): Response {
     return new Response(md5($this->hash));
   }

  /**
   * @Route("/game/{hash}/start")
   */
  public function game($hash): Response
  {

    if ($hash != md5($this->hash)) {
      return new Response("The hash question is not valid");
    }


    // Requesting randomly a famous movie (title and picture), with one of its actors (name and picture)
    $requestParameters = array();
    $requestParameters['page_movie'] = rand(1, 500);
    $requestParameters['page_people'] = rand(1, 500);
    $requestParameters['movie_index'] = rand(1, 8);
    $requestParameters['people_index'] = rand(1, 8);
    $requestParameters['url_page']  = "https://api.themoviedb.org/3/movie/popular";
    $requestParameters['url']  = "https://api.themoviedb.org/3/movie/";
    $requestParameters['url_popular_people']  = "https://api.themoviedb.org/3/person/popular";

    $request = $requestParameters['url_page'] . "?api_key=" . $this->movieApiKey . "&language=" . $this->lang . "&page=" . $requestParameters['page_movie'];

    $result = json_decode(file_get_contents($request), true);
    $movieId = $result['results'][$requestParameters['movie_index']]['id'];

    $request = $requestParameters['url'] . $movieId . "?api_key=" . $this->movieApiKey . "&language=" . $this->lang;

    $response = file_get_contents($request);
    $response = json_decode($response, true);

    $title = $response['title'];
    $backdrop = "https://image.tmdb.org/t/p/w500/" . $response['backdrop_path'];

    $requestParameters['url_credits'] = "https://api.themoviedb.org/3/movie/" . $movieId . "/credits";

    $request = $requestParameters['url_credits'] . "?api_key=" . $this->movieApiKey . "&language=" . $this->lang;

    $response = file_get_contents($request);
    $response = json_decode($response, true);
    $actorIndex = rand(1, 3);

    $actorTrueName = $response['cast'][$actorIndex]['name'];
    $actorProfileUrl = "https://image.tmdb.org/t/p/w500/" . $response['cast'][$actorIndex]['profile_path'];

    $request = $requestParameters['url_popular_people'] . "?api_key=" . $this->movieApiKey . "&language=" . $this->lang . "&page=" . $requestParameters['page_people'];
    $response = json_decode(file_get_contents($request), true);
    $actorFalseName = $response['results'][$requestParameters['people_index']]['name'];
    $actorFalseProfile = "https://image.tmdb.org/t/p/w500/" . $response['results'][$requestParameters['people_index']]['profile_path'];

    $response = array(
      "movie" => $title,
      "backdrop" => $backdrop,
      "true_actor" => $actorTrueName,
      "true_profile" => $actorProfileUrl,
      "false_actor" => $actorFalseName,
      "false_profile" => $actorFalseProfile,
    );

    return new JsonResponse($response);
  }
}
