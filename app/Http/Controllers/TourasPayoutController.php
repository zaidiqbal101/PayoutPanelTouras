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
        Log::info('Add Beneficiary Request Data:', $request->all());

        $url = "https://neodev2.touras.in/agWalletAPI/Contact/createContactAPI";

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

        $nonEncryptedPayload = json_encode($payloadData);
        $encryptedPayload = AESHelper::encrypt($nonEncryptedPayload, $this->key, $this->iv);

        Log::info('Add Beneficiary Encrypted Payload:', ['payload' => $encryptedPayload]);

        $requestJson = [
            'payload' => $encryptedPayload,
            'uId' => $request->me_id ?? 'AGEN5500134316',
            'iv' => base64_encode($this->iv),
        ];

        $response = $this->makeCurlRequest($url, $requestJson);

        if (isset($response['error'])) {
            Log::error('cURL Error in addBeneficiary:', ['error' => $response['error']]);
            return response()->json(['error' => $response['error']], 500);
        }

        $responseData = json_decode($response, true);

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
        $encryptedPayload = AESHelper::encrypt($nonEncryptedPayload, $this->key, $this->iv);

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
        Log::info('Get Beneficiary List Request Data:', $request->all());

        $url = "https://neodev2.touras.in/agWalletAPI/v2/agg";

        $payloadData = [
            'header' => [
                'operatingSystem' => $request->operatingSystem ?? 'WEB',
                'sessionId' => $request->sessionId ?? 'AGEN5500134316',
                'version' => $request->version ?? '1.0.0',
            ],
            'transaction' => [
                'requestType' => 'WTW',
                'requestSubType' => 'BENEL',
                'id' => $request->id ?? 'AGEN5500134316',
                'tranCode' => (int)($request->tranCode ?? 0),
                'txnAmt' => (float)($request->txnAmt ?? 0.0),
                'surChargeAmount' => (float)($request->surChargeAmount ?? 0.0),
                'txnCode' => (int)($request->txnCode ?? 0),
                'userType' => (int)($request->userType ?? 0),
            ],
        ];

        $nonEncryptedPayload = json_encode($payloadData);
        $encryptedPayload = AESHelper::encrypt($nonEncryptedPayload, $this->key, $this->iv);

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

        $responseData = json_decode($response, true);

        if ($responseData && isset($responseData['responseData'])) {
            $decryptedResponse = AESHelper::decrypt($responseData['responseData'], $this->key, $this->iv);
            $responseData['decrypted_response'] = $decryptedResponse === "Error" ? null : $decryptedResponse;
            Log::info('Get Beneficiary List Decrypted Response:', ['decrypted_response' => $responseData['decrypted_response']]);
        }

        return response()->json([
            'status' => 'sent',
            'response_raw' => $response,
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