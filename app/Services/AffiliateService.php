<?php

namespace App\Services;

use App\Exceptions\AffiliateCreateException;
use App\Mail\AffiliateCreated;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AffiliateService
{
    public function __construct(
        protected ApiService $apiService,
        protected  Affiliate $affiliate
    ) {}

    /**
     * Create a new affiliate for the merchant with the given commission rate.
     *
     * @param  Merchant $merchant
     * @param  string $email
     * @param  string $name
     * @param  float $commissionRate
     * @return Affiliate
     */
    public function register(Merchant $merchant, string $email, string $name, float $commissionRate): Affiliate
    {
        // TODO: Complete this method
        try {
            if ($this->getAffiliate($email)) {
                Log::error("User already Exists");
            }

            $affiliateUser = User::create([
                'name' => $name,
                'email' => $email,
                'type' => User::TYPE_AFFILIATE,
            ]);

            $discountCode = $this->apiService->createDiscountCode($merchant);

            $affiliate = $this->affiliate->create([
                'user_id' => $affiliateUser->id,
                'merchant_id' => $merchant->id,
                'commission_rate' => $commissionRate,
                'discount_code' => $discountCode['code'],
            ]);

            return $affiliate;
        }catch(\Exception $e){
            Log::error("Exception in Affiliate Service:". $e->getMessage());
        }
    }
    public function getAffiliate($email): ?User
    {
        return  User::where('email',$email)->first();
    }
}
