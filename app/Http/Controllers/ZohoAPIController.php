<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ZohoAPIController extends Controller
{
    /**
     * @throws \Exception
     */
    public function createAccount(){
        $response = Http::withToken(cache('access_token'))
            ->get(env('API_DOMAIN') . '/crm/v2/users');
        $owner_id = $response->json('users')[0]['id'];
        $response = Http::withToken(cache('access_token'))->post(env('API_DOMAIN') . '/crm/v2/Accounts', ['data'=>[
            'Owner'=>['id'=>$owner_id],
            'Parent_Account'=>null,
            'Account_Name'=>'Test account'
            ]
        ]);
        dd($response->json());
    }

    public function createCampaign(){
        $response = Http::withToken(cache('access_token'))->asJson()->post(env('API_DOMAIN') . '/crm/v2/Campaigns', ['data'=>[
            'Campaign_Name'=>'test'
            ]
        ]);
        dd($response->json());
    }

    /**
     * @throws \Exception
     */
    public function createAuthToken(Request $request){
        if(cache('refresh_token')){
            $response = Http::asForm()->post(env('ACCOUNTS_URL') . '/oauth/v2/token',[
                'client_id'=>env('CLIENT_ID'),
                'client_secret'=>env('CLIENT_SECRET'),
                'refresh_token'=>cache('refresh_token'),
                'grant_type'=>'refresh_token',
            ]);
            Cache::put('access_token',$response->json('access_token'),$response->json('expires_in'));
            return response()->json([
                'Status'=>'Found existing refresh token, new access token was created',
                'access_token'=>Cache::get('access_token'),
                'refresh_token'=>Cache::get('refresh_token'),
                ]);
        }else{
            if(!$request->code){
                abort(401);
            }else{
                $response = Http::asForm()->post(env('ACCOUNTS_URL') . '/oauth/v2/token',[
                    'client_id'=>env('CLIENT_ID'),
                    'client_secret'=>env('CLIENT_SECRET'),
                    'code'=>$request->code,
                    'grant_type'=>'authorization_code',
                ]);
                dump($response->json());
                Cache::put('access_token',$response->json('access_token'),$response->json('expires_in'));
                Cache::forever('refresh_token',$response->json('refresh_token'));
                return response()->json([
                    'Status'=>'Created new access and refresh tokens',
                    'access_token'=>Cache::get('access_token'),
                    'refresh_token'=>Cache::get('refresh_token'),
                ]);
            }
        }
    }
}
