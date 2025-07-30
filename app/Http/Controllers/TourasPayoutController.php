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
            'me_id' => $request->me_id ?? 'AGEN4430031130',
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
        // dd($encryptedPayload);

        // Log::info('Add Beneficiary Encrypted Payload:', ['payload' => $encryptedPayload]);

        $requestJson = [
            'payload' => $encryptedPayload,
            // 'uId' => $request->me_id ?? 'AGEN4430031130',
            'uId' => 'AGEN4430031130',
        ];
        // dd($requestJson);

        $response = $this->makeCurlRequest($url, $requestJson);
        
        // $responseData['responseData'] = 'qbV8/7PGlffOIYkSlNB26o17iD4pJxqthM+BzbecLhn4A7gzZN3EIkLAxJ40kwZLNjl3BQu9qYcz5N2bwz+ygO3ni/XnNbP40ok//Mm7iZP4JCHDMIGzvAPDe0Ox+mWOBp0bo41HslfctsJjoMTf/fmF1DS9iGgjy3aRr0BmuWwaZRCD81TU5lJaxoBJKF9AbVrkjYbb1jjW4QPUZ/5HMb0yDdPQEIuVQCSeFCOvUJQ8wNfnZXcqg/6jXewjKjEtP317RX0UY33jB/ExfiTrqERxQ1IfC8qvv5dX3sQxl5ACIz1UieLgM19viLBg7SgYt7vKTzUG69DcJ3bgo79/KQ\u003d\u003d';

        if (isset($response['errorMessage'])) {
            Log::error('cURL Error in addBeneficiary:', ['error' => $response['errorMessage']]);
            return response()->json(['error' => $response['errorMessage']]);
        }

        // $responseData = json_decode($response, true);
        // $responseData['responseData'] ='yEszeCGTQYOS60G2yL0iIy7+gNoCxmagIVcpaALwYMM0IW6pgfvL717s/rzXG4ej6Hr08Zv2LR4eX4W/pnMV/KTEtocSVrOHbp6IP92B2ZupYWwAeYCXAGWd8a1tAI/jntyNtmnF91PIaiIJuscfmCM4gRGiKWcnAioys+eOijHswyu+b1ukztjjS2rxLH/Fy/dqDJS946g3Fv31OC3e7zlXbyFT4YASotp9OhwJTfWtwaJYsA2hw9eTY8/hBEV8gx53TvNnr6Upj1ir/EobS+nLcsNgDWzxcQmh8+NaTXoWo3bk/hAiW3FgZQ6uc4/5hGt8NWnkJ25KmiIwO1f8BIE0vWZn9zWO+xnn8yYWugWo5m7wiWYbSa2hN28uGw/JKoIA6IneqqxpY8Drqsr1kpKNZewFPmzbhmxMM1taE08Dqts/LZ8yZXeynANtN64bKwSNnFbedTOeoXr6PB3mXNY9KfKYPikOjUgdZytUMpnJWm3rcevEq7TfTE+c3EoJvC2yKAiizdzgblTgJBaX6MOYIKw3Tyh0HPs7Nf8qT9AI6Z6g4nKuYB093M8Is37CYgprbSeEayDnzg3qJVglVcp1sH7sSLJr8WxyIpqXAjItpsYmU71slEVoH2XWdn/ZGF2AZqsHYiac42qRuWw4spjr9DYkAXM98N7O55sEXHYbKGlu6a465Qv+um0r0rhP5xMYaxeUa5FPYukLO2MO3W8gNpOGq4TPghpcylVsLeLGoq9Ee2so65T5uT2xLGQ2N6AejArBN9QZZeV/9KVh2mR6kNGDIbNJx6CKyVIAN9bDbxHoXpqi04ea+Z5RwCpjTC/Q/kLZZiPVer/onda6SgZyyFf1lmhh3g0sWRiiA6NGjNH5nrJnMlgMrMNvRtngOAF81Tp5xbE7Gf1qjjUqhbRzaYOZCPXDkEfSMSc5zlGJYrsoui0/1oAKn0l4cgiZDI+6SpTB/yAWX97/ipkJuZDI6TYNRcUxcWFcLDZ9dBUXyeJNx0uC0BtaN+fuR6zS68H6bFn5u73WNNgoaNOHdjrIE1j4T/VLgXQSwK71PXlIwdAQfqf/9YllCVGarNgA+xZEUu23Ar7i8RScFOjHPhMrcCapZbSzXJn29d9XBEHNWcS4iFwKYOehK7LwBmGgIVe8kf9vcoWqQ7RFLYgh4aYKuxuJWq8BTzJ2H376reYRyH0wrFjQG4ixcHbdLLkegPICAoiBbBVffjsBHGYoIJII0zq0UaHVA5Us4FhbqcvoY7P9IzBV7T5ncdQ6kQCipW+gdZPdn572b4TCDm38BQ==';

        if (isset($response['responseData']) || isset($response['userMessage'])) {
            dd($response);
            $decryptedResponse = AESHelper::decrypt($responseData['responseData'], $key);
            $array = json_decode($decryptedResponse, true);
            dd($array);
            // $responseData['decrypted_response'] = $decryptedResponse === "Error" ? null : $decryptedResponse;
            // Log::info('Add Beneficiary Decrypted Response:', ['decrypted_response' => $responseData['decrypted_response']]);
        }

        return response()->json([
            'status' => 'sent',
            'response_raw' => $response,
            'response_json' => $response,
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
        $key = 'b5QH4QP9rACLYLs0x+4DWnod27LmdD9V453AGVKCMOg=';
        $nonEncryptedPayload = json_encode($payloadData);
        $encryptedPayload = AESHelper::encrypt($nonEncryptedPayload, $key);
        // dd($encryptedPayload);

        Log::info('Payout With Beneficiary Encrypted Payload:', ['payload' => $encryptedPayload]);

        $requestJson = [
            'payload' => $encryptedPayload,
            'uId' => $request->uId ?? 'AGEN4430031130',
            // 'iv' => base64_encode($this->iv),
        ];

        $response = $this->makeCurlRequest($url, $requestJson);
        // dd($response);

        if (isset($response['error'])) {
            Log::error('cURL Error in payoutWithBene:', ['error' => $response['error']]);
            return response()->json(['error' => $response['error']], 500);
        }

        // $responseData = json_decode($response, true);

        if ($response && isset($response['payload'])) {
            $decryptedResponse = AESHelper::decrypt($response['payload'], $key);
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

        $url = "https://neodev2.touras.in/agWalletAPI/v2/agg";
        // $url = "https://neodev2.touras.in/agWalletAPI/Contact/createContactAPI";

        $payloadData = [
            'header' => [
                'operatingSystem' => 'WEB',
                'sessionId' => 'AGEN4430031130',
                'version' => '1.0.0',
            ],
            'transaction' => [
                'requestType' => 'WTW',
                'requestSubType' => 'BENEL',
                'id' => 'AGEN4430031130',
            ],
        ];

        $key = 'b5QH4QP9rACLYLs0x+4DWnod27LmdD9V453AGVKCMOg=';

        $nonEncryptedPayload = json_encode($payloadData);
        $encryptedPayload = AESHelper::encrypt($nonEncryptedPayload, $key);
        // dd($encryptedPayload);  

        Log::info('Get Beneficiary List Encrypted Payload:', ['payload' => $encryptedPayload]);

        $requestJson = [
            'payload' => $encryptedPayload,
            'uId' => $request->id ?? 'AGEN4430031130 ',
            // 'iv' => base64_encode($this->iv),
        ];

        $response = $this->makeCurlRequest($url, $requestJson);
        // dd($response['payload']);

        if (isset($response['error'])) {
            Log::error('cURL Error in getBeneList:', ['error' => $response['error']]);
            return response()->json(['error' => $response['error']], 500);
        }

        // $responseData = json_decode($response, true);
        // dd($response);
        $responseData['responseData'] = $response['payload'];
        // $responseData['responseData1'] = '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';

        if ($responseData && isset($responseData['responseData'])) {
            $decryptedResponse = AESHelper::decrypt($responseData['responseData'], $key);
            $array = json_decode($decryptedResponse, true); 
            // dd($array);
            // $responseData['decrypted_response'] = $decryptedResponse === "Error" ? null : $decryptedResponse;
            // Log::info('Get Beneficiary List Decrypted Response:', ['decrypted_response' => $responseData['decrypted_response']]);
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

    // protected function makeCurlRequest($url, $requestJson)
    // {
    //     Log::info('cURL Request:', ['url' => $url, 'data' => $requestJson]);

    //     $ch = curl_init($url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestJson));
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, [
    //         'Content-Type: application/json'
    //     ]);

    //     $response = curl_exec($ch);
    //     dd($response);

    //     if (curl_errno($ch)) {
    //         $error = curl_error($ch);
    //         curl_close($ch);
    //         Log::error('cURL Error:', ['error' => $error]);
    //         return ['error' => $error];
    //     }

    //     curl_close($ch);
    //     return $response;
    // } by zaid

    protected function makeCurlRequest($url, $requestJson)
    {
        Log::info('Sending cURL Request:', ['url' => $url, 'payload' => $requestJson]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestJson));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);
        // dd($response);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            Log::error('cURL Error:', ['error' => $error]);
            return ['error' => $error];
        }

        curl_close($ch);

        Log::info('API Response:', ['response' => $response]);

        return json_decode($response, true) ?? $response;
    }

}