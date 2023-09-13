<?php
namespace App\Repository\Authentication;


use App\Models\Patient;
use App\Notifications\OTP;
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
    public function register(Request $request) {

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.Patient::class],
            'password' => ['required', 'confirmed','min:8',Password::defaults()],
            'age' => ['required','integer'],
            'gender' => ['required', 'string'],
        ]);
        //create Patient
        $patient = Patient::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'age' => $request->age,
            'gender' => $request->gender,
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

    public function getWeight(Request $request){
        $patient = Patient::find(Auth::user()->id);

        $request->validate([
            'first_weight' => ['required','integer'],
        ]);

        $first_weight = $request->input('first_weight');
        $patient->first_weight = $first_weight;
        $patient->save();
    }

    public function getHeight(Request $request){
        $patient = Patient::find(Auth::user()->id);

        $request->validate([
            'height' => ['required','integer'],
        ]);

        $height = $request->input('height');
        $patient->height = $height;
        $patient->save();
    }

    public function getActiveStatus(Request $request){
        $patient = Patient::find(Auth::user()->id);

        $request->validate([
            'active_status' => ['required','string'],
        ]);

        $active_status = $request->input('active_status');
        $patient->active_status = $active_status;
        $patient->save();
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
        $patient = Auth::guard('patient')->user();

        if ($patient) {
            $accessToken = $patient->token();

            if ($accessToken) {
                DB::table('oauth_refresh_tokens')
                    ->where('access_token_id', $accessToken->id)
                    ->update(['revoked' => true]);

                $accessToken->revoke();
            }
            return response([
                'status' => true,
                'message' => 'Logged out successfully',
            ]);
        }
    }

    public function generateOTP(Request $request){
        $patient = Patient::where('email', $request->email)->first();
        if (!$patient) {
            return response([
                'message' => 'There is no account with this email',
            ]);
        }

        $patient->generateOtpCode(); //send otp code
        $patient->notify(new OTP());
            return response([
                'OTP-Code' => $patient->verfication_code
            ]);
    }

    public function verifyOTP(Request $request){
        $patient = Patient::where('email', $request->email)->first();

        $request->validate([
            'verfication_code' => 'required',
        ]);

        if ($request->verfication_code == $patient->verfication_code) {
            return response([
                'status' => true,
                'message' => 'Correct verification code',
            ]);
        } else {
            return response([
                'status' => false,
                'message' => 'Your verification code is incorrect',
            ]);
        }
    }

    public function resetPassword(Request $request){
        $patient = Patient::where('email',$request->email)->first();

        $request->validate([
            'password' => ['required', 'confirmed','min:8',Password::defaults()],
        ]);

        $patient -> update([
            'password' => Hash::make($request->password),
            'verfication_code' => null, // Clear the verification code after successful reset
        ]);
        return response([
            'status' => true,
            'message' => 'Your password has been changed'
        ]);
    }

    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        //create Patient
        $patient -> update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
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
