<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class CustomerController extends Controller
{
    function __construct()
    {
        $this->middleware(['permission:customer-list|customer-create|customer-edit|customer-delete|customer-action']);

        $this->middleware('permission:customer-list', ['only' => ['customers']]);
        $this->middleware('permission:customer-create', ['only' => ['customerCreate','customerSave', 'addCustomerModal']]);
        $this->middleware('permission:customer-edit', ['only' => ['customerEdit','customerSave']]);
        $this->middleware('permission:customer-view', ['only' => ['customerView']]);
        $this->middleware('permission:customer-delete', ['only' => ['customerDelete']]);
        $this->middleware('permission:customer-action', ['only' => ['customerAction']]);
    }

    public function customers()
    {
        $customers = User::where('userType', '1')->where('status','!=','2')->get();
        return View('sales.customer', compact('customers'));
    }
    public function customerListDatable(Request $request)
    {
        $query = User::where('userType', '1')->where('status','!=','2');
        return datatables($query)
            ->addColumn('checkbox', function ($query)
            {
                
                $checkbox = '<label class="custom-control custom-checkbox">
                       <input type="checkbox"  name="boxchecked[]" value="' . $query->id . '"  class ="colorinput-input custom-control-input allChecked" id="boxchecked">
                         <span class="custom-control-label"></span>
                        </label>';
                
                return $checkbox;
            })
            
            ->editColumn('status', function ($query)
            {
                if ($query->status == '1')
                {
                    $status = '<<span class="text-danger">Inactivo</span>>';
                }
               
                else
                {
                    $status = ' <span class="text-success">Activo</span>';
                }
                return $status;
            })
            
            ->addColumn('action', function ($query)
            {
                $view = auth()->user()->can('customer-view') ? '<a class="btn btn-sm btn-secondary" href="'.route('customer-view',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ver"><i class="fa fa-eye"></i></a>' : '';
                $edit = auth()->user()->can('customer-edit') ? '<a class="btn btn-sm btn-primary" href="'.route('customer-edit',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="" data-original-title="Editar"><i class="fa fa-edit"></i></a>' : '';
                $delete = auth()->user()->can('customer-delete') ? '<a class="btn btn-sm btn-danger" href="'.route('customer-delete',base64_encode($query->id)).'" onClick="return confirm("Está seguro que desea eliminarlo?");" data-toggle="tooltip" data-placement="top" title="" data-original-title="Eliminar"><i class="fa fa-trash"></i></a>' : '';
                

                return '<div class="btn-group btn-group-xs">'.$view.$edit.$delete.'</div>';
            })
        ->escapeColumns(['action'])
        ->addIndexColumn()
        ->make(true);
    }

    public function customerCreate()
    {
        return View('sales.customer');
    }

    public function customerEdit($id)
    {
        if(User::find(base64_decode($id)))
        {
            $customer = User::find(base64_decode($id));
            return View('sales.customer',compact('customer'));
        }
        notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        return redirect()->back();
        
    }

    public function customerSave(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required',
            'lastname'  => 'required',
            'email'     => 'required|email',
            'phone'   	=> 'required',
            'status'    => 'required',
            'doc_type'  => 'required',
            'doc_number'=> 'required',
        ]);

        if(!empty($request->id))
        {
        	$this->validate($request, [
                'email'     => 'required|email|unique:users,email,'.$request->id
            ]);

            $customer = User::find($request->id);
            notify()->success('Success, Customer information updated successfully.');
        }
        else
        {
        	$this->validate($request, [
                'email'     => 'required|email|unique:users,email'
            ]);
        	$customer = new User;
        	$customer->password    	= \Hash::make($request->phone);
            notify()->success('Success, Customer created successfully.');
        }
        $customer->name     	= $request->name;
        $customer->lastname     = $request->lastname;
        $customer->email    	= $request->email;
        $customer->phone   		= $request->phone;

        $customer->companyname  = (!empty($request->companyname)) ? $request->companyname : '-';
        $customer->address1 	= (!empty($request->address1)) ? $request->address1 : '-';
        $customer->address2 	= (!empty($request->address2)) ? $request->address2 : '-';
        $customer->city 		= (!empty($request->city)) ? $request->city : '-';
        $customer->state 		= (!empty($request->state)) ? $request->state : '-';
        $customer->country 		= (!empty($request->country)) ? $request->country : '-';
        $customer->postcode 	= (!empty($request->postcode)) ? $request->postcode : '-';

        $customer->status       = $request->status;
        $customer->doc_type     = $request->doc_type;
        $customer->doc_number   = $request->doc_number;
        $customer->save();
        return redirect()->back(); 
    }

    public function customerView($id)
    {
        if(User::find(base64_decode($id)))
        {
            $user = User::find(base64_decode($id));
            return View('sales.customer', compact('user'));
        }
        notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        return redirect()->back();
    }

    public function customerAction(Request $request)
    {
      	$data  = $request->all();
      	foreach($request->input('boxchecked') as $action)
      	{
          	if($request->input('cmbaction')=='Inactive') {
              	User::where('id', $action)->update(['status' => '1']);
          	} elseif($request->input('cmbaction')=='Active') {
                User::where('id', $action)->update(['status' => '0']);
            } else {
              	User::where('id', $action)->update(['status' => '2']);
          	}
      	}
      	notify()->success('Success, Action successfully done.');
      	return redirect()->back();
  	}  

    public function customerDelete($id)
    {
        if(User::find(base64_decode($id)))
        {
            $user = User::find(base64_decode($id));
            $user->status = '2';
            $user->save();
            notify()->success('Success, Customer successfully deleted.');
            return redirect()->back();
        }
        notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        return redirect()->back();
    }

    public function addCustomerModal(Request $request)
    {
      return view('sales.add-customer-modal'); 
    }
}
