@if(!empty($banners) && count($banners) > 0)
<section class="banner-section">
    <div class="swiper-container">
        <div class="swiper-wrapper">
            @foreach($banners as $banner)
                <div class="swiper-slide">
                    <img src="{{ asset($banner->getFirstMediaUrl()) }}" alt="{{ $banner->title ?? 'Banner Image' }}" loading="lazy" style="width: 100%; height: auto;">
                </div>
            @endforeach
        </div>
        <div class="swiper-navigation">
            <button class="swiper-button-prev"><i class="fas fa-chevron-left"></i></button>
            <div class="swiper-pagination"></div>
            <button class="swiper-button-next"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
</section>
@endif
