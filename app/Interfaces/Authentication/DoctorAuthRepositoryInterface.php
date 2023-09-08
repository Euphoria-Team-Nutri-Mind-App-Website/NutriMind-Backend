<?php
namespace App\Interfaces\Authentication;

use App\Models\Doctor;
use Illuminate\Http\Request;
use App\Http\Requests\DoctorLoginRequest;
use App\Http\Requests\UpdateDoctorRequest;



interface DoctorAuthRepositoryInterface
{
    public function register(Request $request);

    public function login(DoctorLoginRequest $request);

    public function logout();

    public function resetPassword(DoctorLoginRequest $request);

    public function update(UpdateDoctorRequest $request, Doctor $doctor);

    public function handleProviderCallback($provider);

    public function validateProvider($provider);

}
