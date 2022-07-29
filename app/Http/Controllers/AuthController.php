<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected function customLogin(Request $request)
    {
        $input = $request->all();

        if(auth()->attempt(array('email' => $input['email'], 'password' => $input['password'])))
        {
            if (auth()->user()->role == 'ADMIN') {
                $notification = array(
                    'message' => 'Login successfully!',
                    'alert-type' => 'success'
                );
                return redirect()->route('dashboard')
                    ->with($notification);
            }
            $notification = array(
                'message' => 'Forbidden!',
                'alert-type' => 'error'
            );
            return redirect()->back()
                ->with($notification);
        }
        $notification = array(
            'message' => 'Invalid Email Or Password!',
            'alert-type' => 'error'
        );
        return redirect()->back()
            ->with($notification);
        
    }

    public function login()
    {
        return view('auth.login');
    }
}
