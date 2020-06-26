<?php

namespace Sushil\Certificate\Http\Controllers\Adminarea;

use Illuminate\Http\Request;
use Sushil\Certificate\Models\Account;
use Cortex\Auth\DataTables\Adminarea\AdminsDataTable;
use Cortex\Foundation\Http\Controllers\AuthenticatedController;
use Validator;
use Session;

class AccountsController extends AuthenticatedController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AdminsDataTable $adminsDataTable)
    {
    	$conditions[] = array('user_id', auth()->user()->id);
		$accounts = Account::where($conditions)->orderby('created_at', 'desc')->paginate(12);
		return view('sushil/makegui::adminarea.pages.account.index',compact('accounts'));
        return $adminsDataTable->with([
            'id' => 'adminarea-members-index-table',
            'accounts'=>$accounts,
        ])->render('sushil/makegui::adminarea.pages.account.index');
    }
		/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $account = Account::findOrFail($id);
        $account->delete();
        return redirect(route('accounts.index'));
    }
		public function newaccount(Request $request)  {
				$googleClient = new \Google_Client();
				
				$googleClient->setApplicationName(env('GOOGLE_CLIENT_SECRET'));
				$googleClient->setClientId(env('GOOGLE_CLIENT_ID'));
				$googleClient->setClientSecret(env('GOOGLE_CLIENT_SECRET'));

				$googleClient->setRedirectUri(route('newaccount'));
				$googleClient->setAccessType ("offline");
				$googleClient->setApprovalPrompt ("force");

				$googleClient->setScopes(array(
															'https://www.googleapis.com/auth/presentations.readonly',
															'https://www.googleapis.com/auth/spreadsheets',
															'https://www.googleapis.com/auth/drive',
															'https://www.googleapis.com/auth/userinfo.profile',
														));
				$google_oauthV2 = new \Google_Service_Oauth2($googleClient);
				if ($request->get('code')){
						$googleClient->authenticate($request->get('code'));
						$request->session()->put('token', $googleClient->getAccessToken());
				}
				if ($request->session()->get('token'))
				{
						$googleClient->setAccessToken($request->session()->get('token'));
				}
				if ($googleClient->getAccessToken())
				{	
						$guser = $google_oauthV2->userinfo->get();  
						if(empty($guser)){
							Session::flash('success',  __('Unable to add Google Account. Please try again'));
							return redirect(route('accounts.index'));
						}
						
						// check for already has account
						$account = Account::where('google_id',$guser->getId())->where('user_id',auth()->user()->id)->first();
						
						//GET Refresh Token and save it with Access token to autogenerate access_token once it expire
						$accessToken = $googleClient->getAccessToken();
						$accessToken['refresh_token'] = $googleClient->getRefreshToken();

						// if user already found
						if( $account ) {
							// update the avatar and provider that might have changed
							$account->update([
									'name' => $guser->getName(),
									'avatar' => $guser->getPicture(),
									'access_token' => json_encode($accessToken)
							]);
							Session::flash('success',  __('Google Account has been updated successfully.'));
						} else {
							$account = new Account;
							$account->user_id 	= auth()->user()->id;
							$account->name 			= $guser->getName();
							$account->google_id = $guser->getId();
							$account->avatar 		= $guser->getPicture();
							$account->access_token = json_encode($accessToken);							
							$account->save();
							Session::flash('success',  __('New Google Account added successfully.'));
						}	
            return redirect(route('accounts.index'));
				} else
				{
						//For Guest user, get google login url
						$authUrl = $googleClient->createAuthUrl();
						return redirect()->to($authUrl);
				}
		}
		public function getfilefolder($googleAccountId){
				$googleClient = Account::getGoogleClient($googleAccountId);
				$service = new \Google_Service_Drive($googleClient);
				$optParams = array(
					// 'pageSize' => 100,
					'q' => '"root" in parents', 
					'orderBy' => 'modifiedTime',
					'fields' => 'files',
					'fields' => 'nextPageToken, files(id, name, mimeType,fileExtension, parents)',
					);
					
				$results = $service->files->listFiles($optParams);

				//$results = $service->files->get('root');

				echo "<pre>";
				print_r($results);
				echo "</pre>";


				echo '<table class="table table-sm table-striped table-bordered" style="margin-top: 20px;">';
				echo '<thead>';
				echo '<tr><th>#</th><th>Name</th><th>Extension </th><th> Type </th><th>ID</th></tr>';
				echo '</thead>';
				echo '<tbody>';
				$s = 1;
				foreach ($results as $file) {
				//    if(!is_array($file->getparents())){
					echo '<tr><td>'.$s.'</td><td>'.$file->getName().'</td><td>'.$file->getFileExtension().'</td><td>'.$file->getMimeType().'</td><td>'.$file->getId().'</td></tr>';
					$s++;
					//}
				}
				die;
				echo '</tbody>';
				echo '</table>';

				exit;

		}
		public function getaccesstoken(Request $request){
			$googleClient = Account::getGoogleClient($request->accountId);
			echo "<pre>";
			print_r($googleClient );exit;
			if(!empty($googleClient)){
				$account = Account::find($request->accountId);
				if(!empty($account)){
					$access_token = json_decode($account->access_token);
					return response()->json([
								'status' => 'success',
								'token' => isset($access_token->access_token)?$access_token->access_token:""
						]);				
				}
			}	
			return response()->json([
					'status' => 'fail',
					'message' => "Unable to load data, Please reload page once again and try it again."
			]);
		}
		public function processfile(Request $request){

			$googleClient = Account::getGoogleClient($request->accountId);					
			if(!empty($request->slideId) && !empty($request->sheetId)){		
						
				$slidesService = new \Google_Service_Slides($googleClient);
				$presentationId = $request->slideId;
				$presentation = $slidesService->presentations->get($presentationId);
				$slides = $presentation->getSlides();
				$slideContent = "";
				foreach ($slides as $i => $slide) {
						$elements = $slide->getPageElements();
						foreach ($elements as $element) {
							$textElements = $element->shape->text->textElements;
							foreach ($textElements as $text) {
								if(isset($text->textRun))	
									$slideContent = $slideContent." ".$text->textRun->content;
							}
						}
				}
				preg_match_all('/\[\[.*?\]\]/', $slideContent, $match,PREG_PATTERN_ORDER);
				$arrSlide = array_unique($match[0]);
				
				//CODE TO READ SHEET
				$sheetsService = new \Google_Service_Sheets($googleClient);
				$sheetId = $request->sheetId;
				// get the sheet[0]
				$spreadSheet = $sheetsService->spreadsheets->get($sheetId);
				$sheets = $spreadSheet->getSheets();
				$titleSheet=$sheets[0]['properties']['title'];
				

				$dataRangeNotation = $titleSheet;
				$sheetsResponse = $sheetsService->spreadsheets_values->get($sheetId,$dataRangeNotation);
				$values = $sheetsResponse['values'];
				$arrSheet = array_unique($values[0]);
				//if(!empty($match[0]) && !empty($values[0])){
				//}
				return response()->json([
						'status' => 'success',
						'slide' => array_values($arrSlide),
						'sheet' => array_values($arrSheet)
				]);				
			}
			// return response()->json([
			// 		'status' => 'success',
			// 		'slide' => array(0=>"[[first_name]]",1=>"[[last_name]]",2=>"[[email]]",3=>"[[technology]]",4=>"[[company_name]]"),
			// 		'sheet' => array("first_name","last_name","email","technology","company_name")
			// ]);
		}
}
