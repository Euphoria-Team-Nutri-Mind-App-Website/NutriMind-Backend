<?php
namespace App\Repository\Authentication;


use App\Models\Patient;
use App\Notifications\OTP;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Validation\Rules\Password;
use App\Http\Requests\PatientLoginRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Interfaces\Authentication\PatientAuthRepositoryInterface;

class PatientAuthRepository implements PatientAuthRepositoryInterface
{
    use ImageTrait;  // Store image
    public function register(Request $request) {

        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request->file('image'), 'images/profileImages');
        };

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.Patient::class],
            'password' => ['required', 'confirmed','min:8',Password::defaults()],
            'height' => ['required','integer'],
            'weight' => ['required','integer'],
            'age' => ['required','integer'],
            'gender' => ['required', 'string'],
            'credit_card_number' => ['required', 'string'],
            'active_status' => ['required'],
        ]);
        //create Patient
        $patient = Patient::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'image' => $imagePath,
            'height' => $request->height,
            'weight' => $request->weight,
            'age' => $request->age,
            'gender' => $request->gender,
            'credit_card_number' => $request->credit_card_number,
            'active_status' => $request->active_status,
        ]);

        //create token
        $token = $patient->createToken('patient_token');

        return response([
            'status' => true,
            'message' => 'Registered Successfully!',
            $token,
            $patient,
        ]);
    }

    public function login(PatientLoginRequest $request) {

        $patient = Patient::where('email' , $request->email)->first();
        //check if patient is not found or password not matched with password in DB
        if (!$patient|| !Hash::check($request->password, $patient->password))
        {
            return response([
                'status' => false,
                'message' => 'Email or Password may be wrong, please try again'
            ]);
        }

        //create token
        $token = $patient->createToken('patient_token');

        return response([
            'status' => 'True',
            'message' => 'LoggedIn Successfully!',
            $token,
            $patient,
        ]);
    }

    public function logout() {

        if(Auth::guard('patient')->check()){
            $accessToken = Auth::guard('patient')->user()->token();

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

    public function generateOTP(Request $request){
        $patient = Patient::where('email',$request->email)->first();
        $patient->generateOtpCode(); //send otp code
        return response([
            'OTP-Code' => $patient->verfication_code
        ]);
    }

    public function resetPassword(Request $request){
        $patient = Patient::where('email',$request->email)->first();
        // $patient->notify(new OTP());

        $request->validate([
            'password' => ['required', 'confirmed','min:8',Password::defaults()],
            'verfication_code' => 'required',
        ]);

        if($request->verfication_code == $patient->verfication_code){
            $patient -> update([
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

    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $file_name = $this->saveImage($request->image, 'images/profileImages');

        //create Patient
        $patient -> update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'image' => $imagePath,
        ]);

        //create token
        $token = $patient->createToken('patient_token');

        return response([
            'status' => true,
            'message' => 'Profile information has been updated successfully',
            $token,
            $patient
        ]);
    }

    public function handleProviderCallback($provider)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }
        try {
            $patient = Socialite::driver($provider)->stateless()->user();
        } catch (ClientException $exception) {
            return response([
                'status' => true,
                'message' => 'Invalid credentials provided'
            ]);
        }

        $patientCreated = Patient::firstOrCreate(
            [
                'email' => $patient->getEmail()
            ],
            [
                'email_verified_at' => now(),
                'name' => $patient->getName(),
                'status' => true,
            ]
        );
        $patientCreated->providers()->updateOrCreate(
            [
                'provider_name' => $provider,
                'provider_id' => $patient->getId(),
            ],
            [
                'avatar' => $patient->getAvatar()
            ]
        );
        $token = $patientCreated->createToken('token-name')->plainTextToken;

        return response([
            'status' => true,
            $patientCreated,
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
