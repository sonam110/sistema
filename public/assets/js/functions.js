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
          if($("#payment_through").val()=='Partial Payment')
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

    $(e).closest('tr').find(".no_of_installment").attr('required',true);
    $(e).closest('tr').find(".installment_amount").attr('required',true);
    $(e).closest('tr').find(".cheque_number").attr('required',false);
    $(e).closest('tr').find(".bank_detail").attr('required',false);
  }
  else
  {
    $(e).closest('tr').find(".no_of_installment_span").hide();
    $(e).closest('tr').find(".cheque_number_span").hide();
    $(e).closest('tr').find(".installment_amount_span").hide();
    $(e).closest('tr').find(".bank_detail_span").hide();

    $(e).closest('tr').find(".no_of_installment").attr('required',false);
    $(e).closest('tr').find(".installment_amount").attr('required',false);
    $(e).closest('tr').find(".cheque_number").attr('required',false);
    $(e).closest('tr').find(".bank_detail").attr('required',false);
  }
}

function calculat_intallment_amount(e)
{
  var amount = $(e).closest('tr').find(".partial_amount").val();
  var install_cal = parseFloat(amount)/parseFloat(e.value);
  $(e).closest('tr').find(".installment_amount").val(install_cal.toFixed(2));
}