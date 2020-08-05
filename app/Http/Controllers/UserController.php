<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;
use DB;

class UserController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:employee-list', ['only' => ['employees']]);
        $this->middleware('permission:employee-create', ['only' => ['employeeCreate','employeeSave']]);
        $this->middleware('permission:employee-edit', ['only' => ['employeeEdit','employeeSave']]);
        $this->middleware('permission:employee-view', ['only' => ['employeeView']]);
        $this->middleware('permission:employee-delete', ['only' => ['employeeDelete']]);
        $this->middleware('permission:employee-action', ['only' => ['employeeAction']]);
    }

    public function employees()
    {
        $data = User::where('userType','2')->where('status','!=','2')->get();
        return View('employees.employee', compact('data'));
    }

    public function employeeCreate()
    {
        $roles = Role::where('name', '!=', 'admin')->pluck('name','name')->all();
        return View('employees.employee',compact('roles'));
    }

    public function employeeEdit($id)
    {
        if(User::find(base64_decode($id)))
        {
            $roles = Role::where('name', '!=', 'admin')->pluck('name','name')->all();
            $user = User::find(base64_decode($id));
            $userRole = $user->roles->pluck('name','name')->all();
            return View('employees.employee',compact('roles', 'user', 'userRole'));
        }
        notify()->error('Oops!!!, something went wrong, please try again.');
        return redirect()->back();
        
    }

    public function employeeSave(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required',
            'email'     => 'required|email',
            'roles'     => 'required'
        ]);

        if(!empty($request->id))
        {
            if(!empty($request->password))
            {
                $this->validate($request, [
                    'email'     => 'required|email|unique:users,email,'.$request->id,
                    'password'  => 'required|same:confirm-password'
                ]);
            }

            $user = User::find($request->id);
            $user->name     = $request->name;
            $user->email    = $request->email;
            $user->address1 = $request->address;
            $user->phone    = $request->phone;
            $user->status   = $request->status;
            $user->userType = '2';

            if(!empty($request->password))
            {
                $user->password = bcrypt($request->password);
            }
            $user->save();
            DB::table('model_has_roles')->where('model_id', $request->id)->delete();
            $user->assignRole($request->roles);
            notify()->success('Success, Employee information updated successfully.');
        }
        else
        {
            $this->validate($request, [
                'email'     => 'required|email|unique:users,email',
                'password'  => 'required|same:confirm-password'
            ]);

            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $input['userType'] = '2';
            $user = User::create($input);
            $user->assignRole($request->roles);
            notify()->success('Success, Employee created successfully.');
        }
        return redirect()->route('employee-list'); 
    }

    public function employeeView($id)
    {
        if(User::find(base64_decode($id)))
        {
            $user = User::find(base64_decode($id));
            return View('employees.employee', compact('user'));
        }
        notify()->error('Oops!!!, something went wrong, please try again.');
        return redirect()->back();
    }

    public function employeeAction(Request $request)
    {
      	$data  = $request->all();
      	foreach($request->input('boxchecked') as $action)
      	{
          	if($request->input('cmbaction')=='Active') {
              	User::where('id', $action)->update(['status' => '0']);
          	} elseif($request->input('cmbaction')=='Inactive') {
                User::where('id', $action)->update(['status' => '1']);
            } else {
              	User::where('id', $action)->update(['status' => '2']);
          	}
      	}
      	notify()->success('Success, Action successfully done.');
      	return redirect()->back();
  	}  

    public function employeeDelete($id)
    {
        if(User::find(base64_decode($id)))
        {
            $user = User::find(base64_decode($id));
            $user->status = '2';
            $user->save();
            notify()->success('Success, User successfully deleted.');
            return redirect()->back();
        }
        notify()->error('Oops!!!, something went wrong, please try again.');
        return redirect()->back();
    }
}
