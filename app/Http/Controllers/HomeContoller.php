<?php

namespace App\Http\Controllers;

use App\Models\Content\Banner;
use App\Models\Content\CustomerReview;
use App\Models\Content\Page;
use App\Models\Faq;
use App\Settings\GeneralSettings;
use App\Settings\LandingSettings;

class HomeContoller extends Controller
{
    public function index()
    {
        $settings = new LandingSettings();
        $about_description = $settings->about_description;
        $about_features = $settings->about_features;
        $our_features_description = $settings->our_features_description;
        $features = $settings->features;
        $feature_image = $settings->feature_image;
        $app_screens = $settings->app_screen;
        $customer_reviews = CustomerReview::where('status', 1)->get();
        $faqs = Faq::where('status', 1)->take(5)->get();
        return view('site.index', compact(
            'about_description',
            'about_features',
            'our_features_description',
            'features',
            'feature_image',
            'app_screens',
            'customer_reviews',
            'faqs',
        ));
    }

    public function aboutUs(){
        $general_settings = new GeneralSettings();
        $about_us_id = $general_settings->app_pages['about_us'];
        $about_us = Page::findOrFail($about_us_id);
        return view('site.about-us', compact('about_us'));
    }

    public function faqs(){
        $general_settings = new GeneralSettings();
        $faqs = Faq::where('status', 1)->get();
        return view('site.faqs', compact('faqs'));
    }

    public function term(){
        $general_settings = new GeneralSettings();
        $term_id = $general_settings->app_pages['terms_and_conditions'];
        $term = Page::findOrFail($term_id);

        return view('site.term', compact('term'));
    }

    public  function privacyPolicy(){
        $general_settings = new GeneralSettings();
        $privacy_policy_id = $general_settings->app_pages['privacy_policy'];
        $privacy_policy = Page::findOrFail($privacy_policy_id);
        return view('site.privacy-policy', compact('privacy_policy'));
    }
}
