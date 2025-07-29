<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\AESHelper;

class PayoutController extends Controller
{
    public function send(Request $request)
    {
        
        $key = env('AES_SECRET'); // base64-encoded
        $iv = "0123456789abcdef";
        $url = "https://neodev2.touras.in/agWalletAPI/v2/agg";

        $payloadData = [
            'header' => [
                'operatingSystem' => $request->operatingSystem,
                'sessionId' => $request->sessionId,
                'version' => $request->version,
            ],
            'userInfo' => [],
            'transaction' => [
                'requestType' => $request->requestType,
                'requestSubType' => $request->requestSubType,
                'tranCode' => $request->tranCode,
                'txnAmt' => $request->txnAmt,
                'id' => $request->id,
            ],
        ];

        // JSON encode and encrypt the payload
        $nonEncryptedPayload = json_encode($payloadData);
        // dd($nonEncryptedPayload);
        $encryptedPayload = AESHelper::encrypt($nonEncryptedPayload, $key, $iv);
        // dd($encryptedPayload);
        // Final request body
        $requestJson = [
            'agId' => 'AGG6210019097',
            'uId' => 'AGEN4430031130',
            'payload' => $encryptedPayload
        ];

        // Initialize cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestJson));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        // Execute request
        $response = curl_exec($ch);
        // dd($response);
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return response()->json(['error' => $error], 500);
        }

        curl_close($ch);

        // Optionally decode and return response
        return response()->json([
            'status' => 'sent',
            'response_raw' => $response,
            'response_json' => json_decode($response, true)
        ]);
    }

    public function decryptPayload(Request $request)
    {
        $key = env('AES_SECRET');
        $iv = "0123456789abcdef";

        $encrypted = $request->payload;
        $decrypted = AESHelper::decrypt($encrypted, $key, $iv);

        return response()->json([
            'decrypted' => $decrypted
        ]);
    }
}
