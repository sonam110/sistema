<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>

    <style>
    .invoice-box {
        max-width: 800px;
        margin: auto;
        padding: 10px;
        border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, .15);
        font-size: 16px;
        line-height: 24px;
        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color: #555;
    }
    .text-center{
    	text-align: center!important;
    }
    .uppercase{
    	text-transform: uppercase;
    }
    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
    }

    .invoice-box table td {
        padding: 5px;
        vertical-align: top;
    }

    .invoice-box table tr td:nth-child(2) {
        text-align: left;
    }

    .invoice-box table tr.top table td {
        padding-bottom: 20px;
    }

    .invoice-box table tr.top table td.title {
        font-size: 45px;
        line-height: 45px;
        color: #333;
    }

    .invoice-box table tr.information table td {
        padding-bottom: 10px;
    }

    .invoice-box table tr.heading td {
        background: #eee;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
    }

    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }

    .invoice-box table tr.item td{
        border-bottom: 1px solid #eee;
    }

    .invoice-box table tr.item.last td {
        border-bottom: none;
    }

    .invoice-box table tr.total td:nth-child(2) {
        border-top: 2px solid #eee;
        font-weight: bold;
    }

    @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td {
            width: 100%;
            display: block;
            text-align: center;
        }

        .invoice-box table tr.information table td {
            width: 100%;
            display: block;
            text-align: center;
        }
    }

    /** RTL **/
    .rtl {
        direction: rtl;
        font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
    }

    .rtl table {
        text-align: right;
    }

    .rtl table tr td:nth-child(2) {
        text-align: left;
    }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="4">
                    <table>
                        <tr>
                            <td class="title" width="400px">
                                <img src="https://image3.mouthshut.com/images/imagesp/925719693s.jpg" height="80px" width="200px">
                            </td>

                            <td>
                            	<strong>Gandhi Medical College</strong><br>
								Sultania Road<br>
								Bhopal-462001<br>
								Phone: 0755-2540590, 4050000<br>
                                Fax: 0755-2541376<br>
                                Email: deangmc_bpl@yahoo.co.in
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td width="5%">
                    #
                </td>
                <td>
                    Medicine
                </td>

                <td width="10%">
                    <center>Dosage</center>
                </td>

                <td width="15%">
                    <center>No. of days</center>
                </td>
            </tr>
            @foreach($data->PrescriptionMedicines as $key => $rec)
            <tr class="item">
                <td>
                    {{$key+1}}
                </td>
                <td>
                    {{$rec->medicine_name}}
                    @if(!empty($rec->comment))
                        <br>
                        ({{$rec->comment}})
                    @endif
                </td>
                <td>
                	<center>{{$rec->takeperday}}</center>
                </td>

                <td>
                	<center>{{$rec->howMuchDays}}</center>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</body>
</html>