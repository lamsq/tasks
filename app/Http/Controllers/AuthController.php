<?php
   
namespace App\Http\Controllers;
   
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
   
class AuthController extends Controller
{
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];  
        return response()->json($response, 200);
    }
  
    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    #public function sendError($error, $errorMessages = [], $code = 404)
    #{
    #    $response = [
    #        'success' => false,
    #        'message' => $error,
    #    ];
    #    if(!empty($errorMessages))
    #        $response['data'] = $errorMessages;      
    #    return response()->json($response, $code);
    #}


    public function sendError($error, $errorMessages = [], $code = 400): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'message' => $error,
            'data' => $errorMessages
        ], $code);
    }
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required'            
        ]);
   
        if($validator->fails())
            return $this->sendError('Authentication Error', $validator->errors());       
           
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user -> createToken('MyApp') ->plainTextToken;
        $success['name'] =  $user -> name;   
        return $this->sendResponse($success, 'User registered');
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request): JsonResponse
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $request->user()->createToken('MyApp')->plainTextToken; 
            $success['name'] =  $user->name;   
            return $this->sendResponse($success, 'User logged in');
        } 
        else
            return $this->sendError('Unauthorised', ['error'=>'Unauthorised']);
    }
}