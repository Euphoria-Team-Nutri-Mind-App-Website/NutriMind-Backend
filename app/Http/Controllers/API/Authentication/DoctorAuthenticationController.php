<?php

namespace App\Http\Controllers\API\Authentication;


use App\Models\Doctor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Requests\DoctorLoginRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Interfaces\Authentication\DoctorAuthRepositoryInterface;

class DoctorAuthenticationController extends Controller
{

    private $doctorAuthRepository;

    public function __construct(DoctorAuthRepositoryInterface $doctorAuthRepository)
    {
        $this->doctorAuthRepository = $doctorAuthRepository;
    }

//------------------------------Default Authentication Methods----------------------------------//

    // DoctorRegisterRequest contain registration rules for Doctor
    public function register(Request $request) {
        return $this->doctorAuthRepository->register($request);
    }

    public function setWorkTime(Request $request) {
        return $this->doctorAuthRepository->setWorkTime($request);
    }

    public function login(DoctorLoginRequest $request) {

        return $this->doctorAuthRepository->login($request);
    }

    public function logout() {
        return $this->doctorAuthRepository->logout();
    }

    public function generateOTP(Request $request){
        return $this->doctorAuthRepository->generateOTP($request);
    }

    public function verifyOTP(Request $request){
        return $this->doctorAuthRepository->verifyOTP($request);
    }

    public function resetPassword(Request $request){
        return $this->doctorAuthRepository->resetPassword($request);
    }

//------------------------------End Default Authentication Methods----------------------------------//






//------------------------------Authentication By Social Network Methods--------------------------------//

    //Redirect Doctor to the Provider authentication page
    public function redirectToProvider($provider){
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }

        return Socialite::driver($provider)->stateless()->redirect();
    }

    //Get Doctor information from Provider.
    public function handleProviderCallback($provider)
    {
        return $this->doctorAuthRepository->handleProviderCallback($provider);
    }

    protected function validateProvider($provider)
    {
        return $this->doctorAuthRepository->validateProvider($provider);
    }

//------------------------------End Authentication By Social Network Methods--------------------------------//







//------------------------------Profile Methods--------------------------------//

public function show() {
    $doctor = Doctor::where('id' , Auth::id())->get(['name','email','image','national_id']);
    return response([
        'status' => true,
        'doctor information' => $doctor
    ]);
}

    public function update(UpdateDoctorRequest $request, Doctor $doctor)
    {
        return $this->doctorAuthRepository->update($request,$doctor);
    }

    public function destroy(){
        $doctor = Doctor::where('id' , Auth::id());
        $doctor->delete();
        return response([
            'status' => true,
            'mesaage' => 'Your account has been deleted'
        ]);
    }

//------------------------------End Profile Methods--------------------------------//



}
