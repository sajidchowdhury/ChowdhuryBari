<?php

namespace App\Http\Controllers\Payment;

use App\Models\Central\Payment;
use Illuminate\Http\Request;

/**
 * Contract for all payment gateway adapters (SSL Commerz, bKash, Nagad).
 *
 * Each gateway must implement these 3 methods. The PaymentService
 * will instantiate the right adapter based on user choice and call
 * these methods uniformly.
 *
 * Usage:
 *   $gateway = app(PaymentGateway::class, ['gateway' => 'sslcommerz']);
 *   $redirectUrl = $gateway->initiatePayment($order);
 *   // ... user pays at gateway ...
 *   // gateway sends webhook → handleWebhook() → verifies and updates Payment
 */
interface PaymentGateway
{
    /**
     * Create a payment session at the gateway and return the redirect URL
     * where the user completes payment.
     *
     * @param mixed $payable Order|Subscription|DuesPayment — anything payable
     * @return string Redirect URL to the gateway's payment page
     */
    public function initiatePayment($payable): string;

    /**
     * Verify a payment's status by querying the gateway's API.
     * Used when webhook delivery is delayed or for manual verification.
     *
     * @param string $paymentId The Payment model's ID or the gateway's payment ID
     * @return Payment The refreshed Payment model with updated status
     */
    public function verifyPayment(string $paymentId): Payment;

    /**
     * Handle the gateway's webhook/IPN callback.
     * Validates the payload signature, updates the Payment record's
     * status, and triggers any side-effects (e.g. mark Order as paid).
     *
     * @param Request $request The raw webhook request
     * @return Payment The updated Payment model
     */
    public function handleWebhook(Request $request): Payment;
}
