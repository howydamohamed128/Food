 <footer class="py-4 bg-dark text-white">
     <div class="container">
         <div class="row">
             <div class="col-md-4 mb-3">
                 <h5 class="mb-3">{{$settings->app_name}}</h5>
                 <p>Serving exceptional flavors since 2005. We pride ourselves on using the freshest ingredients and creating memorable dining experiences.</p>
             </div>
             <div class="col-md-4 mb-3">
                 <h5 class="mb-3">Opening Hours</h5>
                 <ul class="list-unstyled">
                     <li>Monday - Friday: 11am - 10pm</li>
                     <li>Saturday & Sunday: 10am - 11pm</li>
                     <li>Happy Hour: 4pm - 6pm daily</li>
                 </ul>
             </div>
             <div class="col-md-4 mb-3">
                 <h5 class="mb-3">Contact Us</h5>
                 <ul class="list-unstyled">
                     <li><i class="fas fa-map-marker-alt me-2"></i>{{$settings->app_address}}</li>
                     <li><i class="fas fa-phone me-2"></i>{{$settings->app_phone}}</li>
                     <li><i class="fas fa-envelope me-2"></i>{{$settings->app_email}}</li>
                 </ul>
                 <div class="social-icons mt-3">
                     @forelse($settings->social_links as $media)
                     <a href="{{ $media['link'] }}" class="text-white me-3" target="_blank"><i class="fab fa-{{$media['icon']}}" ></i></a>
                     @empty
                     @endforelse

                 </div>
             </div>
         </div>
         <div class="border-top border-secondary pt-3 mt-3 text-center">
             <p class="mb-0">&copy; 2025 {{$settings->app_name}}. All rights reserved.</p>
         </div>
     </div>
 </footer>
