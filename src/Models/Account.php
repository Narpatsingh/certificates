<?php

namespace Sushil\Certificate\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = "google_accounts";

    protected $fillable = ["name"];

    public static function rules ($id=null){
        return [
        	"name" => 'required',
       ];
    }


    public function user(){
        return $this->belongsTo('Sushil\Certificate\Models\User','user_id','id');
    }
		
	public static function getGoogleClient($id){
		//GET ACCESS TOKEN FROM DATABASE
		$account = Account::find($id);

		$client = new \Google_Client();
		$client->setApplicationName(env('GOOGLE_CLIENT_SECRET'));
		$client->setClientId(env('GOOGLE_CLIENT_ID'));
		$client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));

		$client->setRedirectUri(route('newaccount'));
		$client->setAccessType ("offline");
		$client->setApprovalPrompt ("force");

		$client->setScopes(array(
			'https://www.googleapis.com/auth/presentations.readonly',
			'https://www.googleapis.com/auth/spreadsheets',
			'https://www.googleapis.com/auth/drive',
			'https://www.googleapis.com/auth/userinfo.profile',
		));
													
		$client->setAccessToken($account->access_token);
		// Refresh the token if it's expired.
		if ($client->isAccessTokenExpired()) {
				$refreshToken = $client->getRefreshToken();
				$client->fetchAccessTokenWithRefreshToken($refreshToken);
				$accessToken = $client->getAccessToken();
				$accessToken['refresh_token'] = $refreshToken;
				Account::where('id', $id)->update(['access_token' => json_encode($accessToken)]);
				$client->setAccessToken($accessToken);
		}
		return $client;
	}
}
