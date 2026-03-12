<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        $carouselImages = [
            asset('images/home/back_one.jpg'),
            asset('images/home/back_two.jpg'),
        ];

        return view('home', [
            'carouselImages' => $carouselImages,
        ]);
    }
}
