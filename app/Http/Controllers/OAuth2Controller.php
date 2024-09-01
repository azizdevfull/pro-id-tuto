<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OAuth2Controller extends Controller
{
    public function redirectToProId($isMobileOrWeb): JsonResponse|Redirector|Application|RedirectResponse
    {
        // info('salom');
        $client_id_map = [
            'web_client' => '41',
        ];

        $client_id = $client_id_map[$isMobileOrWeb] ?? '';
        $callbackUri = "http://localhost:8000/api/oauth/proid/$isMobileOrWeb/callback";

        if (!empty($client_id) && !empty($callbackUri)) {
            $query = http_build_query([
                'client_id' => $client_id,
                'redirect_uri' => $callbackUri,
                'response_type' => 'code',
                'scope' => '',
                'state' => 'abcdabcdabcdabcdabcd',
                'prompt' => 'consent',
            ]);
            return redirect('http://localhost:5173/oauth?' . $query);
        } else {
            return response()->json([
                'message' => __('errors client does not have permission')
            ]);
        }
    }

    public function handleProIdCallback($isMobileOrWeb, Request $request): JsonResponse|Redirector|Application|RedirectResponse
    {
        info($request->all());
        // dd($request->all());
        $clientData = [
            'web_client' => [
                'client_id' => 41,
                'client_secret' => 'Nx12OHTMWAU3c2gzXVmzzGOtSpJTn2Sj9SLuXyak',
            ],
        ];

        $clientSecret = $clientData[$isMobileOrWeb]['client_secret'];
        $clientId = $clientData[$isMobileOrWeb]['client_id'];
        $callbackUri = "http://localhost:8000/api/oauth/proid/$isMobileOrWeb/callback";
        // dd($request->code);
        //getting user data
        $response = Http::asForm()->post('https://newmoto.uz/api/v2/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $callbackUri,
            'code' => $request->code,
        ]);

        $responseData = $response->json();
        info($responseData);
        $accessToken = $responseData['access_token'];

        $userRes = Http::withToken($accessToken)->get('https://newmoto.uz/api/user');

        $userData = $userRes->json();
        // dd($userData);

        $provider = 'proid';

        $userRole = '';


        return response()->json($userData);
    }
}
