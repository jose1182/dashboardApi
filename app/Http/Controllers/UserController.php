<?php   
namespace App\Http\Controllers;

    use App\Models\User;
    use App\Models\SocialProfile;
    use App\Models\Widget;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;
    use JWTAuth;
    use Tymon\JWTAuth\Exceptions\JWTException;
    use Laravel\Socialite\Facades\Socialite;

class UserController extends Controller
{
    public function authenticate(Request $request)
    {
    $credentials = $request->only('email', 'password');
    try {
        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'invalid_credentials'], 400);
        } 
        if( JWTAuth::user()->email_verified_at === null){
            return response()->json(['error' => 'email not verify'], 400);
        }



    } catch (JWTException $e) {
        return response()->json(['error' => 'could_not_create_token'], 500);
    }

    //get user authenticated
    $user = JWTAuth::user();

    //get widgets of user
    $widget = Widget::where('user_id',"=", $user->id)->get();

    return response()->json(compact('user', 'token', 'widget'));
    }

    public function getAuthenticatedUser()
    {
    try {
        if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
        }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
                return response()->json(['token_absent'], $e->getStatusCode());
        }
        return response()->json(compact('user'));
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if($validator->fails()){
                return response()->json(
                    [
                        'errors' => $validator->errors()
                    ], 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        //sending email verificvation
        $user->sendEmailVerificationNotification();

        //send message "email verification send"
        return response()->json(['message' => 'email send'],201);

        //$token = JWTAuth::fromUser($user);
        //return response()->json(compact('user','token'),201);
    }

    //Obtain user information from socialize
    public function redirectToProvider($driver){
        $drivers = ['facebook', 'google'];

        if(!in_array($driver, $drivers)){
            return response()->json(['error' => $drivers . 'is not supported to login'], 400);
        } 

        return Socialite::driver($driver)->redirect();
    }

    //Obtain user information from socialize
    public function handleProviderCallback(Request $request, $driver){


        if($request->get('error')){
            return response()->json(['error' => 'Something was wrong'], 400);
        }

        $userSocialite = Socialite::driver($driver)->user();
        
        $social_profile = SocialProfile::where('social_id', $userSocialite->getId())
                                        ->where('social_name', $driver)->first();


        if(!$social_profile){

            $user = User::where('email', $userSocialite->getEmail())->first();

            if(!$user){
                $user = User::create([
                    'name' => $userSocialite->getName(),
                    'email'=> $userSocialite->getEmail(),
                    'password' => Hash::make('password'),
                ]);
            }

            $social_profile = SocialProfile::create([
                'user_id' => $user->id,
                'social_id' => $userSocialite->getId(),
                'social_name' => $driver,
                'social_avatar'  => $userSocialite->getAvatar()
            ]);      
        }

        // get user authenticated
        $user = User::find($social_profile->user->id);


        // get token
        $token = JWTAuth::fromUser($social_profile->user);

            //get widgets of user
        $widget = Widget::where('user_id',"=", $user->id)->get();

        
        return response()->json(compact('user', 'token', 'widget'));
        
    }
}