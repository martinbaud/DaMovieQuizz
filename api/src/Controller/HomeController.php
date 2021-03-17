<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class HomeController
{
  /**
   * @Route("/game/", methods={"GET"})
   */
  public function index(): Response
  {

    $key = "7ea5f490261a949e52930517e1b4657c";
    $lang = "en-US";

    // Select randomly a page
    $page = rand(1, 500);
    $link  = "https://api.themoviedb.org/3/movie/popular";
    $req = $link . "?api_key=" . $key . "&language=" . $lang . "&page=" . $page;

    // Select randomly a movie in the page
    $select = rand(1, 8);
    $res = file_get_contents($req);
    $res = json_decode($res, true);
    $id = $res["results"][1]['id'];

    $req = "https://api.themoviedb.org/3/movie/" . $id . "?api_key=" . $key . "&language=" . $lang;

    $res = file_get_contents($req);
    $res = json_decode($res, true);

    return new Response($res['title']);
  }
}
