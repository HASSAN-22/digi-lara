<?php

namespace App\Providers;
// use Illuminate\Support\Facades\Gate;
use App\Models\Address;
use App\Models\Becomeseller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Contact;
use App\Models\Coupon;
use App\Models\Debtor;
use App\Models\Email;
use App\Models\Faq;
use App\Models\Guarantee;
use App\Models\Returned;
use App\Models\Sms;
use App\Models\Work;
use App\Models\Legalinfo;
use App\Models\News;
use App\Models\Newsletter;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Models\Productcomment;
use App\Models\Productquestion;
use App\Models\Productquestionanswer;
use App\Models\Profile;
use App\Models\Property;
use App\Models\Report;
use App\Models\Shopconfig;
use App\Models\Size;
use App\Models\Slider;
use App\Models\Ticket;
use App\Models\Ticketcategory;
use App\Models\Transport;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Widget;
use App\Models\Wishlist;
use App\Models\Withdrawwallet;
use App\Policies\AddressPolicy;
use App\Policies\BecomesellerPollicy;
use App\Policies\BrandPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\ColorPolicy;
use App\Policies\ContactPolicy;
use App\Policies\CouponPolicy;
use App\Policies\DebtorPolicy;
use App\Policies\EmailPolicy;
use App\Policies\FaqPolicy;
use App\Policies\GuaranteePolicy;
use App\Policies\ReturnedPolicy;
use App\Policies\SmsPolicy;
use App\Policies\WorkPolicy;
use App\Policies\LegalInfoPolicy;
use App\Policies\NewsletterPolicy;
use App\Policies\NewsPolicy;
use App\Policies\OrderPolicy;
use App\Policies\PagePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\ProductCommentPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ProductquestionanswerPolicy;
use App\Policies\ProductQuestionPolicy;
use App\Policies\ProfilePolicy;
use App\Policies\PropertyPolicy;
use App\Policies\ReportPolicy;
use App\Policies\RolePolicy;
use App\Policies\ShopconfigPollicy;
use App\Policies\SizePolicy;
use App\Policies\SliderPolicy;
use App\Policies\TicketcategoryPolicy;
use App\Policies\TicketPolicy;
use App\Policies\TransportPolicy;
use App\Policies\UserPolicy;
use App\Policies\WalletPolicy;
use App\Policies\WidgetPolicy;
use App\Policies\WishlistPolicy;
use App\Policies\WithdrawwalletPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Category::class => CategoryPolicy::class,
        Property::class => PropertyPolicy::class,
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
        Brand::class => BrandPolicy::class,
        Guarantee::class => GuaranteePolicy::class,
        Product::class => ProductPolicy::class,
        Widget::class => WidgetPolicy::class,
        Slider::class => SliderPolicy::class,
        Color::class => ColorPolicy::class,
        Size::class => SizePolicy::class,
        Address::class => AddressPolicy::class,
        Coupon::class => CouponPolicy::class,
        Work::class => WorkPolicy::class,
        Profile::class => ProfilePolicy::class,
        Legalinfo::class => LegalInfoPolicy::class,
        Wallet::class => WalletPolicy::class,
        Order::class => OrderPolicy::class,
        Transport::class => TransportPolicy::class,
        Shopconfig::class => ShopconfigPollicy::class,
        Becomeseller::class => BecomesellerPollicy::class,
        Faq::class => FaqPolicy::class,
        News::class => NewsPolicy::class,
        Page::class => PagePolicy::class,
        Ticketcategory::class => TicketcategoryPolicy::class,
        Ticket::class => TicketPolicy::class,
        Contact::class => ContactPolicy::class,
        Withdrawwallet::class => WithdrawwalletPolicy::class,
        Newsletter::class => NewsletterPolicy::class,
        Report::class => ReportPolicy::class,
        Productcomment::class => ProductCommentPolicy::class,
        Productquestion::class => ProductQuestionPolicy::class,
        Productquestionanswer::class => ProductquestionanswerPolicy::class,
        Wishlist::class => WishlistPolicy::class,
        Email::class => EmailPolicy::class,
        Sms::class => SmsPolicy::class,
        Returned::class => ReturnedPolicy::class,
        Debtor::class => DebtorPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
