<?php

if (!empty($setting['logo'])) {
    $image_path = public_path('images/' . $setting['logo']);
    if (file_exists($image_path)) {
        $image_data = file_get_contents($image_path);
        $logo = 'data:image/' . pathinfo($image_path, PATHINFO_EXTENSION) . ';base64,' . base64_encode($image_data);
    } else {
        $logo  = null;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <title>Sale _{{$sale['Ref']}}</title>
{{--      <link rel="stylesheet" href="{{asset('/css/pdf_style.css')}}" media="all" />--}}
       <style>
           @page {
               margin: 0cm 0cm;
           }

           body {
               background-size: cover;
               background-repeat: no-repeat;
               padding: 10px 25px 0px 25px;
               height: 100%;
               position: relative;
               margin: 0 auto;
               color: black;
               font-size: 14px;
               font-family: 'dejavu sans', sans-serif;

           }

           .clearfix:after {
               content: "";
               display: table;
               clear: both;
           }

           a {
               color: #0087C3;
               text-decoration: none;
           }

           #comp {
               font-size: 15px;
               color: black;
               font-weight: 600;
           }


           #total {
               float: right;
           }

           #total>table {
               border-collapse: collapse;
               border-spacing: 0;
               margin-bottom: 20px;
           }

           #total>table td {
               padding: 10px;
               background: #EEEEEE;
               line-height: 10px;
           }

           #total>table th {
               padding: 6px;
               background: darkturquoise;
               border-bottom: 1px solid #FFFFFF;
           }

           #total>table th {
               color: #fff;
               font-size: 12px;
               font-weight: 300;
           }

           #total>table tr:last-child {
               border-top: 2px solid;
           }

           #Title-heading {
               float: right;
               margin-right: 160px;
               margin-top: 40px;
               padding-left: 6px;
               min-height: 90px;
               font-size: 85%;
               font-size: 22px;
               color:#8A2BE2;
               font-weight: 900;
           }

           #logo {
               float: left;
               margin-top: 8px;
               width: 280px;
           }

           #logo img {
               height: 70px;
               width: 70px;
           }

           #company {

               float: right;
               float: right;
               padding-left: 6px;
               min-height: 90px;
               font-size: 85%;
               padding: 1.5%;
           }

           .name {
               font-size: 22px;
           }

           #client .to {
               color: cornflowerblue;
               font-size: 22px;
           }

           h2.name {
               font-size: 1.4em;
               font-weight: normal;
               margin: 0;
               background: darkturquoise;
           }

           #invoice>table {
               width: 100%;
               border-collapse: collapse;
               border-spacing: 0;
               margin-bottom: 20px;
           }

           #invoice>table td {
               padding: 14px;
               background: #EEEEEE;
               line-height: 20px;
           }

           #invoice>table th {
               padding: 6px;
               border-bottom: 1px solid #FFFFFF;
               background: blueviolet;
               color: #fff;
               font-size: 13px;
               font-weight: bold;
           }

           #client>table {
               width: 100%;
               border-collapse: collapse;
               border-spacing: 0;
               margin-bottom: 20px;
           }

           #client>table td {
               padding: 14px;
               background: #EEEEEE;
               line-height: 20px;
           }

           #client>table th {
               padding: 6px;
               background: blueviolet;
               color: #fff;
               font-size: 13px;
               font-weight: bold;
           }

           #client {
               padding: 0!important;
               float: left;
               width: 50%;
               min-height: 90px;
               font-size: 85%;
           }

           #invoice {
               margin-right: 0px;
               width: 40%;
               float: right;
               padding-left: 6px;
               min-height: 90px;
               font-size: 85%;
               padding: 0%;
           }

           #invoice h1 {
               color: #0087C3;
               font-size: 16px;
               line-height: 1em;
               font-weight: normal;
               margin: 0 0 10px 0;
           }

           #invoice .to {
               color: cornflowerblue;
           }

           #invoice .date {
               font-size: 1.1em;
               color: #777777;
           }

           #details_inv>table {
               width: 100%;
               border-collapse: collapse;
               border-spacing: 0;
               margin-bottom: 20px;
           }

           #details_inv>table td {
               padding: 6px;
               background: #EEEEEE;
               text-align: left;
           }

           #details_inv>table th {
               padding: 6px;
               text-align: left;
               border-bottom: 1px solid #FFFFFF;
               color: #fff;
               background: blueviolet;
               color: #fff;
               font-size: 13px;
               font-weight: bold;
           }

           #details_inv>table td {
               text-align: left;
           }

           #details_inv>table td h3 {
               color: #57B223;
               font-size: 1.2em;
               font-weight: normal;
               margin: 0 0 0.2em 0;
           }

           #details_inv>table .no {
               color: #FFFFFF;
               font-size: 1.6em;
               background: #57B223;
           }

           #details_inv>table .Ref {
               text-align: left;
               font-size: 16px!important;
           }

           #details_inv>table .Payment {
               text-align: right;
               font-size: 16px!important;
           }

           #details_inv>table .mode {
               text-align: center;
               font-size: 16px!important;
           }

           #details_inv>table td.unit,
           #details_inv>table td.qty,
           #details_inv>table td.total {
               font-size: 1.2em;
           }

           #details_inv>table tbody tr:last-child td {
               border: none;
           }

           #details_inv>table tfoot td {
               padding: 10px 20px;
               background: #FFFFFF;
               border-bottom: none;
               font-size: 1.2em;
               white-space: nowrap;
               border-top: 1px solid #AAAAAA;
           }

           #details_inv>table tfoot tr:first-child td {
               border-top: none;
           }

           #details_inv>table tfoot tr:last-child td {
               color: #57B223;
               font-size: 1.4em;
               border-top: 1px solid #57B223;
           }

           #details_inv>table tfoot tr td:first-child {
               border: none;
           }

           #thanks {
               font-size: 2em;
               margin-bottom: 50px;
               margin-top: 228px;
           }

           #signature {
               color: #777777;
               width: 100%;
               height: 30px;
               position: absolute;
               bottom: 30;
               padding: 8px 0;
               text-align: center;
           }

           #notices {
               padding-left: 6px;
               border-left: 6px solid #0087C3;
           }

           #notices .notice {
               font-size: 1.2em;
           }

           footer {
               color: #777777;
               width: 100%;
               height: 30px;
               position: absolute;
               bottom: 0;
               border-top: 1px solid #AAAAAA;
               padding: 8px 0;
               text-align: center;
           }

           #paiment {
               border: 2px solid;
               padding: 24px;
               width: 659px;
           }
       </style>
   </head>

   <body>
      <header class="clearfix">
         <div id="logo">
         <img src="{{$logo}}">
         </div>
         <div id="company">
            <div><strong> Date : </strong>{{$sale['date']}}</div>
            <div><strong> Number : </strong> {{$sale['Ref']}}</div>
            <div><strong> Status : </strong> {{$sale['statut']}}</div>
            <div><strong> Payment Status : </strong> {{$sale['payment_status']}}</div>
         </div>
         <div id="Title-heading">
            Sale  : {{$sale['Ref']}}
         </div>
         </div>
      </header>
      <main>
         <div id="details" class="clearfix">
            <div id="client">
               <table class="table-sm">
                  <thead>
                     <tr>
                        <th class="desc">Customer Info</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>
                           <div><strong>Full Name :</strong> {{$sale['client_name']}}</div>
                           <div><strong>Phone :</strong> {{$sale['client_phone']}}</div>
                           <div><strong>Email :</strong>  {{$sale['client_email']}}</div>
                           <div><strong>Address :</strong>   {{$sale['client_adr']}}</div>
                           @if($sale['client_tax'])<div><strong>Tax Number :</strong>  {{$sale['client_tax']}}</div>@endif
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
            <div id="invoice">
               <table class="table-sm">
                  <thead>
                     <tr>
                        <th class="desc">Company Info</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>
                           <div id="comp">{{$setting['CompanyName']}}</div>
                           <div><strong>Phone :</strong>  {{$setting['CompanyPhone']}}</div>
                           <div><strong>Email :</strong>  {{$setting['email']}}</div>
                           <div><strong>Address :</strong>  {{$setting['CompanyAdress']}}</div>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
         <div id="details_inv">
            <table  class="table-sm">
               <thead>
                  <tr>
                     <th>Image</th>
                     <th>PRODUCT</th>
                     <th>UNIT PRICE</th>
                     <th>QUANTITY</th>
                     <th>DISCOUNT</th>
                     <th>TAX</th>
                     <th>TOTAL</th>
                  </tr>
               </thead>
               <tbody>
                  @foreach ($details as $detail)
                  <tr>
                      <td><img src="{{$detail['image'] }}" style="width: 50px; height: 50px; object-fit: cover;" alt=""/></td>
                     <td>
                        <span>{{$detail['code']}} ({{$detail['name']}})</span>
                           @if($detail['is_imei'] && $detail['imei_number'] !==null)
                              <p>IMEI/SN : {{$detail['imei_number']}}</p>
                           @endif
                     </td>
                     <td>{{$detail['price']}} </td>
                     <td>{{$detail['quantity']}}/{{$detail['unitSale']}}</td>
                     <td>{{$detail['DiscountNet']}} </td>
                     <td>{{$detail['taxe']}} </td>
                     <td>{{$detail['total']}} </td>
                  </tr>
                  @endforeach
               </tbody>
            </table>
         </div>
         <div id="total">
            <table>
               <tr>
                  <td>Order Tax</td>
                  <td>{{$sale['TaxNet']}} </td>
               </tr>
               <tr>
                  <td>Discount</td>
                  <td>{{$sale['discount']}} </td>
               </tr>
               <tr>
                  <td>Shipping</td>
                  <td>{{$sale['shipping']}} </td>
               </tr>
               <tr>
                  <td>Total</td>
                  <td>{{$symbol}} {{$sale['GrandTotal']}} </td>
               </tr>

               <tr>
                  <td>Paid Amount</td>
                  <td>{{$symbol}} {{$sale['paid_amount']}} </td>
               </tr>

               <tr>
                  <td>Due</td>
                  <td>{{$symbol}} {{$sale['due']}} </td>
               </tr>
            </table>
         </div>
         <div id="signature">
            @if($setting['is_invoice_footer'] && $setting['invoice_footer'] !==null)
               <p>{{$setting['invoice_footer']}}</p>
            @endif
         </div>
      </main>
   </body>
</html>
