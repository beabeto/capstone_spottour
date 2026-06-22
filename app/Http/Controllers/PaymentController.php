<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([

            'booking_id' => 'required',

            'receipt' => 'required|image'

        ]);

        $imageName =
            time().'.'.
            $request->receipt->extension();

        $request->receipt->move(

            public_path('uploads/payments'),

            $imageName

        );

        Payment::create([

            'booking_id'
                => $request->booking_id,

            'receipt_image'
                => $imageName,

            'amount'
                => $request->amount,

            'status'
                => 'pending'

        ]);

        return back()->with(
            'success',
            'Receipt uploaded successfully.'
        );
    }
}