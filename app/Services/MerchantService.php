<?php

namespace App\Services;

use App\Jobs\PayoutOrderJob;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class MerchantService
{
    /**
     * Register a new user and associated merchant.
     * Hint: Use the password field to store the API key.
     * Hint: Be sure to set the correct user type according to the constants in the User model.
     *
     * @param array{domain: string, name: string, email: string, api_key: string} $data
     * @return Merchant
     */
    public function register(array $data): Merchant
    {
        try {
            // TODO: Complete this method
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['api_key'],
                'type' => User::TYPE_MERCHANT
            ]);

            return Merchant::create([
                'domain' => $data['domain'],
                'user_id' => $user->id,
                'display_name' => $data['name']
            ]);
        }catch(\Exception $e){
            Log::error("Merchant Service Exception: ". $e->getMessage());
        }
    }

    /**
     * Update the user
     *
     * @param array{domain: string, name: string, email: string, api_key: string} $data
     * @return void
     */
    public function updateMerchant(User $user, array $data)
    {
        // TODO: Complete this method

        $merchant = $user->merchant;

        if (!$merchant) {
            return;
        }

        $merchant->domain=$data['domain'];
        $merchant->display_name=$data['name'];
        if($merchant->save()) {
            $user->email = $data['email'];
            $user->name = $data['name'];
            $user->password = $data['api_key'];
            $user->save();
        }
        return;
    }

    /**
     * Find a merchant by their email.
     * Hint: You'll need to look up the user first.
     *
     * @param string $email
     * @return Merchant|null
     */
    public function findMerchantByEmail(string $email): ?Merchant
    {
        // TODO: Complete this method

        $merchant = Merchant::whereHas('user', function ($q) use ($email) {
            $q->where('email', $email);
        })->first();

        return $merchant;
    }

    /**
     * Pay out all of an affiliate's orders.
     * Hint: You'll need to dispatch the job for each unpaid order.
     *
     * @param Affiliate $affiliate
     * @return void
     */
    public function payout(Affiliate $affiliate)
    {
        // TODO: Complete this method
        try{
            $unpaidOrders = Order::where('affiliate_id', $affiliate->id)
                ->where('payout_status', Order::STATUS_UNPAID)
                ->get();

            foreach ($unpaidOrders as $order) {
                dispatch(new PayoutOrderJob($order));
            }
        }catch (\Exception $e) {
            Log::error("Order Payout Failed :". $e->getMessage());
        }
    }
}
