<div>
    <section class="categories-section py-5">
        <div class="container">
            <h2 class="section-title text-center mb-4">Our Menu Categories</h2>
            <div class="categories-container d-flex justify-content-center gap-3" id="categories-container">
                @foreach($categories as $category)
                <div class="category-card" wire:click="selectCategory({{ $category->id }})" loading="lazy" style="border-color: {{ $selectedCategoryId == $category->id ? '#f00' : 'transparent' }};">
                    <img src="{{ $category->getFirstMediaUrl() }}" alt="{{ $category->name }}" class="category-image">
                    <p class="category-name">{{ $category->name }}</p>
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
                <div class="menu-item">
                    <div class="menu-item-img" style="background: url('{{ $product->getFirstMediaUrl() }}') center/cover no-repeat;"></div>
                    <div class="menu-item-info">
                        <div class="menu-item-header">
                            <h4 class="menu-item-name">{{ $product->title }}</h4>
                            <p class="menu-item-price">${{ number_format($product->price, 2) }}</p>
                        </div>
                        <p class="menu-item-description">{{ $product->description }}</p>
                        <div class="menu-item-tags">
                            <span class="tag">{{ $product->category->name ?? 'No Category' }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-center text-white-50">No products found in this category.</p>
                @endforelse
            </div>
        </div>
    </section>

</div>
