<?php
namespace App\Repository\Authentication;


use App\Models\Doctor;
use App\Notifications\OTP;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Requests\DoctorLoginRequest;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Validation\Rules\Password;
use App\Http\Requests\UpdateDoctorRequest;
use App\Interfaces\Authentication\DoctorAuthRepositoryInterface;


class DoctorAuthRepository implements DoctorAuthRepositoryInterface
{
    use ImageTrait;  // Store image

    public function register(Request $request) {

        if ($request->hasFile('image')) {
            $path = $this->uploadImage($request->file('image'), 'images/profileImages');
        };


        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.Doctor::class],
            'password' => ['required', 'confirmed','min:8',Password::defaults()],
            'phone' => ['required', 'string'],
            'price' => ['required', 'integer'],
            'national_id' => ['required', 'string'],
            'qualification' => ['required', 'string'],
            'experience_years' => ['required', 'integer'],
            'gender' => ['required', 'string'],
            'credit_card_number' => ['required', 'integer'],
        ]);

        //create doctor
        $doctor = Doctor::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'image' => $path,
            'phone' => $request->phone,
            'price' => $request->price,
            'national_id'=> $request->national_id,
            'qualification' => $request->qualification,
            'experience_years' => $request->experience_years,
            'gender' => $request->gender,
            'credit_card_number' => $request->credit_card_number,
        ]);

        //create token
        $token = $doctor->createToken('doctor_token');

        return response([
            'status' => true,
            'message' => 'Registered Successfully',
            $token,
            $doctor
        ]);
    }

    public function login(DoctorLoginRequest $request) {

        $doctor = Doctor::where('email' , $request->email)->first();

        //check if doctor is not found or password not matched with password in DB
        if (!$doctor|| !Hash::check($request->password, $doctor->password))
        {
            return response([
                'status' => false,
                'message' => 'Email or Password may be wrong, please try again'
            ]);
        }

        //create token
        $token = $doctor->createToken('doctor_token');

        return response([
            'status' => true,
            'message' => 'LoggedIn Successfully',
            $token,
            $doctor
        ]);
    }

    public function logout() {

        if(Auth::guard('doctor')->check()){
            $accessToken = Auth::guard('doctor')->user()->token();

                DB::table('oauth_refresh_tokens')
                    ->where('access_token_id', $accessToken->id)
                    ->update(['revoked' => true]);
            $accessToken->revoke();
        return response([
            'status' => true,
            'mesaage' => 'Logged out sucsessfully'
        ]);
        }
    }

    public function resetPassword(Request $request){
        $doctor = Doctor::where('email',$request->email)->first();
        $doctor->generateOtpCode(); //send otp code

        $doctor->notify(new OTP());

        $request->validate([
            'password' => ['required', 'confirmed','min:8',Password::defaults()]
        ]);

        if($request->verfication_code == $doctor->verfication_code){
            $doctor -> update([
                'password' => Hash::make($request->password),
            ]);
            return response([
                'status' => true,
                'message' => 'Your password has been changed'
            ]);
        }
        else{
            return response([
                'status' => true,
                'message' => 'Your verification code not correct'
            ]);
        }
    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor)
    {
        $file_name = $this->uploadImage($request->image, 'images/profileImages');

        //create Doctor
        $doctor -> update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'image' => $file_name,
            'phone' => $request->phone,
        ]);

        //create token
        $token = $doctor->createToken('doctor_token');

        return response([
            'status' => true,
            'message' => 'Profile information has been updated successfully',
            $token,
            $doctor
        ]);
    }

    //Get Doctor information from Provider.
    public function handleProviderCallback($provider)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }
        try {
            $doctor = Socialite::driver($provider)->stateless()->user();
        } catch (ClientException $exception) {
            return response([
                'status' => true,
                'message' => 'Invalid credentials provided'
            ]);
        }

        $doctorCreated = Doctor::firstOrCreate(
            [
                'email' => $doctor->getEmail()
            ],
            [
                'email_verified_at' => now(),
                'name' => $doctor->getName(),
                'status' => true,
            ]
        );
        $doctorCreated->providers()->updateOrCreate(
            [
                'provider_name' => $provider,
                'provider_id' => $doctor->getId(),
            ],
            [
                'avatar' => $doctor->getAvatar()
            ]
        );
        $token = $doctorCreated->createToken('token-name')->plainTextToken;

        return response([
            'status' => true,
            $doctorCreated,
            $token
        ]);
    }

    public function validateProvider($provider)
    {
        if (!in_array($provider, ['facebook', 'apple', 'google'])) {
            return response([
                'status' => true,
                'message' => 'Please login using facebook, apple or google'
            ]);
        }
    }

}
