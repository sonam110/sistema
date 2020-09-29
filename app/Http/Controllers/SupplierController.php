<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Supplier;

class SupplierController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:supplier-list', ['only' => ['suppliers']]);
        $this->middleware('permission:supplier-create', ['only' => ['supplierCreate','supplierSave', 'addSupplierModal']]);
        $this->middleware('permission:supplier-edit', ['only' => ['supplierEdit','supplierSave']]);
        $this->middleware('permission:supplier-view', ['only' => ['supplierView']]);
        $this->middleware('permission:supplier-delete', ['only' => ['supplierDelete']]);
        $this->middleware('permission:supplier-action', ['only' => ['supplierAction']]);
    }

    public function suppliers()
    {
        $suppliers = Supplier::where('status','!=','2')->get();
        return View('purchases.supplier', compact('suppliers'));
    }

    public function supplierCreate()
    {
        return View('purchases.supplier');
    }

    public function supplierEdit($id)
    {
        if(Supplier::find(base64_decode($id)))
        {
            $supplier = Supplier::find(base64_decode($id));
            return View('purchases.supplier',compact('supplier'));
        }
        notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        return redirect()->back();
        
    }

    public function supplierSave(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required',
            'email'     => 'required|email',
            'address'   => 'required',
            'city'     	=> 'required',
            'state'     => 'required',
            'phone'     => 'required',
        ]);

        if(!empty($request->id))
        {
            $supplier = Supplier::find($request->id);
            notify()->success('Success, Supplier information updated successfully.');
        }
        else
        {
        	$supplier = new Supplier;
            notify()->success('Success, Supplier created successfully.');
        }
        $supplier->name     	= $request->name;
        $supplier->email    	= $request->email;
        $supplier->company_name = $request->company_name;
        $supplier->address 		= $request->address;
        $supplier->city    		= $request->city;
        $supplier->state   		= $request->state;
        $supplier->phone   		= $request->phone;
        $supplier->vat_number   = $request->vat_number;
        $supplier->status   	= $request->status;
        $supplier->save();
        return redirect()->back(); 
    }

    public function supplierView($id)
    {
        if(Supplier::find(base64_decode($id)))
        {
            $user = Supplier::find(base64_decode($id));
            return View('purchases.supplier', compact('user'));
        }
        notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        return redirect()->back();
    }

    public function supplierAction(Request $request)
    {
      	$data  = $request->all();
      	foreach($request->input('boxchecked') as $action)
      	{
          	if($request->input('cmbaction')=='Inactive') {
              	Supplier::where('id', $action)->update(['status' => '0']);
          	} elseif($request->input('cmbaction')=='Active') {
                Supplier::where('id', $action)->update(['status' => '1']);
            } else {
              	Supplier::where('id', $action)->update(['status' => '2']);
          	}
      	}
      	notify()->success('Success, Action successfully done.');
      	return redirect()->back();
  	}  

    public function supplierDelete($id)
    {
        if(Supplier::find(base64_decode($id)))
        {
            $user = Supplier::find(base64_decode($id));
            $user->status = '2';
            $user->save();
            notify()->success('Success, Supplier successfully deleted.');
            return redirect()->back();
        }
        notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        return redirect()->back();
    }

    public function addSupplierModal(Request $request)
    {
      return view('purchases.add-supplier-modal'); 
    }
}
