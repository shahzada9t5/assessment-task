<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function __construct(
        protected AffiliateService $affiliateService
    ) {
        $this->affiliateService = $affiliateService;
    }

    /**
     * Process an order and log any commissions.
     * This should create a new affiliate if the customer_email is not already associated with one.
     * This method should also ignore duplicates based on order_id.
     *
     * @param  array{order_id: string, subtotal_price: float, merchant_domain: string, discount_code: string, customer_email: string, customer_name: string} $data
     * @return void
     */
    public function processOrder(array $data)
    {
        // TODO: Complete this method
        try {
            if (!$this->getOrderById($data['order_id'])) {
                $email = $data['customer_email'];
                $domain = $data['merchant_domain'];

                $affiliate = Affiliate::whereHas('user', function ($q) use ($email) {
                    $q->where('email', $email);
                })->whereHas('merchant', function ($q) use ($domain) {
                    q->where('domain', $domain);
                })->first();

                if (!$affiliate) {
                    $merchant = Merchant::where('domain', $domain)->first();
                    $affiliate = $this->affiliateService->register($merchant, $data['customer_email'], $data['customer_name'], 0.1);
                }

                $order = Order::create([
                    'external_order_id' => $data['order_id'],
                    'subtotal' => $data['subtotal_price'],
                    'affiliate_id' => $affiliate->id,
                    'merchant_id' => $affiliate->merchant_id,
                    'commission_owed' => $data['subtotal_price'] * $affiliate->commission_rate,
                ]);
            }
        }catch (\Exception $e){
            Log::error("Exception in Order Service:". $e->getMessage());
        }
    }

    private function getOrderById($orderId){
        return Order::find($orderId);
    }

}
