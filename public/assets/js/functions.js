$(function() {
  $.ajaxSetup({
    headers: {
      'X-CSRF-Token': $('meta[name="_token"]').attr('content')
    }
  });
});


$('body').tooltip({
    selector: '[data-toggle="tooltip"]'
});

function getSubCatList(catId)
{
  $.ajax({
    url: appurl+"get-sub-cat-list",
    type: "POST",
    data: "catId="+catId,
    success:function(info){
      $('#getSubCatList').html(info);
    }
  });
}

function calculationAmount() {
  var tax = $("#tax_percentage").val();
  var shippingCharge = $("#shipping_charge").val();
  var interest_amount = $("#interest_amount").val();
  var max_dis = $("#max_dis").val();
  var $tblrows = $("#product-table tr");
  $tblrows.each(function (index) {
      var $tblrow = $(this);
      var qty = $tblrow.find(".required_qty").val();
      var price = $tblrow.find(".price").val();
      var subTotal = parseFloat(qty) * parseFloat(price);

      if (!isNaN(subTotal)) {
          $tblrow.find('.subtotal').val(subTotal.toFixed(2));
          var totalAmount = 0;
          var taxAmount = 0;
          var grossAmount = 0;

          $(".subtotal").each(function () {
              var stval = parseFloat($(this).val());
              totalAmount += isNaN(stval) ? 0 : stval;
              taxAmount = (totalAmount * tax) / 100;
          });
          grossAmount = totalAmount + taxAmount + parseFloat(shippingCharge)+ parseFloat(interest_amount);
          if(max_dis !=''){
            var discount = max_dis;
            if (discount!='') {
              grossAmount = grossAmount-parseFloat(discount).toFixed(2);
              $('.coupon-amount').text('-$'+discount);
  
            }else{
              $('.coupon-amount').text('Aplicar cupón');
              $('#max_dis').val('');
            }

          }
          //console.log(shippingCharge);
          $('.total_amount').val(totalAmount.toFixed(2));
          $('.tax_amount').val(taxAmount.toFixed(2));
          $('.gross_amount').val(grossAmount.toFixed(2));
      }
  });
}

function calculationAmountPO() {
  var tax = $("#tax_percentage").val();
  var $tblrows = $("#product-table tr");
  $tblrows.each(function (index) {
      var $tblrow = $(this);
      var qty = $tblrow.find(".required_qty").val();
      var price = $tblrow.find(".price").val();
      var subTotal = parseFloat(qty) * parseFloat(price);

      if (!isNaN(subTotal)) {

          $tblrow.find('.subtotal').val(subTotal.toFixed(2));
          var totalAmount = 0;
          var taxAmount = 0;
          var grossAmount = 0;

          $(".subtotal").each(function () {
              var stval = parseFloat($(this).val());
              totalAmount += isNaN(stval) ? 0 : stval;
              taxAmount = (totalAmount * tax) / 100;
          });
          grossAmount = totalAmount + taxAmount;
          $('.total_amount').val(totalAmount.toFixed(2));
          $('.tax_amount').val(taxAmount.toFixed(2));
          $('.gross_amount').val(grossAmount.toFixed(2));
      }
  });
}

function paymentThrough(type)
{
  if(type=='Partial Payment')
  {
    $("#partial-payment").show();
    $("#payment-button").attr('disabled', true);
  }
  else
  {
    $("#partial-payment").hide();
    $("#payment-button").attr('disabled', false);
  }
}

function checkPayment() {
  var gross_amount = $('#gross_amount').val();
  var $tblrows = $("#partial-payment tr");
  $tblrows.each(function (index) {
      var $tblrow = $(this);
      var partial_amount = $tblrow.find(".partial_amount").val();
      if (!isNaN(partial_amount)) {
        var totalAmount = 0;
        var remainingAmount = 0;
        $(".partial_amount").each(function () {
            var stval = parseFloat($(this).val());
            totalAmount += isNaN(stval) ? 0 : stval;
            remainingAmount = (parseFloat(totalAmount)-parseFloat(gross_amount));
        });
        if(parseFloat(totalAmount).toFixed(2)==parseFloat(gross_amount).toFixed(2))
        {
          $("#payment-button").attr('disabled', false);
        }
        else
        {
          if(($("#payment_through").val()=='Partial Payment') || ($("#payment_through").val()==''))
          {
            $("#payment-button").attr('disabled', true);
          }
        }
        $('#remaining_amount').text(remainingAmount.toFixed(2));
      }
  });
}

function paymentCheckInput(e)
{
  if(e.value=='Cheque')
  {
    $(e).closest('tr').find(".cheque_number_span").show();
    $(e).closest('tr').find(".bank_detail_span").show();
    $(e).closest('tr').find(".no_of_installment_span").hide();
    $(e).closest('tr').find(".installment_amount_span").hide();
    $(e).closest('tr').find(".card_brand_span").hide();
    $(e).closest('tr').find(".card_number_span").hide();

    $(e).closest('tr').find(".card_brand").attr('required',false);
    $(e).closest('tr').find(".card_number").attr('required',false);
    $(e).closest('tr').find(".no_of_installment").attr('required',false);
    $(e).closest('tr').find(".installment_amount").attr('required',false);
    $(e).closest('tr').find(".cheque_number").attr('required',true);
    $(e).closest('tr').find(".bank_detail").attr('required',true);
  }
  else if(e.value=='Installment')
  {
    $(e).closest('tr').find(".no_of_installment_span").show();
    $(e).closest('tr').find(".installment_amount_span").show();
    $(e).closest('tr').find(".cheque_number_span").hide();
    $(e).closest('tr').find(".bank_detail_span").hide();
    $(e).closest('tr').find(".card_brand_span").hide();
    $(e).closest('tr').find(".card_number_span").hide();

    $(e).closest('tr').find(".no_of_installment").attr('required',true);
    $(e).closest('tr').find(".installment_amount").attr('required',true);
    $(e).closest('tr').find(".cheque_number").attr('required',false);
    $(e).closest('tr').find(".bank_detail").attr('required',false);
    $(e).closest('tr').find(".card_brand").attr('required',false);
    $(e).closest('tr').find(".card_number").attr('required',false);
  }
  else if(e.value=='Credit Card')
  {
    $(e).closest('tr').find(".card_brand_span").show();
    $(e).closest('tr').find(".card_number_span").show();
    $(e).closest('tr').find(".no_of_installment_span").hide();
    $(e).closest('tr').find(".installment_amount_span").hide();
    $(e).closest('tr').find(".cheque_number_span").hide();
    $(e).closest('tr').find(".bank_detail_span").hide();

    $(e).closest('tr').find(".card_brand").attr('required',true);
    $(e).closest('tr').find(".card_number").attr('required',true);
    $(e).closest('tr').find(".no_of_installment").attr('required',false);
    $(e).closest('tr').find(".installment_amount").attr('required',false);
    $(e).closest('tr').find(".cheque_number").attr('required',false);
    $(e).closest('tr').find(".bank_detail").attr('required',false);
  }
  else
  {
    $(e).closest('tr').find(".card_brand_span").hide();
    $(e).closest('tr').find(".card_number_span").hide();
    $(e).closest('tr').find(".no_of_installment_span").hide();
    $(e).closest('tr').find(".cheque_number_span").hide();
    $(e).closest('tr').find(".installment_amount_span").hide();
    $(e).closest('tr').find(".bank_detail_span").hide();

    $(e).closest('tr').find(".no_of_installment").attr('required',false);
    $(e).closest('tr').find(".installment_amount").attr('required',false);
    $(e).closest('tr').find(".cheque_number").attr('required',false);
    $(e).closest('tr').find(".bank_detail").attr('required',false);
    $(e).closest('tr').find(".card_brand").attr('required',false);
    $(e).closest('tr').find(".card_number").attr('required',false);
  }
}

function calculat_intallment_amount(e)
{
  var amount = $(e).closest('tr').find(".partial_amount").val();
  var install_cal = parseFloat(amount)/parseFloat(e.value);
  $(e).closest('tr').find(".installment_amount").val(install_cal.toFixed(2));

}

function getInstallmentsAmount(e){
  var gross_amount = $('#gross_amount').val();
  var type = 'modal';
  var max_dis = $('#max_dis').val();
  pids =[];
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
  if( pids.length>0){
        $.ajax({
            url: appurl + "getInstallmentsAmount",
           type: "POST",
            data: "installments=" + e + "&pids=" + pids + "&totalAmount=" + gross_amount +  "&max_dis=" + max_dis+"&required_qty="+required_qty+"&price="+price,
            success:function(info){
              $('#interest_amount').val(info['interestAmountVal']);
              calculationAmount();
              checkPayment();
            }
        });
    }
}

function getInstallmentsInfo(){
  $("#interest-rate-modal").modal('hide');
  var gross_amount = $('#gross_amount').val();
  var installments = $('#installments').val();
  var type = 'modal';
  var max_dis = $('#max_dis').val();
  pids =[];
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
  if( pids.length>0){
        $.ajax({
            url: appurl + "getInstallmentsAmount",
           type: "POST",
            data: "installments=" + installments + "&pids=" + pids + "&type=" + type + "&totalAmount=" + gross_amount +  "&max_dis=" + max_dis+"&required_qty="+required_qty+"&price="+price,
            success:function(info){
              $('#interest-rate-section').html(info);
              $("#interest-rate-modal").modal('show');

            }
        });
    }
}