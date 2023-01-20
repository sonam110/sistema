
            
<div class="modal-header">
    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="ti-close"></span>X</button> -->
    <h4 class="modal-title" id="myModalLabel">
        <span class="text-info">CUPONES
        </span>

    </h4>
    
</div>
<div class="modal-body">

      <div class="coupon-code your-order-table table-responsive">
        <table class="table-content">
          <tbody>

              <tr class="order-total" >
                <td   class="product-name">
                    <div class="custom-search checkout-form-list ">
                      <input type="text" class="custom-search-input" name="coupon_code"  id="coupon_code" placeholder="Código promocional" value="">
                      <button class="custom-search-botton btn btn-secondary" id="coupon-code"  disabled  onclick="checkCoupon()" >Controlar</button>  

                    </div>    
                    <div id="coupon-error" class="" style="color: red;"></div>
                </td>
                <td>
                    <strong id="selected-coupon"></strong>
                </td>
               
              </tr>
          
          </tbody>
          
        </table>
  
    </div>
    <br>
    <div class="table-responsive">
        <table class="table table-hover ">
        
            @if(count($allCoupons)>0)
            @foreach($allCoupons as $data)
            <tr>
                <th width="90%" > <strong > {{ $data->coupon_code }}</strong><br><span class="text-success">
                        {{ $data->coupon_desc }}
                    </span></th>
                <th width="10%" >
                  <a href="javascript:;" id="coupon-use" data-coupon="{{ $data->coupon_code }}" style="color: #d672f1;font-size: 15px;">  Usar</a>
                </th>
                
               
            </tr>
            @endforeach
             @else
              <tr class=""><td>No hay otros cupones disponibles en este producto.</td></tr>
             @endif
           
        </table>

    </div>
</div>
<div class="modal-footer">
   
    <strong >Máximo ahorro : $<span id="max_saving"> 0</span> </strong>
    <button type="button" class="btn btn-secondary appy-for-coupon"  >Aplicar</button>
    <button type="button" class="btn btn-warning" data-dismiss="modal">Cerrar</button>
    
   
</div>

<script type="text/javascript">
    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });

    $(document).on('click', '#coupon-use', function(){
        $('#coupon-error').hide();
        var coupon_code = $(this).data('coupon');
        if(coupon_code!=''){
            $('#coupon-code').prop('disabled',false);
            $('#coupon_code').val(coupon_code);
            checkCoupon();
        }
        
        
    });
    function checkCoupon(){
        var coupon_code = $('#coupon_code').val();
        var customer_id = $('#customer_id').val();
        var subtotal = $('#gross_amount').val();

        var pids=[]; 
        $('select[name="product_id[]"] option:selected').each(function() {
          pids.push($(this).val());
        });
        var price=[]; 
        $('input[name="price[]"]').each(function() {
          price.push($(this).val());
        });
        var required_qty=[]; 
        $('input[name="required_qty[]"]').each(function() {
          required_qty.push($(this).val());
        });
        $('#coupon-error').hide();
        $.ajax({
          url: "{{ route('check-coupon-code') }}",
          type: 'POST',
          data: "coupon_code="+coupon_code+"&customer_id="+customer_id+"&subtotal="+subtotal+"&pids="+pids+"&required_qty="+required_qty+"&price="+price,
          success:function(info){
            if(info['type']=='error'){
              $('#coupon-error').show();
              $('#coupon-error').text(info['message']);
              $('#max_saving').text(0);
              $('#coupon_discount').val('');

            }
            if(info['type']=='success'){
              $('#max_saving').text(info['max_saving']);
              $('#max_dis').val(info['max_saving']);
              $('#coupon_id').val(info['coupon_id']);
              $('#coupon_discount').val(info['coupon_discount']);
             
            }

          }
        });
    }
    /*$('input#coupon_code').bind("change keyup input",function() { 
        $('.error').hide();

        checkCoupon();
    });*/

    $(document).on('click', '.appy-for-coupon', function(){
       
            var coupon_code = $('#coupon_code').val();
            var max_dis = $('#max_dis').val();
            var gross_amount = $('#gross_amount').val();
            $('#couponcode').hide();
            $.ajax({
              url: "{{ route('apply-for-coupon') }}",
              type: 'POST',
              data: "coupon_code="+coupon_code,
              success:function(info){
                if(info['type']=='error'){
                    $('#max_dis').val('');
                    $('#coupon_id').val('');
                    $('#coupon_discount').val('');
                    var max= 'Aplicar cupón';
                    calculationAmount();
                    checkPayment();
                    $('.coupon-amount').text(max);
                    $("#coupon-list-modal").modal('hide');
                }
                if(info['type']=='success'){
                    if(max_dis <1){
                        $('#max_dis').val('');
                        $('#coupon_id').val('');
                        $('#coupon_discount').val('');
                        var max= 'Aplicar cupón';
                        calculationAmount();
                        checkPayment();
                        $("#coupon-list-modal").modal('hide');
                        $('.coupon-amount').text(max);
                      
                    } else{
                        $('#old_grossamount').val(gross_amount);
                        calculationAmount();
                        checkPayment();
                        $("#coupon-list-modal").modal('hide');
                    }
                   
                    if(info['max_saving'] <1){
                        $('#couponcode').show();
                        $('#couponcode').text('Cupón aplicado : '+coupon_code);

                    }
                    
                }

              }
          });
 
        
    });

</script>


