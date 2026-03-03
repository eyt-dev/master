<?php

namespace App\Traits;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait RegistersAdmins
{
    use \Illuminate\Foundation\Auth\RegistersUsers;

    public function showRegistrationForm()
    {
        $countries = \App\Models\CountryRegion::orderBy('name')->get();
        return view('auth.register', compact('countries'));
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
                    ? new JsonResponse([], 201)
                    : redirect($this->redirectPath());
    }

    protected function registered(Request $request, $user)
    {
        // Custom logic after registration if needed
    }
}