@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Basket') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">Product Name</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Price</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $total = 0;
                            @endphp
                            @foreach($cart_items as $key => $item)
                                @php
                                    $total += $item["item_price"] * $item["item_quantity"];
                                @endphp
                                <tr>
                                    <th scope="row">
                                        <img style="width: 60px" src="{{ $item["item_image"] }}" alt="">
                                    </th>
                                    <td>{{ ucfirst($item["item_name"]) }}</td>
                                    <td>{{ $item["item_quantity"] }}</td>
                                    <td><span class="price">{{ number_format($item["item_price"] * $item["item_quantity"], 2)  }}</span>TL</td>
                                </tr>
                            @endforeach
                            <tr>
                                <th scope="row">&nbsp;</th>
                                <td>&nbsp;</td>
                                <td class="text-right font-weight-bold">Subtotal :</td>
                                <td><span class="total font-weight-bold">{{ number_format($total, 2) }}TL</span> </td>
                            </tr>
                            <tr>
                                <th scope="row">&nbsp;</th>
                                <td>&nbsp;</td>
                                <td class="text-right font-weight-bold">Total :</td>
                                <td><span class="total font-weight-bold">{{ number_format($total, 2) }}TL</span> </td>
                            </tr>
                            </tbody>
                        </table>


                    <hr>

                    <!-- Card Form -->

                        <div class="row">
                            <div class="col-lg-7 mx-auto">
                                <div class="bg-white rounded-lg shadow-sm p-3">
                                    <!-- Credit card form tabs -->
                                    <ul role="tablist" class="nav bg-light nav-pills rounded-pill nav-fill mb-3">
                                        <!--
                                        <li class="nav-item">
                                            <a data-toggle="pill" href="#nav-tab-card" class="nav-link active rounded-pill">
                                                <i class="fa fa-credit-card"></i>
                                                Credit Card
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a data-toggle="pill" href="#nav-tab-paypal" class="nav-link rounded-pill">
                                                <i class="fa fa-paypal"></i>
                                                Paypal
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a data-toggle="pill" href="#nav-tab-bank" class="nav-link rounded-pill">
                                                <i class="fa fa-university"></i>
                                                Bank Transfer
                                            </a>
                                        </li>
                                        -->
                                    </ul>
                                    <!-- End -->


                                    <!-- Credit card form content -->
                                    <div class="tab-content">

                                        <!-- credit card info-->
                                        <div id="nav-tab-card" class="tab-pane fade show active">
                                            @if ($message = Session::get('success_message'))
                                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                    <strong>{{ $message }}</strong>                                               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                            @endif

                                            @if ($message = Session::get('error_message'))
                                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                    <strong>{{ $message }}</strong>                                               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                            @endif

                                            @if ($message = Session::get('warning_message'))
                                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                                    <strong>{{ $message }}</strong>                                               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                            @endif

                                            @if ($data = Session::get('data'))
                                                <pre class="prettyprint">
                                                    {{ collect($data)->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}
                                                </pre>
                                            @endif

                                            <br>
                                            <h4 id="cardNumberHelpBlock" class="form-text text-muted font-weight-bold">
                                                <a onclick="Javascript:$('#form_credit_card').fadeToggle();" data-toggle="collapse" href="#collapseCards" role="button" aria-expanded="false" aria-controls="collapseExample">
                                                    Pay by registered card
                                                </a>
                                            </h4>
                                            <div class="collapse" id="collapseCards">
                                                <div class="card card-body">
                                                    <div class="list-group card-list">
                                                    </div>

                                                    <form id="form_save_card" action="{{ route('payment.store') }}" method="post">
                                                        @csrf
                                                        <div class="inputs">
                                                            <input type="hidden" name="payment_save_card" value="1">
                                                            <input type="hidden" name="total" value="{{ number_format($total, 2) }}">
                                                            <input type="hidden" name="card_token">
                                                            <input type="hidden" name="customer_number" value="{{ $user->customer_number ?? null }}">
                                                            <input type="hidden" name="customer_email" value="{{ $user->email }}">
                                                            <input type="hidden" name="customer_phone" value="{{ $user->phone }}">
                                                            <input type="hidden" name="currency_code" value="TRY">
                                                        </div>
                                                        <button type="submit" class="mt-4 subscribe btn btn-primary btn-block rounded-pill shadow-sm"> Confirm </button>
                                                    </form>

                                                </div>
                                            </div>

                                            <input type="hidden" id="save_card_url" value="{{ route('cards') }}">
                                            <input type="hidden" id="pos_url" value="{{ route('pos') }}">
                                            <input type="hidden" id="installments_url" value="{{ route('installments') }}">
                                            <form id="form_credit_card" method="post" action="{{ route('payment.store') }}" role="form">
                                                @csrf
                                                <input type="hidden" name="total" value="{{ number_format($total, 2) }}">
                                                <div class="form-group">
                                                    <label for="fullname">Full name (on the card)</label>
                                                    <input type="text" name="fullname" placeholder="John Dao" value="John Dao" required class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label for="cardNumber">Card number</label>
                                                    <div class="input-group">
                                                        <input type="text" name="credit_card" value="5406675406675403" placeholder="Your card number" class="form-control" required>
                                                    </div>
                                                    <small id="cardNumberHelpBlock" class="form-text text-muted font-weight-bold">
                                                        <a data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                                                            Installment Information
                                                        </a>
                                                    </small>
                                                    <div class="collapse" id="collapseExample">
                                                        <div class="card card-body">
                                                            <table class="table table-sm pos-table">
                                                                <thead>
                                                                <tr>
                                                                    <th scope="col">#</th>
                                                                    <th scope="col">Card Type</th>
                                                                    <th scope="col">Card Program</th>
                                                                    <th scope="col">Payable Amount</th>
                                                                    <th scope="col">Installments Number</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <div class="form-group">
                                                            <label><span class="hidden-xs">Expiration</span></label>
                                                            <div class="input-group">
                                                                <input type="number" placeholder="MM" value="12" name="expiry_month" class="form-control" required>
                                                                <input type="number" placeholder="YYYY" value="2026" name="expiry_year" class="form-control" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group mb-4">
                                                            <label data-toggle="tooltip" title="Three-digits code on the back of your card">CVV
                                                                <i class="fa fa-question-circle"></i>
                                                            </label>
                                                            <input type="text" placeholder="000" value="000" name="cvv" required class="form-control">
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="row mb-4">

                                                    <div class="col-sm-5">

                                                        <div class="">
                                                            <label class="mr-sm-2" for="installments_number">Installments Number</label>
                                                            <select class="custom-select mr-sm-2" id="installments_number" name="installments_number" required="true">
                                                                <option value="" selected disabled hidden>Choose...</option>
                                                                <option value="1">1</option>
                                                            </select>
                                                        </div>

                                                    </div>
                                                    @if($is_3d == 1)
                                                        <div class="col-sm-3">
                                                            <br>
                                                            <div class="form-group mt-3">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" name="is_3d" value="1" id="invalidCheck2">
                                                                    <label class="form-check-label" for="invalidCheck2">
                                                                        3D Secure
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="col-sm-4">
                                                        <br>
                                                        <a href="#" data-toggle="modal" data-target="#commissionsModal" class="btn btn-info mt-2">Commissions</a>

                                                    </div>
                                                </div>
                                                <div class="row mb-1">
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="save_my_card" value="1" id="invalidCheck3">
                                                                <label class="form-check-label" for="invalidCheck3">
                                                                    Save my card
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="submit" class="subscribe btn btn-primary btn-block rounded-pill shadow-sm"> Confirm  </button>
                                            </form>
                                        </div>
                                        <!-- End -->

                                        <!-- Paypal info -->
                                        <div id="nav-tab-paypal" class="tab-pane fade">
                                            <p>Paypal is easiest way to pay online</p>
                                            <p>
                                                <button type="button" class="btn btn-primary rounded-pill"><i class="fa fa-paypal mr-2"></i> Log into my Paypal</button>
                                            </p>
                                            <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                                            </p>
                                        </div>
                                        <!-- End -->

                                        <!-- bank transfer info -->
                                        <div id="nav-tab-bank" class="tab-pane fade">
                                            <h6>Bank account details</h6>
                                            <dl>
                                                <dt>Bank</dt>
                                                <dd> THE WORLD BANK</dd>
                                            </dl>
                                            <dl>
                                                <dt>Account number</dt>
                                                <dd>7775877975</dd>
                                            </dl>
                                            <dl>
                                                <dt>IBAN</dt>
                                                <dd>CZ7775877975656</dd>
                                            </dl>
                                            <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                                            </p>
                                        </div>
                                        <!-- End -->
                                    </div>
                                    <!-- End -->

                                </div>
                            </div>
                        </div>


                    <!-- Card Form End -->


                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="commissionsModal" data-commissions-url="{{ route('commissions') }}" data-currency-code="TRY" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-lg modal-dialog modal-dialog-top modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commissionsModalLabel">Commissions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <table class="table table-bordered table-sm">
                    <thead>
                    <tr>
                        <th scope="col">Card Program</th>
                        <th scope="col">Installment</th>
                        <th scope="col">Merchant Commission</th>
                        <th scope="col">User Commission</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="editCardModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-md modal-dialog modal-dialog-top modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commissionsModalLabel">Edit Card</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="message">

                </div>
                <form id="form_editcard" data-base-url="{{ url("/") }}" method="post" onsubmit="return false;">
                    @csrf
                    <div class="form-group">
                        <label for="card_holder_name">Card Holder Name</label>
                        <input type="text" class="form-control" id="card_holder_name" disabled name="card_holder_name" aria-describedby="emailHelp">
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="submit" form="form_editcard" class="btn btn-success">Update</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


@endsection


@section('js')
    <script>
        // Get Installments
        function getInstallments()
        {
            var installments_url = $('#installments_url').val();
            var data = {
                '_token' : $('meta[name="csrf-token"]').attr('content'),
            };

            var post = $.post(installments_url,data);
            post.done(function(xhr){
                if(xhr.data.length > 0){
                    var optionsHtml = '<option value="" selected disabled hidden>Choose...</option>';
                    xhr.data.forEach(function(item, index){
                        optionsHtml += '<option value="'+item+'">'+item+'</option>';
                        $('#installments_number').html(optionsHtml);
                    });
                }
            });
        }


        // Get Pos
        function getPos()
        {
            var pos_url = $('#pos_url').val();
            var data = {
                '_token' : $('meta[name="csrf-token"]').attr('content'),
                'credit_card' : $('input[name=credit_card]').val(),
                'amount' : {{ number_format($total, 2) }},
                'currency_code' : 'TRY',
            };

            var post = $.post(pos_url,data);
            post.done(function(xhr){
                if(xhr.data.length > 0){
                    var rowHtml = '';
                    xhr.data.forEach(function(item, index){
                        rowHtml +=  '<tr>' +
                            '<th scope="row">'+(index+1)+'</th>' +
                            '<td>'+item.card_type+'</td>' +
                            '<td>'+item.card_program+'</td>' +
                            '<td>'+item.payable_amount+'</td>' +
                            '<td>'+item.installments_number+'</td>' +
                            '</tr>';
                    });

                    $('table.pos-table tbody').html(rowHtml);
                }
            });
        }

        // Get Commissions
        function getCommissions(url, inputs, el)
        {
            var get = $.getJSON(url, inputs);
            get.done(function(xhr){
                var modal_body = el.find('.modal-body table tbody');
                if(Object.keys(xhr.data).length > 0){
                    var trElements = '';
                    Object.keys(xhr.data).forEach(function(key){
                        trElements +=
                            '<tr>' +
                            '<td>'+xhr.data[key][0].card_program+'</td>' +
                            '<td>'+xhr.data[key][0].installment+'</td>' +
                            '<td>'+xhr.data[key][0].merchant_commission_fixed+'</td>' +
                            '<td>'+xhr.data[key][0].user_commission_fixed+'</td>' +
                            '</tr>';
                    });
                    modal_body.html(trElements)
                }
            });
        }

        // Get cards
        function getCards()
        {
            $("#cardNumberHelpBlock").hide();
            var url = $('#save_card_url').val();

            $.getJSON(url, function(xhr){
                if(xhr.data){
                    var cardListHtml = '';
                    if(xhr.data.length > 0){
                        $("#cardNumberHelpBlock").show();
                    }
                    xhr.data.forEach(function(item){
                        console.log(item);

                        cardListHtml += '<div data-card-token="'+item.card_token+'" class="list-group-item list-group-item-action card-list-item">' +
                            '<div class="d-flex w-100 justify-content-between">' +
                            '<h5 class="mb-1">'+item.card_number+'</h5>' +
                            '</div>' +
                            '<p class="mb-1">'+item.customer_name+'</p>' +
                            '<small class="">'+item.bank_code+'</small>' +
                            '<button class="btn btn-danger btn-sm float-right ml-1 btn-delete-card" data-base-url="{{ url('/') }}" data-card-token="'+item.card_token+'" data-csrf-token="{{ csrf_token() }}">Delete</button>' +
                            '<button class="btn btn-success btn-sm float-right btn-edit-card" data-card-token="'+item.card_token+'" data-card-holder-name="'+item.customer_name+'">Edit</button>' +
                            '</div>';

/*
                        cardListHtml += '<a href="#" data-card-token="'+item.card_token+'" class="list-group-item list-group-item-action card-list-item">' +
                           '<div class="d-flex w-100 justify-content-between">' +
                                '<h5 class="mb-1">'+item.binlist.bank.name+'</h5>' +
                            '</div>' +
                            '<p class="mb-1">'+item.binlist.scheme+'</p>' +
                            '<small class="text-muted">'+item.bin+'</small>' +
                        '</a>';
*/
                    });

                    $('.card-list').html(cardListHtml);
                }
                //console.log(xhr);
            });
        }


        $(function (){
            getInstallments();
            getCards()

            $('body').on("click",'.card-list-item', function(event){
                event.preventDefault();
                $('input[name=card_token]').val($(this).data('card-token'));

                if($(".card-list-item").length > 1){
                    $('.card-list-item').not($(this)).removeClass('active');
                }

                $(this).toggleClass('active');

                if(! $(this).hasClass('active')){
                    $('input[name=card_token]').val('');
                }

                console.log($(this).data('card-token'));
            });


            // Credit card keyup function
            $('input[name=credit_card]').keyup(function(event){
                var cv = $(thisPaymen).val().length;
                if(cv > 5){
                    if(cv < 7){
                        getPos();
                    }
                }
            }).focusout(function(){

                var cv = $(this).val().length;
                if(cv > 5){
                    getPos();
                }
            });

            // Commissions modal show event
            $('#commissionsModal').on('show.bs.modal', function (event) {
                var modal_body = $(this).find('.modal-body table tbody');
                modal_body.html('');

                var commissions_url = $(this).data('commissions-url');
                var inputs = {
                    'currency_code' : $(this).data('currency-code')
                }
                getCommissions(commissions_url, inputs, $(this));
            });
            /*
                    $('#form_credit_card').on('submit', function(event){
                        event.preventDefault();

                        var url = $(this).attr('action');
                        var data = $(this).serialize();

                        var post = $.post(url,data);
                        post.done(function(xhr){
                            console.log(xhr);
                        });

                    });
            */


            $('body').on('click', '.btn-edit-card', function(){
                var card_holder_name = $(this).data('card-holder-name');
                var card_token = $(this).data('card-token');

                $("#form_editcard #card_holder_name").val(card_holder_name);

                $('#editCardModal form input[name=card-token]').remove();
                $('#editCardModal form').append('<input id="card-token" name="card-token" type="hidden" value="'+card_token+'" /> ');

                $('#editCardModal').modal('show');
            });

            $('body').on('click', '.btn-delete-card', function(){
                var thiss = $(this);
                var card_token = $(this).data('card-token');
                var csrf_token = $(this).data('csrf-token');
                var url = $(this).data('base-url')+"/deletecard/"+card_token;
                var data = {
                    '_token' : csrf_token,
                };

                /*
                console.log(thiss.parent().parent().find('.list-group-item').length);
                if(thiss.parent().parent().find('.list-group-item').length > 1){
                    thiss.parent().remove();
                }else{
                    $("#cardNumberHelpBlock").hide();
                    $('#collapseCards').removeClass('show');
                    $('#form_credit_card').show();
                }

*/


                var post = $.post(url, data);
                post.done(function(xhr){
                    if(xhr.status === "success" ){

                        console.log(thiss.parent().parent().find('.list-group-item').length);
                        if(thiss.parent().parent().find('.list-group-item').length > 1){
                            thiss.parent().remove();
                        }else{
                            $("#cardNumberHelpBlock").hide();
                            $('#collapseCards').removeClass('show');
                            $('#form_credit_card').show();
                        }
                    }
                });

            });

            $('body').on('submit', '#form_editcard', function(){
                var card_token = $(this).find("#card-token").val();
                var url = $(this).data("base-url") + "/editcard/" + card_token;
                var data = $(this).serialize();

                var post = $.post(url, data);
                post.done(function(xhr){
                    if(xhr.status === "success"){
                        $("#message").html('' +
                            '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            '<strong>'+xhr.status_description+'</strong>' +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span>' +
                            '</button>' +
                            '</div>');
                    }else{
                        $("#message").html('' +
                            '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            '<strong>'+xhr.status_description+'</strong>' +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span>' +
                            '</button>' +
                            '</div>');
                    }
                });
            });




        });
    </script>
@endsection
