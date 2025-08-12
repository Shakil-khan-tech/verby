<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index(Request $request)
    {
        $page_title = __('Dashboard');
        $page_description = __('Select an action');

        return view('pages.dashboard', compact('page_title', 'page_description'));
    }

    /**
     * Demo methods below
     */

     
    // // Quicksearch Result
    // public function quickSearch()
    // {
    //     return view('layout.partials.extras._quick_search_result');
    // }
}
