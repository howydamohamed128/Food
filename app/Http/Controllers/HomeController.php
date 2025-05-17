<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Content\Banner;
use App\Models\Catalog\Product;
use App\Models\Catalog\Category;
use App\Settings\GeneralSettings;

class HomeController extends Controller
{
    public function index(){
        $settings = new GeneralSettings();
        $banners = Banner::enabled()->get();
        $categories = Category::enabled()->get();
        $products = Product::with('category')->enabled()->get();
        return view('site.index', compact('settings','banners','categories','products'));

    }
}
