<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth;
use App\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        return View('dashboard');
    }

    public function loginPage()
    {
        return View('auth.login');
    }

    public function authenticate(Request $request)
    {
        $this->validate($request, [
            'email'     => 'required|email',
            'password'  => 'required|min:4'
        ]);

        $userdata = [
            'email'     => $request->email,
            'password'  => $request->password
        ];

        if(Auth::attempt($userdata)) {
            $getUserType = User::select('userType')->where('status', '0')->where('id', Auth::id())->first();
            if($getUserType)
            {
                return redirect()->intended('dashboard');
            }
            \Session::flash('error', "Account deacivated, Please contact to admin.");
            return redirect()->back()->withInput();
        }
        else
        {
            \Session::flash('error', "Username and password do not matched, Please try again.");
            return redirect()->back()->withInput();
        }
    }

    public function profile()
    {
        $user = User::find(Auth::id());
        return view('edit-profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $this->validate($request, [
            'name'          => ['required', 'string', 'max:191'],
            'mobile'        => ['required', 'numeric', 'digits_between:9,12'],
            'address'       => ['required', 'string', 'max:191'],
            'city'          => ['required', 'string', 'max:191'],
            'locktimeout'   => ['required', 'numeric','min:10','max:120']
        ]);

        $user = User::find(Auth::id());
        $user->name         = $request->name;
        $user->companyname  = $request->companyname;
        $user->phone        = $request->mobile;
        $user->address1     = $request->address;
        $user->city         = $request->city;
        $user->locktimeout  = $request->locktimeout;
        $user->save();
        if($user)
        {
            notify()->success('Success, Profile setting successfully changed.');
        }
        else
        {
            notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        }
        return redirect()->back();

    }

    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'old_password'              => ['required'],
            'new_password'              => ['required', 'confirmed', 'min:6', 'max:25'],
            'new_password_confirmation' => ['required']
        ]);

        $matchpassword  = User::find(Auth::id())->password;
        if(\Hash::check($request->old_password, $matchpassword))
        {
            $user = User::find(Auth::id());
            $user->password = bcrypt($request->new_password);
            $user->save();
            notify()->success('Success, Password successfully changed.');
        }
        else
        {
            notify()->error('Incorrect password, Please try again with correct password.');
        }
        return redirect()->back();
    }

    public function screenlock($currtime,$id,$randnum)
    {
      Auth::logout();
      return View('admin.screenlock')->with('currtime', $currtime)->with('id', $id)->with('randnum',$randnum);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->away('.');
    }
}
