<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Catalog\Category;
use App\Models\Catalog\Product;

class MenuProducts extends Component
{
    public $categories = [];
    public $products = [];
    public $selectedCategoryId = null;

    public function mount()
    {
        $this->categories = Category::enabled()->get();
        if($this->categories->count() > 0){
            $this->selectedCategoryId = $this->categories->first()->id;
            $this->loadProducts();
        }
    }

    public function loadProducts()
    {
        if($this->selectedCategoryId){
            $this->products = Product::with('category')
                ->enabled()
                ->where('category_id', $this->selectedCategoryId)
                ->get();
        } else {
            $this->products = Product::with('category')->enabled()->get();
        }
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategoryId = $categoryId;
        $this->loadProducts();
    }

    public function render()
    {
        return view('livewire.menu-products');
    }
}
