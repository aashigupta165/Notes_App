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