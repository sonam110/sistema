<div class="row">
    <div class="col-md-12">
    	<div class="table-responsive">
        	<table class="table table-striped table-bordered">
                <tr>
                    <th width="20%">Order No.</th>
                    <td width="30%"><strong>{{$saleInfo->tranjectionid}}</strong></td>
                    <th width="20%">Order Date</th>
                    <td>{{date('Y-m-d', strtotime($saleInfo->created_at))}}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>{{$saleInfo->deliveryStatus}}</td>
                    <th>Total Amount</th>
                    <td><strong>${{$saleInfo->amount}}</strong></td>
                </tr>
                <tr>
                    <th>Tax ({{$saleInfo->tax_percentage}}%)</th>
                    <td><strong>${{$saleInfo->tax_amount}}</strong></td>
                    <th>Payable Amount</th>
                    <td><strong>${{$saleInfo->payableAmount}}</strong></td>
                </tr>
                <tr>
                    <th>Returned Amount</th>
                    <td colspan="3"><strong>${{$saleInfo->totalReturnAmount()}}</strong></td>
                </tr>
            </table>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <tr>
                    <th width="20%">Customer Name</th>
                    <td width="30%">{{$saleInfo->firstname}} {{$saleInfo->lastname}}</td>
                    <th width="20%">Company Name</th>
                    <td>{{$saleInfo->companyname}}</td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td colspan="3">
                        {{$saleInfo->address1}}, 
                        {{$saleInfo->address2}}, {{$saleInfo->city}}, {{$saleInfo->state}}
                    </td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td colspan="3">{{$saleInfo->phone}}</td>
                </tr>

                <tr>
                    <th>Order Remark</th>
                    <td colspan="3">{{$saleInfo->remark}}</td>
                </tr>
            </table>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Payment Mode</th>
                        <th>Amount</th>
                        <th></th>
                        <th>Pay</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($saleInfo->bookingPaymentThroughs as $key => $payment)
                    <tr class="item @if($payment->payment_mode=='Installment') highlight-section @endif ">
                        <th>{{$payment->payment_mode}}</th>
                        <td>
                            <strong>${{$payment->amount}}
                            </strong>
                        </td>
                        <td>
                            @if($payment->payment_mode=='Cheque')
                                <span class="text-left bolder">Cheque No. :</span> 
                                <span class="pull-right">{{$payment->cheque_number}}</span>
                                <br>
                                <span class="text-left bolder">Bank Info:</span> 
                                <span class="pull-right">{{$payment->bank_detail}}</span>
                            @elseif($payment->payment_mode=='Installment')
                                <span class="text-left bolder">No. of Installment:</span> 
                                <span class="pull-right">{{$payment->no_of_installment}}</span>
                                <br>
                                <span class="text-left bolder">Installment Amount:</span>
                                <span class="pull-right">${{$payment->installment_amount}}</span>
                                <br>
                                <span class="text-left bolder">Paid Installment:</span>
                                <span class="pull-right">{{$payment->paid_installment}}</span>
                                <br>
                                <span class="text-left bolder">Is Installment Complete:</span>
                                <span class="pull-right">{!!($payment->is_installment_complete=='1' ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>')!!}</span>
                            @endif
                        </td>
                        <td>
                            @if($payment->payment_mode=='Installment' && $payment->is_installment_complete=='0')
                            <div class="form-footer">
                                <a href="{{route('installment-receive-save',['bookingId'=>base64_encode($saleInfo->id),'paymentThroughId'=>base64_encode($payment->id)])}}" class="btn btn-success btn-block" onClick="return confirm('Are you sure you want to receive this ${{$payment->installment_amount}} EMI?');" data-toggle="tooltip" data-placement="top" title="" data-original-title="Receive ${{$payment->installment_amount}}">
                                    <i class="fe fe-check mr-2"></i> Receive ${{$payment->installment_amount}} 
                                </a>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>