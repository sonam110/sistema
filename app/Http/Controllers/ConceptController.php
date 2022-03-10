<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Concept;
use DB;

class ConceptController extends Controller
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

    public function concepts()
    {
        $data = Concept::where('id','>','1')->get();
        return View('concept.concept', compact('data'));
    }

    public function conceptCreate()
    {
        return View('concept.concept');
    }

    public function conceptEdit($id)
    {
        $concept = Concept::find(base64_decode($id));
        if($concept)
        {
            return View('concept.concept',compact('concept'));
        }
        notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        return redirect()->back();
        
    }

    public function conceptSave(Request $request)
    {
        $this->validate($request, [
            'description'      => 'required',
            'status'     => 'required'
        ]);

        if(!empty($request->id))
        {
            $concept = Concept::find($request->id);
            $concept->description = $request->description;
            $concept->status   = $request->status;
            $concept->save();
            notify()->success('Success, Employee information updated successfully.');
        }
        else
        {
            $concept = new Concept;
            $concept->description = $request->description;
            $concept->status   = $request->status;
            $concept->save();
            notify()->success('Success, Employee created successfully.');
        }
        return redirect()->route('concept-list'); 
    }

    public function conceptDelete($id)
    {
        $concept = Concept::find(base64_decode($id)); 
        if ($concept)
        {
            $concept->delete();
            notify()->success('Success, User successfully deleted.');
            return redirect()->back();
        }
        notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        return redirect()->back();
    }
}
