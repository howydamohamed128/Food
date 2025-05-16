<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Role;
use App\Models\AddressBook;
use App\Models\Branch;
use App\Models\Catalog\Category;
use App\Models\Catalog\Option;
use App\Models\Catalog\Product;
use App\Models\ContactType;
use App\Models\Content\Banner;
use App\Models\Content\Contact;
use App\Models\Content\CustomerReview;
use App\Models\Content\Page;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Location\City;
use App\Models\Location\District;
use App\Models\Manager;
use App\Models\Operator;
use App\Models\Order;
use App\Models\Report\CustomersPayment;
use App\Models\WholesaleRequest;
use App\Policies\AddressBookPolicy;
use App\Policies\BranchPolicy;
use App\Policies\Catalog\CategoryPolicy;
use App\Policies\Catalog\OptionPolicy;
use App\Policies\Catalog\ProductPolicy;
use App\Policies\ContactTypePolicy;
use App\Policies\Content\BannerPolicy;
use App\Policies\Content\ContactPolicy;
use App\Policies\Content\CustomerReviewPolicy;
use App\Policies\Content\PagePolicy;
use App\Policies\CouponPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\Location\CityPolicy;
use App\Policies\Location\DistrictPolicy;
use App\Policies\ManagerPolicy;
use App\Policies\OperatorPolicy;
use App\Policies\OrderPolicy;
use App\Policies\Report\CustomersPaymentPolicy;
use App\Policies\RolePolicy;
use App\Policies\WholesaleRequestPolicy;

class AuthServiceProvider extends ServiceProvider {
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Customer::class => CustomerPolicy::class,
        Product::class => ProductPolicy::class,
       Category::class => CategoryPolicy::class,
        // Manager::class => ManagerPolicy::class,
        // Operator::class => OperatorPolicy::class,
        // Page::class => PagePolicy::class,
        Banner::class => BannerPolicy::class,
        Role::class => RolePolicy::class,
        // Order::class => OrderPolicy::class,
        // Coupon::class => CouponPolicy::class,
        // CustomersPayment::class => CustomersPaymentPolicy::class,
        User::class => UserPolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void {
        //
    }
}
