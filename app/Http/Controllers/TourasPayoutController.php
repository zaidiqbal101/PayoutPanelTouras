<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\AESHelper;
use Illuminate\Support\Facades\Log;

class TourasPayoutController extends Controller
{
    protected $key;
    protected $iv;

    public function __construct()
    {
        $this->key = env('AES_SECRET'); // Base64-encoded key
        $this->iv = openssl_random_pseudo_bytes(16); // Random IV for better security
    }

    public function addBeneficiary(Request $request)
    {
        // dd($request->all());
        Log::info('Add Beneficiary Request Data:', $request->all());

        $url = "https://neodev2.touras.in/agWalletAPI/Contact/createContactAPI";
        // $url =       "https://neodev2.touras.in/agWalletAPI/v2/agg";

        $payloadData = [
            'contact_type' => $request->contact_type ?? 'Vendor',
            'name' => $request->name ?? '',
            'org_name' => $request->org_name ?? '',
            'email_id' => $request->email_id ?? '',
            'mobile_no' => $request->mobile_no ?? '',
            'me_id' => $request->me_id ?? 'AGEN5500134316',
            'banks' => [
                [
                    'account_no' => $request->account_no ?? '',
                    'ifsc_code' => $request->ifsc_code ?? '',
                    'category_type' => 'BANK',
                    'account_holder_name' => $request->account_holder_name ?? '',
                    'code' => $request->code ?? '',
                ]
            ],
            'pan_no' => $request->pan_no ?? '',
            'registration_type' => $request->registration_type ?? 'Consumer',
            'gst_no' => $request->gst_no ?? '',
            'notes' => $request->notes ?? '',
        ];

        // $jsonPayload = json_encode($payloadData); 
        // dd($jsonPayload);

        $nonEncryptedPayload = json_encode($payloadData, JSON_UNESCAPED_UNICODE);
        // dd($nonEncryptedPayload);
        $key = 'b5QH4QP9rACLYLs0x+4DWnod27LmdD9V453AGVKCMOg=';
       
        $encryptedPayload = AESHelper::encrypt($nonEncryptedPayload, $key);
        dd($encryptedPayload);

        Log::info('Add Beneficiary Encrypted Payload:', ['payload' => $encryptedPayload]);

        $requestJson = [
            'payload' => $encryptedPayload,
            'uId' => $request->me_id ?? 'AGEN5500134316',
            // 'iv' => '0123456789abcdef',
        ];
        // dd($requestJson);

        $response = $this->makeCurlRequest($url, $requestJson);

        if (isset($response['error'])) {
            Log::error('cURL Error in addBeneficiary:', ['error' => $response['error']]);
            return response()->json(['error' => $response['error']], 500);
        }

        // $responseData = json_decode($response, true);
        // $responseData['responseData'] ='yEszeCGTQYOS60G2yL0iIy7+gNoCxmagIVcpaALwYMM0IW6pgfvL717s/rzXG4ej6Hr08Zv2LR4eX4W/pnMV/KTEtocSVrOHbp6IP92B2ZupYWwAeYCXAGWd8a1tAI/jntyNtmnF91PIaiIJuscfmCM4gRGiKWcnAioys+eOijHswyu+b1ukztjjS2rxLH/Fy/dqDJS946g3Fv31OC3e7zlXbyFT4YASotp9OhwJTfWtwaJYsA2hw9eTY8/hBEV8gx53TvNnr6Upj1ir/EobS+nLcsNgDWzxcQmh8+NaTXoWo3bk/hAiW3FgZQ6uc4/5hGt8NWnkJ25KmiIwO1f8BIE0vWZn9zWO+xnn8yYWugWo5m7wiWYbSa2hN28uGw/JKoIA6IneqqxpY8Drqsr1kpKNZewFPmzbhmxMM1taE08Dqts/LZ8yZXeynANtN64bKwSNnFbedTOeoXr6PB3mXNY9KfKYPikOjUgdZytUMpnJWm3rcevEq7TfTE+c3EoJvC2yKAiizdzgblTgJBaX6MOYIKw3Tyh0HPs7Nf8qT9AI6Z6g4nKuYB093M8Is37CYgprbSeEayDnzg3qJVglVcp1sH7sSLJr8WxyIpqXAjItpsYmU71slEVoH2XWdn/ZGF2AZqsHYiac42qRuWw4spjr9DYkAXM98N7O55sEXHYbKGlu6a465Qv+um0r0rhP5xMYaxeUa5FPYukLO2MO3W8gNpOGq4TPghpcylVsLeLGoq9Ee2so65T5uT2xLGQ2N6AejArBN9QZZeV/9KVh2mR6kNGDIbNJx6CKyVIAN9bDbxHoXpqi04ea+Z5RwCpjTC/Q/kLZZiPVer/onda6SgZyyFf1lmhh3g0sWRiiA6NGjNH5nrJnMlgMrMNvRtngOAF81Tp5xbE7Gf1qjjUqhbRzaYOZCPXDkEfSMSc5zlGJYrsoui0/1oAKn0l4cgiZDI+6SpTB/yAWX97/ipkJuZDI6TYNRcUxcWFcLDZ9dBUXyeJNx0uC0BtaN+fuR6zS68H6bFn5u73WNNgoaNOHdjrIE1j4T/VLgXQSwK71PXlIwdAQfqf/9YllCVGarNgA+xZEUu23Ar7i8RScFOjHPhMrcCapZbSzXJn29d9XBEHNWcS4iFwKYOehK7LwBmGgIVe8kf9vcoWqQ7RFLYgh4aYKuxuJWq8BTzJ2H376reYRyH0wrFjQG4ixcHbdLLkegPICAoiBbBVffjsBHGYoIJII0zq0UaHVA5Us4FhbqcvoY7P9IzBV7T5ncdQ6kQCipW+gdZPdn572b4TCDm38BQ==';

        if ($responseData && isset($responseData['responseData'])) {
            $decryptedResponse = AESHelper::decrypt($responseData['responseData'], $this->key, $this->iv);
            $responseData['decrypted_response'] = $decryptedResponse === "Error" ? null : $decryptedResponse;
            Log::info('Add Beneficiary Decrypted Response:', ['decrypted_response' => $responseData['decrypted_response']]);
        }

        return response()->json([
            'status' => 'sent',
            'response_raw' => $response,
            'response_json' => $responseData,
        ]);
    }

    public function payoutWithBene(Request $request)
    {
        Log::info('Payout With Beneficiary Request Data:', $request->all());

        $url = "https://neodev2.touras.in/agWalletAPI/v2/agg";

        $payloadData = [
            'header' => [
                'operatingSystem' => $request->operatingSystem ?? 'WEB',
                'sessionId' => $request->sessionId ?? 'AGEN5500134316',
                'version' => $request->version ?? '1.0.0',
            ],
            'userInfo' => [],
            'transaction' => [
                'requestType' => 'WTW',
                'requestSubType' => 'PBENE',
                'tranCode' => (int)($request->tranCode ?? 0),
                'txnAmt' => (float)($request->txnAmt ?? 0.0),
                'surChargeAmount' => (float)($request->surChargeAmount ?? 0.0),
                'txnCode' => (int)($request->txnCode ?? 0),
                'userType' => (int)($request->userType ?? 0),
            ],
            'payOutBean' => [
                'mobileNo' => $request->mobileNo ?? '',
                'txnAmount' => $request->txnAmount ?? '',
                'beneId' => $request->beneId ?? '',
                'count' => (int)($request->count ?? 0),
                'orderRefNo' => $request->orderRefNo ?? '',
                'payMode' => $request->payMode ?? 'IMPS',
            ],
        ];

        $nonEncryptedPayload = json_encode($payloadData);
        $encryptedPayload = AESHelper::encrypt($nonEncryptedPayload, 'b5QH4QP9rACLYLs0x+4DWnod27LmdD9V453AGVKCMOg=');
        dd($encryptedPayload);

        Log::info('Payout With Beneficiary Encrypted Payload:', ['payload' => $encryptedPayload]);

        $requestJson = [
            'payload' => $encryptedPayload,
            'uId' => $request->uId ?? 'AGEN5500134316',
            'iv' => base64_encode($this->iv),
        ];

        $response = $this->makeCurlRequest($url, $requestJson);

        if (isset($response['error'])) {
            Log::error('cURL Error in payoutWithBene:', ['error' => $response['error']]);
            return response()->json(['error' => $response['error']], 500);
        }

        $responseData = json_decode($response, true);

        if ($responseData && isset($responseData['responseData'])) {
            $decryptedResponse = AESHelper::decrypt($responseData['responseData'], $this->key, $this->iv);
            $responseData['decrypted_response'] = $decryptedResponse === "Error" ? null : $decryptedResponse;
            Log::info('Payout With Beneficiary Decrypted Response:', ['decrypted_response' => $responseData['decrypted_response']]);
        }

        return response()->json([
            'status' => 'sent',
            'response_raw' => $response,
            'response_json' => $responseData,
        ]);
    }

    public function getBeneList(Request $request)
    {
        // dd($request->all());
        Log::info('Get Beneficiary List Request Data:', $request->all());

        // $url = "https://neodev2.touras.in/agWalletAPI/v2/agg";
        $url = "https://neodev2.touras.in/agWalletAPI/Contact/createContactAPI";

        $payloadData = [
            'header' => [
                'operatingSystem' => $request->operatingSystem ?? 'WEB',
                'sessionId' => $request->sessionId ?? 'AGEN4430031130',
                'version' => '1.0.0',
            ],
            'transaction' => [
                'requestType' => 'WTW',
                'requestSubType' => 'BENEL',
                'id' => $request->id ?? 'AGEN4430031130 ',
                // 'tranCode' => (int)($request->tranCode ?? 0),
                // 'txnAmt' => (float)($request->txnAmt ?? 0.0),
                // 'surChargeAmount' => (float)($request->surChargeAmount ?? 0.0),
                // 'txnCode' => (int)($request->txnCode ?? 0),
                // 'userType' => (int)($request->userType ?? 0),
            ],
        ];
        $key = 'b5QH4QP9rACLYLs0x+4DWnod27LmdD9V453AGVKCMOg=';

        $nonEncryptedPayload = json_encode($payloadData);
        $encryptedPayload = AESHelper::encrypt($nonEncryptedPayload, $key);
        // dd($encryptedPayload);

        Log::info('Get Beneficiary List Encrypted Payload:', ['payload' => $encryptedPayload]);

        $requestJson = [
            'payload' => $encryptedPayload,
            'uId' => $request->id ?? 'AGEN5500134316',
            'iv' => base64_encode($this->iv),
        ];

        $response = $this->makeCurlRequest($url, $requestJson);

        if (isset($response['error'])) {
            Log::error('cURL Error in getBeneList:', ['error' => $response['error']]);
            return response()->json(['error' => $response['error']], 500);
        }

        // $responseData = json_decode($response, true);
        $responseData['responseData'] = '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';

        if ($responseData && isset($responseData['responseData'])) {
            $decryptedResponse = AESHelper::decrypt($responseData['responseData'], $key);
            $array = json_decode($decryptedResponse, true); 
            // dd($array);
            // $responseData['decrypted_response'] = $decryptedResponse === "Error" ? null : $decryptedResponse;
            Log::info('Get Beneficiary List Decrypted Response:', ['decrypted_response' => $responseData['decrypted_response']]);
        }

        return response()->json([
            'status' => 'sent',
            'response_raw' => $array,
            'response_json' => $responseData,
        ]);
    }

    public function payoutWithoutBene(Request $request)
    {
        Log::info('Payout Without Beneficiary Request Data:', $request->all());

        $url = "https://neodev2.touras.in/agWalletAPI/v2/agg";

        $payloadData = [
            'header' => [
                'operatingSystem' => $request->operatingSystem ?? 'WEB',
                'sessionId' => $request->sessionId ?? 'AGEN5500134316',
                'version' => $request->version ?? '1.0.0',
            ],
            'userInfo' => [],
            'transaction' => [
                'requestType' => 'WTW',
                'requestSubType' => 'PWTB',
                'tranCode' => (int)($request->tranCode ?? 0),
                'txnAmt' => (float)($request->txnAmt ?? 0.0),
                'id' => $request->id ?? 'AGEN5500134316',
                'surChargeAmount' => (float)($request->surChargeAmount ?? 0.0),
                'txnCode' => (int)($request->txnCode ?? 0),
                'userType' => (int)($request->userType ?? 0),
            ],
            'payOutBean' => [
                'mobileNo' => $request->mobileNo ?? '',
                'txnAmount' => $request->txnAmount ?? '',
                'accountNo' => $request->accountNo ?? '',
                'ifscCode' => $request->ifscCode ?? '',
                'bankName' => $request->bankName ?? '',
                'accountHolderName' => $request->accountHolderName ?? '',
                'txnType' => $request->txnType ?? 'IMPS',
                'accountType' => $request->accountType ?? 'Saving',
                'emailId' => $request->emailId ?? '',
                'orderRefNo' => $request->orderRefNo ?? '',
                'count' => (int)($request->count ?? 0),
            ],
        ];

        $nonEncryptedPayload = json_encode($payloadData);
        $encryptedPayload = AESHelper::encrypt($nonEncryptedPayload, $this->key, $this->iv);

        Log::info('Payout Without Beneficiary Encrypted Payload:', ['payload' => $encryptedPayload]);

        $requestJson = [
            'payload' => $encryptedPayload,
            'uId' => $request->id ?? 'AGEN5500134316',
            'iv' => base64_encode($this->iv),
        ];

        $response = $this->makeCurlRequest($url, $requestJson);

        if (isset($response['error'])) {
            Log::error('cURL Error in payoutWithoutBene:', ['error' => $response['error']]);
            return response()->json(['error' => $response['error']], 500);
        }

        $responseData = json_decode($response, true);

        if ($responseData && isset($responseData['responseData'])) {
            $decryptedResponse = AESHelper::decrypt($responseData['responseData'], $this->key, $this->iv);
            $responseData['decrypted_response'] = $decryptedResponse === "Error" ? null : $decryptedResponse;
            Log::info('Payout Without Beneficiary Decrypted Response:', ['decrypted_response' => $responseData['decrypted_response']]);
        }

        return response()->json([
            'status' => 'sent',
            'response_raw' => $response,
            'response_json' => $responseData,
        ]);
    }

    protected function makeCurlRequest($url, $requestJson)
    {
        Log::info('cURL Request:', ['url' => $url, 'data' => $requestJson]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestJson));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            Log::error('cURL Error:', ['error' => $error]);
            return ['error' => $error];
        }

        curl_close($ch);
        return $response;
    }
}