<?php

namespace App\Exports;

use App\booking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
class salesReport implements FromCollection,WithHeadings
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
	      'Placed By',
	       'email','country', 'firstname', 'lastname', 'companyname', 'address1', 'address2', 'city', 'state', 'postcode', 'phone', 'shipping_email', 'shipping_country', 'shipping_firstname', 'shipping_lastname', 'shipping_companyname', 'shipping_address1', 'shipping_address2', 'shipping_city', 'shipping_state', 'shipping_postcode', 'shipping_phone', 'orderNote', 'tranjectionid', 'amount', 'installments', 'interestAmount', 'tax_percentage', 'tax_percentage', 'payableAmount','paymentThrough', 'orderstatus', 'deliveryStatus','due_condition','address_validation_code','ip_address','created_at'
	    ];
	 }
    public function collection()
    {
    	
        $whereRaw = getWhereRawFromRequest($this->request);
        if($whereRaw != '') {
            $query = booking::where('created_by', '!=', null)->orderBy('id','DESC')
            ->with('createdBy')
            ->whereRaw($whereRaw)
            ->get();
        } else {
            $query = booking::where('created_by', '!=', null)->orderBy('id','DESC')
            ->with('createdBy')
            ->get();

        }
       //dd($getLogs);
        return $array = $query->map(function ($b, $key) {
		return [
	      'Placed By'       => $b->createdBy->name .' '.$b->createdBy->lastname,
	      'email'   		=> $b->email,
	      'country'   		=> $b->country,
	      'firstname'   		=> $b->firstname,
	      'lastname'   		=> $b->lastname,
	      'companyname'   		=> $b->companyname,
	      'address1'   		=> $b->address1,
	      'address2'   		=> $b->address2,
	      'city'   		=> $b->city,
	      'state'   		=> $b->state,
	      'postcode'   		=> $b->postcode,
	      'phone'   		=> $b->phone,
	      'shipping_email'   		=> $b->shipping_email,
	      'shipping_country'   		=> $b->shipping_country,
	      'shipping_firstname'   		=> $b->shipping_firstname,
	      'shipping_lastname'   		=> $b->shipping_lastname,
	      'shipping_companyname'   		=> $b->shipping_companyname,
	      'email'   		=> $b->email,
	      'shipping_address1'   		=> $b->shipping_address1,
	      'shipping_address2'   		=> $b->shipping_address2,
	      'shipping_city'   		=> $b->shipping_city,
	      'shipping_state'   		=> $b->shipping_state,
	      'shipping_postcode'   		=> $b->shipping_postcode,
	      'shipping_phone'   		=> $b->shipping_phone,
	      'orderNote'   		=> $b->orderNote,
	      'tranjectionid'   		=> $b->tranjectionid,
	      'amount'   		=> '$'.$b->amount,
	      'installments'   		=> $b->installments,
	      'interestAmount'   		=> '$'.$b->interestAmount,
	      'tax_percentage'   		=> $b->tax_percentage,
	      'tax_amount'   		=> '$'.$b->tax_amount,
	      'payableAmount'   		=>'$'. $b->payableAmount,
	      'paymentThrough'   		=> $b->paymentThrough,
	      'orderstatus'   		=> $b->orderstatus,
	      'due_condition'   		=> $b->due_condition,
	      'deliveryStatus'   		=> $b->deliveryStatus,
	      'address_validation_code'   		=> $b->address_validation_code,
	      'ip_address'   		=> $b->ip_address,
	      'created_at'   		=> $b->created_at,
	   
		];
		});
    }

    
}
