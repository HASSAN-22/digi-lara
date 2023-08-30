<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserAccessEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Morilog\Jalali\Jalalian;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'sanctum';

    protected $appends = ['ir_created_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'access',
        'mobile',
        'password',
        'status',
        'avatar',
    ];


    public function getIrCreatedAtAttribute()
    {
        return Jalalian::forge($this->created_at)->format('%d %B %Y');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    public function scopeSearch($query){
        $search = trim(request()->get('search'));
        if($search != ''){
            $search = typeService($search)->access('en')->status('en')->get();
            $query = $query->where('name','like',"%$search%")->orWhere('mobile','like',"%$search%")->orWhere('access',$search)
                ->orWhere('status',$search);
        }
        return $query;
    }

    public function generateToken():string{
        $this->tokens()->delete();
        return $this->createToken(getDevice())->plainTextToken;
    }

    public function isAdmin():bool{
        return UserAccessEnum::isAdmin();
    }

    public function isUser():bool{
        return UserAccessEnum::isUser();
    }

    public function isSeller():bool{
        return UserAccessEnum::isSeller();
    }

    public function brands(){
        return $this->hasMany(Brand::class);
    }

    public function products(){
        return $this->hasMany(Product::class);
    }

    public function wishlists(){
        return $this->hasMany(Wishlist::class);
    }

    public function amazingAlerts(){
        return $this->hasMany(Amazingalert::class);
    }

    public function compares(){
        return $this->hasMany(Compare::class);
    }

    public function productComments(){
        return $this->hasMany(Productcomment::class);
    }

    public function productNotifyExists(){
        return $this->hasMany(Productnotifyexists::class);
    }

    public function baskets(){
        return $this->hasMany(Basket::class);
    }

    public function addresses(){
        return $this->hasMany(Address::class);
    }

    public function couponUsers(){
        return $this->hasMany(Couponuser::class);
    }

    public function profile(){
        return $this->hasOne(Profile::class);
    }

    public function legalInfo(){
        return $this->hasOne(Legalinfo::class);
    }

    public function wallet(){
        return $this->hasOne(Wallet::class);
    }

    public function transactions(string $model){
        return $this->hasMany(Transaction::class)->where('transactionable_type','App\Models\\'.$model);
    }

    public function orders(){
        return $this->hasMany(Order::class);
    }

    public function orderDetails(){
        return $this->hasMany(Orderdetail::class);
    }

    public function becomeSellers(){
        return $this->hasMany(Becomeseller::class);
    }

    public function tickets(){
        return $this->hasMany(Ticket::class);
    }

    public function ticketMessages(){
        return $this->hasMany(Ticketmessage::class);
    }

    public function withdrawWallets(){
        return $this->hasMany(Withdrawwallet::class);
    }

    public function debtors(){
        return $this->hasMany(Debtor::class);
    }
}
