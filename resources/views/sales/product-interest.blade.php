

<div class="modal-header">
   <!--  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="ti-close"></span>X</button> -->
    <h4 class="modal-title" id="myModalLabel">
        <span class="text-info">Product With interest rate
        </span>

    </h4>
    

</div>
<div class="modal-body">

    <div class="table-responsive">
        <table class="table table-hover ">
            <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Cantiad</th>
                <th>Subtotal</th>
                <th>Tasa Interes</th>
                <th>Monto Interes</th>
                <th>Total</th>
            </tr>
          </thead>
            @if(count($product_interest)>0)
            @foreach($product_interest as $key=> $rows)

            <tr>
                <td>{!! $key+1 !!}</td>
                <td>{!!$rows['nombre'] !!}</td>
                <td>${!!$rows['price'] !!}</td>
                <td>{!!$rows['qty'] !!}</td>
                <td>${!!$rows['subtotal'] !!}</td>
                <td>{!!$rows['getPer'] !!} %</td>
                <td>${!!$rows['interest'] !!}</td>
                <td>${!!$rows['paAmount'] !!}</td>

            </tr>
            @endforeach
             @else
              <tr class=""><td colspan="8">Producto no encontrado.</td></tr>
             @endif

             <tfoot class="text-center">
                <tr class="cart-subtotal">
                  <th colspan="6" class="text-right"><strong>Total:</strong></th>
                  <td colspan="2" class="text-right" ><strong class="amount">
                  $<span id="subtotal">{{ $payableAmount }}</span></strong></td>
                </tr>
                 <tr class="cart-subtotal">
                  <th colspan="6" class="text-right"><strong>Descuento Cupon:</strong></th>
                  <td colspan="2" class="text-right"><strong class="amount">
                  $<span id="subtotal">{{ $max_dis }}</span></strong></td>
                </tr>

                <tr class="order-total">
                    <?php
                        $total = $payableAmount -$max_dis;

                    ?>
                  <th colspan="6" class="text-right">Monto Total: </th>
                  <td colspan="2" class="text-right"><strong><span class="amount" id="final_amount">$&nbsp;{{ number_format($total,2,'.',',') }}</span></strong>
                  </td>
                </tr>
              </tfoot>
        </table>

    </div>
</div>
<div class="modal-footer">

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
        var email = $('#email').val();
        var subtotal = $('#subtotal').text();
        var pids = $('#pids').val();
        $('#coupon-error').hide();
          $.ajax({
              url: appurl+"check-coupon-code",
              type: 'POST',
              data: "coupon_code="+coupon_code+"&email="+email+"&subtotal="+subtotal+"&pids="+pids,
              success:function(info){
                if(info['type']=='error'){
                  $('#coupon-error').show();
                  $('#coupon-error').text(info['message']);
                  $('#max_saving').text(0);

                }
                if(info['type']=='success'){
                  $('#max_saving').text(info['max_saving']);
                  $('#max_dis').val(info['max_saving']);
                  $('#coupon_id').val(info['coupon_id']);
                  $('#coupon_discount').val(info['coupon_discount']);
                  $('#selected-coupon').text(info['coupon_code']);
                }

              }
          });
    }
    /*$('input#coupon_code').bind("change keyup input",function() {
        $('.error').hide();

        checkCoupon();
    });*/

    $(document).on('click', '.appy-for-coupon', function(){
        var max_saving = $('#max_saving').text();
        var subtotal = $('#subtotal').text();
        var shipping_a = $('#shipping_a').text();
        var coupon_code = $('#coupon_code').val();
        $('#couponcode').hide();
        $.ajax({
          url: appurl+"apply-for-coupon",
          type: 'POST',
          data: "max_saving="+max_saving+"&subtotal="+subtotal+"&shipping_a="+shipping_a+"&coupon_code="+coupon_code,
          success:function(info){
            if(info['type']=='error'){

            }
            if(info['type']=='success'){
                if(info['max_saving'] <1){
                    var max= 'Aplicar cupón';
                    $('#max_dis').val('');
                    $('#coupon_id').val('');
                    $('#coupon_discount').val('');

                } else{
                    var max = '-$'+info['max_saving'];
                }
                $("#coupon-list-modal").modal('hide');
                $('.coupon-amount').text(max);
                $('#final_amount').text('$'+info['final_amount']);
                $('.cartAmountVal').text('$'+info['final_amount']);
                $('.payableAmountVal').text('$'+info['final_amount']);
                $('#amount').val(info['amount']);
                $('#totalAmount').val(info['amount']);
                $('#payableAmount').val(info['amount']);
                if(max_saving >0){
                    $('#couponcode').show();
                    $('#couponcode').text('Cupón aplicado : '+coupon_code);

                }

            }

          }
        });


    });

</script>
