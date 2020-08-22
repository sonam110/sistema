<?php

namespace App\Exports;

use App\PurchaseOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
class purchaseReport implements FromCollection,WithHeadings
{
	use Exportable;
	protected $request;
	public function __construct($request)
	{
	    $this->request = $request;
    	return $this;
	}

	public function headings(): array {
	    return [
	     'supplier', 'Po date', 'Po no', 'Total Amount', 'Tax percentage', 'Tax amount', 'Gross amount', 'Po status', 'Po completed date', 'Remark','created_at'
	    ];
	 }
    public function collection()
    {
    	
        $whereRaw = getWhereRawFromRequest($this->request);
        if($whereRaw != '') {
            $query = PurchaseOrder::orderBy('id','DESC')
            ->with('supplier')
            ->whereRaw($whereRaw)
            ->get();
        } else {
           $query = PurchaseOrder::orderBy('id','DESC')
            ->with('supplier')
            ->get();

        }
       //dd($getLogs);
        return $array = $query->map(function ($b, $key) {
		return [
	      'Placed By'       => $b->supplier->name,
	      'po_date'   		=> $b->po_date,
	      'po_no'   		=> $b->po_no,
	      'total_amount'   		=> '$'.$b->total_amount,
	      'tax_percentage'  => $b->tax_percentage,
	      'tax_amount'   		=> '$'.$b->tax_amount,
	      'gross_amount'   		=>'$'. $b->gross_amount,
	      'po_status'   		=> $b->po_status,
	      'po_completed_date'   => $b->po_completed_date,
	      'remark'   		=> $b->remark,
	      'created_at'   		=> $b->created_at,
	   
		];
		});
    }

    
}
