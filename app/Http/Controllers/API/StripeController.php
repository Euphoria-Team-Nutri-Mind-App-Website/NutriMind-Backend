<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DoctorBankMoney;
use App\Models\Stripe;
use Illuminate\Http\Request;
use Exception;
use Stripe\StripeClient;
use Stripe\Exception\CardException;

class StripeController extends Controller
{
    public function index()
    {
        return view('stripe.index');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'amount' => 'required|numeric',
            'payment_method' => 'required',
            'email' => 'required|email',
        ]);

        try {
            $stripe = new StripeClient(env('STRIPE_SECRET'));

            // Find or create DoctorBankMoney record
            $doctorBankMoney = DoctorBankMoney::where('doctor_id', $validatedData['doctor_id'])->first();
            if ($doctorBankMoney) {
                $doctorBankMoney->update([
                    'doctor_money' => ($doctorBankMoney->money + $validatedData['amount'])
                ]);
            } else {
                DoctorBankMoney::create([
                    'doctor_id' => $validatedData['doctor_id'],
                    'doctor_money' => $validatedData['amount']
                ]);
            }

            // Create payment intent
            // Create payment intent
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $validatedData['amount'],
                'currency' => 'USD',
                'payment_method' => $validatedData['payment_method'],
                'description' => 'Demo payment with Stripe',
                'confirm' => true,
                'receipt_email' => $validatedData['email'],
                'return_url' => 'http://127.0.0.1:8000/api/payment',
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never'
                ]
            ]);

            // Save the payment intent ID to the database
            $payment = Stripe::create([
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $validatedData['amount'],
                'currency' => 'USD',
                'doctor_id' => $validatedData['doctor_id']
            ]);

            return $payment->id;
        } catch (CardException $e) {
            throw new Exception("There was a problem processing your payment", 1);
        }
    }
}
