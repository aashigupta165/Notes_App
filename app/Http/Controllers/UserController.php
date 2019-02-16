<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Auth\Events\Registered;
use App\Jobs\SendVerificationEmail;

class UserController extends Controller
{
    public $successStatus = 200;
    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            if ($user->email_verified_at != null){
            $success['token'] =  $user->createToken('MyApp')-> accessToken;
            return response()->json(['success' => $success], $this-> successStatus);
        }
        else{
            return response()->json(['email not verified' => 'fail']);
        }
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['email_token'] = str_random(15);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')-> accessToken ;
        $success['name'] =  $user->name;
        //event(new Registered($user = $this->create($request->all())));
        dispatch(new SendVerificationEmail($user));
        //return view('Verification');
        return response()->json(['You have successfully registered. An email is sent to you for verification.'=>$success], $this-> successStatus);
    }
    public function verify($token)
    {
        $user = User::where('email_token', $token)->first();
        $user->email_verified_at = date('Y-m-d h:i:s');
        if ($user->save()) {
            return response()->json('Your Email is successfully verified.');
        }
    }
    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this-> successStatus);
    }
    public function logoutApi()
    {
        if (Auth::check()) {
            Auth::user()->AauthAcessToken()->delete();
        }
    
    }
    public function updateuser(Request $request, $id){
        if ($request->hasFile('image')) {
            // $request->file('image');
            $validate = Validator::make($request->all(), [
                'image' => 'mimes:jpeg,png,bmp,tiff |max:4096',
            ]);
            if($validate->fails()){
                return response()->json(['error'=>$validate->errors()], 401);
            }
            else{
            $filename = $request->image->getClientOriginalName();
            $filesize = $request->image->getClientSize();
            $request->image->storeAs('public/upload',$filename);
            $file = User::find($id);
            $file->imagename = $filename;
            $file->imagesize = $filesize;
            $file->save();
        }
        }
        return $request->all();
    }

    public function userimagedelete($id){
        $file = User::find($id);
        $file->imagename = null;
        $file->imagesize = null;
        $file->save();
    }

}
