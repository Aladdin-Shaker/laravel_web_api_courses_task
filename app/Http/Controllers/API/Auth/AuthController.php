<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\ApiController;
use App\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Passport\Client as OClient;
use Illuminate\Support\Facades\Route;

class AuthController extends ApiController
{

    use AuthenticatesUsers;

    // check the user role first and grant the scope based on that role
    /*  protected function authenticated(Request $request, $user)
    {
        // implement your user role retrieval logic, for example retrieve from `roles` database table
        $role = $user->roles();

        // grant scopes based on the role that we get previously
        if ($role == 'admin') {
            $request->request->add([
                'scope' => 'admin' // grant admin scope for user with admin role
            ]);
        } elseif ($role == 'teacher') {
            $request->request->add([
                'scope' => 'teacher' // grant teacher scope for user with teacher role
            ]);
        } else {
            $request->request->add([
                'scope' => 'student' // student scope for other user role
            ]);
        }

        // forward the request to the oauth token request endpoint
        $tokenRequest = Request::create(
            '/oauth/token',
            'post'
        );
        return Route::dispatch($tokenRequest);
    } */

    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @param  [string] image (nullable)
     * @return [string] message
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'image' => 'image|mimes:jpeg,png,gif,webp|max:2048'
        ]);

        $imageName = '';

        if ($request->has('image')) {
            $imageName = time() . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('img'), $imageName);
        }

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'verified' =>  User::UNVERIFIED_USER,
            'image' => $imageName
        ]);
        $user->save();
        $tokenResult = $user->createToken($user->email . '-' . now(), ['student']);
        $token = $tokenResult->token;
        $token->save();
        return response()->json([
            'data' => $user,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'message' => 'Successfully created user!',
        ], 201);
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        $user = $request->user();
        $role = $request->user()->roles()->first();
        if ($role) {
            $this->scope = $role->role;
        }

        $tokenResult = $user->createToken($user->email . '-' . now(), [$this->scope]);
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return $this->showMessage('Successfully logged out');
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function details(Request $request)
    {
        return $this->showOne($request->user());
    }

    /**
     * Get OClient
     *
     * @return
     */
    public function getOClient()
    {
        return OClient::where('password_client', 1)->first();
    }
}
