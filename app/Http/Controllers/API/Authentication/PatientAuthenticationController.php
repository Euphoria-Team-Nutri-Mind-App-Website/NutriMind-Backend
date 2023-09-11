<?php

namespace App\Http\Controllers\API\Authentication;

use App\Models\Patient;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Requests\PatientLoginRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Interfaces\Authentication\PatientAuthRepositoryInterface;

class PatientAuthenticationController extends Controller
{
    private $patientAuthRepository;

    public function __construct(PatientAuthRepositoryInterface $patientAuthRepository)
    {
        $this->patientAuthRepository = $patientAuthRepository;
    }

//------------------------------Default Authentication Methods----------------------------------//

    // PatientRegisterRequest contain registration rules for patient
    public function register(Request $request) {
        return $this->patientAuthRepository->register($request);
    }

    public function getWeight(Request $request) {
        return $this->patientAuthRepository->getWeight($request);
    }

    public function getHeight(Request $request) {
        return $this->patientAuthRepository->getHeight($request);
    }

    public function getActiveStatus(Request $request) {
        return $this->patientAuthRepository->getActiveStatus($request);
    }

    public function login(PatientLoginRequest $request) {
        return $this->patientAuthRepository->login($request);
    }

    public function logout() {
        return $this->patientAuthRepository->logout();
    }

    public function generateOTP(Request $request){
        return $this->patientAuthRepository->generateOTP($request);
    }

    public function verifyOTP(Request $request){
        return $this->patientAuthRepository->verifyOTP($request);
    }

    public function resetPassword(Request $request){
        return $this->patientAuthRepository->resetPassword($request);
    }

//------------------------------End Default Authentication Methods----------------------------------//






//------------------------------Authentication By Social Network Methods--------------------------------//

    //Redirect Patient to the Provider authentication page
    public function redirectToProvider($provider){
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }

        return Socialite::driver($provider)->stateless()->redirect();
    }

    //Get Patient information from Provider.
    public function handleProviderCallback($provider)
    {
        return $this->patientAuthRepository->handleProviderCallback($provider);
    }

    protected function validateProvider($provider)
    {
        return $this->patientAuthRepository->validateProvider($provider);
    }

//------------------------------End Authentication By Social Network Methods--------------------------------//






//------------------------------Profile Methods--------------------------------//

public function show() {
    $patient = Patient::where('id' , Auth::id())->get(['name','email','image']);
    return response([
        'status' => true,
        'patient information' => $patient
    ]);
}

    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        return $this->patientAuthRepository->update($request,$patient);
    }

    public function destroy(){
        $patient = Patient::where('id' , Auth::id());
        $patient->delete();
        return response([
            'status' => true,
            'mesaage' => 'Your account has been deleted'
        ]);
    }
//------------------------------End Profile Methods--------------------------------//


}
