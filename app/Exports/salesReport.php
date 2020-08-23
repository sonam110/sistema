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
	      'Placed By', 'Order Transaction Number',
	       'Email','Country', 'First Name', 'Last Name', 'Company Name', 'Address1', 'Address2', 'City', 'State', 'Postcode', 'Phone', 'Shipping Email', 'Shipping Country', 'Shipping First Name', 'Shipping Last Name', 'Shipping Company Name', 'Shipping Address1', 'Shipping Address2', 'Shipping City', 'Shipping State', 'Shipping Postcode', 'Shipping Phone', 'Order Note', 'Amount', 'Installments', 'InterestAmount', 'Tax Percentage', 'Tax Amount', 'Payable Amount','Payment Through', 'Order Status', 'Delivery Status','due Condition','Address Validation Code','IP Address','Created At'
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
			  'Order Transaction Number'   	=> $b->tranjectionid,
		      'Placed By'       	=> $b->createdBy->name .' '.$b->createdBy->lastname,
		      'Email'   			=> $b->email,
		      'Country'   			=> $b->country,
		      'First Name'   		=> $b->firstname,
		      'Last Name'   			=> $b->lastname,
		      'Company Name'   		=> $b->companyname,
		      'Address1'   			=> $b->address1,
		      'Address2'   			=> $b->address2,
		      'City'   				=> $b->city,
		      'State'   			=> $b->state,
		      'Postcode'   			=> $b->postcode,
		      'Phone'   			=> $b->phone,
		      'Shipping Email'   	=> $b->shipping_email,
		      'Shipping Country'   	=> $b->shipping_country,
		      'Shipping First Name'  => $b->shipping_firstname,
		      'Shipping Last Name'   => $b->shipping_lastname,
		      'Shipping Company Name'=> $b->shipping_companyname,
		      'Shipping Address1'   => $b->shipping_address1,
		      'Shipping Address2'   => $b->shipping_address2,
		      'Shipping City'   	=> $b->shipping_city,
		      'Shipping State'   	=> $b->shipping_state,
		      'Shipping Postcode'   => $b->shipping_postcode,
		      'Shipping Phone'   	=> $b->shipping_phone,
		      'Order Note'   		=> $b->orderNote,
		      'Amount'   			=> '$'.$b->amount,
		      'Installments'   		=> $b->installments,
		      'InterestAmount'   	=> '$'.$b->interestAmount,
		      'Tax Percentage'   	=> $b->tax_percentage,
		      'Tax Amount'   		=> '$'.$b->tax_amount,
		      'Payable Amount'   	=> '$'. $b->payableAmount,
		      'Payment Through'   	=> $b->paymentThrough,
		      'Order Status'   		=> $b->orderstatus,
		      'due Condition'   	=> $b->due_condition,
		      'Delivery Status'   	=> $b->deliveryStatus,
		      'Address Validation Code'=> $b->address_validation_code,
		      'IP Address'   		=> $b->ip_address,
		      'Created At'   		=> $b->created_at->format('Y-m-d'),
			];
		});
    }

    
}
