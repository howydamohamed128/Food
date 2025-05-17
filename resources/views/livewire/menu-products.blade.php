<div>
    <section class="categories-section py-5">
        <div class="container">
            <h2 class="section-title text-center mb-4">Our Menu Categories</h2>
            <div class="categories-container d-flex justify-content-center gap-3" id="categories-container">
                @foreach($categories as $category)
                <div wire:click="selectCategory({{ $category->id }})" class="category-card" loading="lazy" style="min-width: 150px; cursor:pointer; border: 2px solid {{ $selectedCategoryId == $category->id ? '#f00' : 'transparent' }}; padding:10px; border-radius:8px;">
                    <img src="{{ $category->getFirstMediaUrl() }}" alt="{{ $category->name }}" class="category-image">
                    <p class="category-name text-center mt-2">{{ $category->name }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="menu-items-section py-5 bg-dark text-white">
        <div class="container">
            <h2 class="section-title text-center mb-4" style="font-weight: 600; border-bottom: 3px solid #ff4500; display: inline-block; padding-bottom: 5px;">
                {{ $categories->firstWhere('id', $selectedCategoryId)->name ?? 'All Items' }}
            </h2>
            <div class="menu-items-container d-flex flex-wrap justify-content-center gap-4" id="menu-items-container">
                @forelse($products as $product)
                <div class="card bg-dark text-white border-0" style="display: flex; flex-direction: column;">
                    <img src="{{ $product->getFirstMediaUrl() }}" class="product-image card-img-top" loading="lazy" alt="{{ $product->title }}" style="height: 200px; object-fit: cover;">
                    <div class="card-body bg-light text-dark d-flex flex-column flex-grow-1">
                        <h5 class="card-title mb-2">{{ $product->title }}</h5>
                        <p class="text-danger fw-bold mb-2">${{ number_format($product->price, 2) }}</p>
                        <p class="card-text mt-auto" style="overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                            {{ $product->description }}
                        </p>
                    </div>
                </div>
                @empty
                <p class="text-center text-white-50">No products found in this category.</p>
                @endforelse
            </div>
        </div>
    </section>

</div>
