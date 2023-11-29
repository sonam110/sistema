<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Budget;
use App\Websitesetting;
use App\BudgetItem;
use DB;
use PDF;
use Mail;
class BudgetController extends Controller
{
    public function allBudget()
    {
        return view('budgets.budget-list');
    }

    public function budgetDatatable(Request $request)
    {
        if(auth()->user()->hasRole('admin'))
        {
            $query = Budget::with('createdBy')->orderBy('created_at', 'DESC');
        }
        else
        {
            $query = Budget::with('createdBy')->where('created_by', auth()->id());
        }
        return datatables($query)
            ->addColumn('checkbox', function ($query)
            {
                $checkbox = null;
                $checkbox = '<label class="custom-control custom-checkbox">
                       <input type="checkbox"  name="boxchecked[]" value="' . $query->id . '"  class ="colorinput-input custom-control-input allChecked" id="boxchecked">
                         <span class="custom-control-label"></span>
                        </label>';
                
                return $checkbox;
            })
            ->addColumn('placed_by', function ($query)
            {
                if($query->createdBy)
                {
                    return '<strong>'.$query->createdBy->name .' '.$query->createdBy->lastname.'</strong>';
                }
                return '-';
            })
        
            ->addColumn('customer_name', function ($query)
            {
                if($query->customer)
                {
                    return '<strong>'.$query->customer->name .' '.$query->customer->lastname.'</strong>';
                }
                 return '-';
            })
            ->editColumn('created_at', function ($query)
            {
                return $query->created_at->format('Y-m-d');
            })
            ->editColumn('payableAmount', function ($query)
            {
                return '<strong>$ '.number_format($query->payable_amount,2,',','.').'</strong>';
            })
            ->editColumn('status', function ($query)
            {
               
                if($query->status =='1'){
                    $status = '<span class="badge badge-info">Active</span>';

                } else{
                     $status = '<span class="badge badge-danger">Inactive</span>';
                }
                return $status;
            })
           
            ->addColumn('action', function ($query)
            {
                $download = auth()->user()->can('budget-download') ? '<a class="btn btn-sm btn-default" target="_blank" href="'.route('budget-download',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="Download / Print" data-original-title="Descargar / Imprimir"><i class="fa fa-download"></i></a>' : '';  
                $view = auth()->user()->can('budget-view') ? '<a class="btn btn-sm btn-info" href="'.route('budget-view',base64_encode($query->id)).'" data-toggle="tooltip" data-placement="top" title="Ver Orden" data-original-title="Ver Pedido"><i class="fa fa-eye"></i></a>' : '';

                return '<div class="btn-group btn-group-xs">'.$download.$view.'</div>';
            })
        ->escapeColumns(['action'])
        ->addIndexColumn()
        ->make(true);
    }
     public function budgetCreate ()
    {
         return view('budgets.budget-list');
    }
    public function budgetSave(Request $request)
    {

        $this->validate($request, [
            'customer_id'   => 'required|integer|exists:users,id',
            //"product_id"    => "required|array|min:1",
            //"product_id.*"  => "required|string|distinct|min:1",
        ]);

        DB::beginTransaction();
        try {
            $getCustomerInfo = User::find($request->customer_id);
            $budget = new Budget;
            $budget->created_by        = auth()->id();
            $budget->customer_id            = $getCustomerInfo->id;
            $budget->observation         = $request->observation;
            $budget->total            = $request->total_amount;
            $budget->tax_percentage    = $request->tax_percentage;
            $budget->tax_amount        = $request->tax_amount;
            $budget->shipping_charge   = $request->shipping_charge;
            $budget->payable_amount     = $request->gross_amount;
            $budget->status       = '1';
            $budget->ip_address        = $request->ip();
            $budget->save();
            foreach ($request->product_id as $key => $product) {
                if(!empty($product))
                {
                    $budgetItem = new BudgetItem;
                    $budgetItem->budget_id = $budget->id;
                    $budgetItem->itemid    = $product;
                    $budgetItem->itemqty   = $request->required_qty[$key];
                    $budgetItem->itemPrice = $request->price[$key];
                    $budgetItem->save();

                }
            }

            
            DB::commit();
            //send Mail
            notify()->success('Hecha, Presupuesto generado exitosamente.');
            return redirect()->route('budget-create');
        } catch (\Exception $exception) {
            DB::rollback();
            notify()->error('Error, Oops!!!, algo salió mal, intente de nuevo.'. $exception->getMessage());
            return redirect()->back()->withInput();
        } catch (\Throwable $exception) {
            DB::rollback();
            notify()->error('Error, Oops!!!, algo salió mal, intente de nuevo.'. $exception->getMessage());
            return redirect()->back()->withInput();
        }
    }
    public function budgetView($id)
    {
        if(Budget::find(base64_decode($id)))
        {
            $budget = Budget::find(base64_decode($id));
            $user = user::find($budget->customer_id);
            return View('budgets.budget-list', compact('budget'),compact('user'));
        }
        notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        return redirect()->back();
    }
    public function budgetDownload($id)
    {
        if(Budget::find(base64_decode($id)))
        {
            $budget = Budget::find(base64_decode($id));
            $str = 'budget-'.time().'-'.$id.'';
            $user = user::find($budget->customer_id);
            $data = [
                'budget' => $budget,
              'user' => $user
            ];
            $pdf = PDF::loadView('budgets.budget-download', $data);
            return $pdf->stream($str.'.pdf');
        }
        notify()->error('Oops!!!, algo salió mal, intente de nuevo.');
        return redirect()->back();
    }
     public function budgetAction(Request $request)
    {
        $data  = $request->all();
        foreach($request->input('boxchecked') as $action)
        {
            if($request->input('cmbaction')=='Inactive') {
                Budget::where('id', $action)->update(['status' => '0']);
            } else {
                Budget::where('id', $action)->update(['status' => '1']);
            }
        }
        notify()->success('Success, Action successfully done.');
        return redirect()->back();
    }
    
}
