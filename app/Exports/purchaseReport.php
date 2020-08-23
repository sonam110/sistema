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
	     'Supplier Name', 'Po Date', 'PO Number', 'Total Amount', 'Tax Percentage', 'Tax Amount', 'Gross Amount', 'Po Status', 'Po Completed Date', 'Remark', 'Created At'
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

        return $array = $query->map(function ($b, $key) {
			return [
		      'Supplier Name'       => $b->supplier->name,
		      'PO Date'   			=> $b->po_date,
		      'PO Number'   		=> $b->po_no,
		      'Total Amount'   		=> '$'.$b->total_amount,
		      'Tax Percentage' 	 	=> $b->tax_percentage,
		      'Tax Amount'   		=> '$'.$b->tax_amount,
		      'Gross Amount'   		=> '$'. $b->gross_amount,
		      'Po Status'   		=> $b->po_status,
		      'Po Completed Date'   => $b->po_completed_date,
		      'Remark'   			=> $b->remark,
		      'Created At'   		=> $b->created_at->format('Y-m-d'),
		   
			];
		});
    }

    
}
