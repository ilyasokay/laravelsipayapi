@extends('app')

@section('main')

    <div class="container py-5">

        <!-- For demo purpose -->
        <div class="row mb-4">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4">Credit card form</h1>
            </div>
        </div>
        <!-- End -->


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
                                <p class="alert alert-success">{{ $message }}</p>
                            @endif

                            @if ($message = Session::get('error_message'))
                                <p class="alert alert-error">{{ $message }}</p>
                            @endif

<br>
                            <input type="hidden" id="installment_url" value="{{ route('installment') }}">
                            <form id="form_credit_card" method="post" action="{{ route('payment') }}" role="form">
                                @csrf
                                <div class="form-group">
                                    <label for="fullname">Full name (on the card)</label>
                                    <input type="text" name="fullname" placeholder="John Dao" required class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="cardNumber">Card number</label>
                                    <div class="input-group">
                                        <input type="text" name="credit_card" placeholder="Your card number" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <label><span class="hidden-xs">Expiration</span></label>
                                            <div class="input-group">
                                                <input type="number" placeholder="MM" name="expiry_month" class="form-control" required>
                                                <input type="number" placeholder="YY" name="expiry_year" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group mb-4">
                                            <label data-toggle="tooltip" title="Three-digits code on the back of your card">CVV
                                                <i class="fa fa-question-circle"></i>
                                            </label>
                                            <input type="text" placeholder="000" name="cvv" required class="form-control">
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



@endsection

@section('js')
<script>
    // Get Pos
    function getPos()
    {
        var installment_url = $('#installment_url').val();
        var data = {
            'credit_card' : $('input[name=credit_card]').val(),
            'amount' : 248.00,
            'currency_code' : 'TRY',
        };

        var post = $.post(installment_url,data);
        post.done(function(xhr){
            if(xhr.data.length > 0){
                var optionsHtml = '<option value="" selected disabled hidden>Choose...</option>';
                xhr.data.forEach(function(item, index){
                    optionsHtml += '<option value="'+item.installments_number+'">'+item.installments_number+'</option>';
                    $('#installments_number').html(optionsHtml);
                });
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


    $(function (){

        // Credit card keyup function
        $('input[name=credit_card]').keyup(function(event){
            var cv = $(this).val().length;
            if(cv > 5){
                if(cv < 7){
                    getPos();
                }
            }
            else
            {
                var optionsHtml = '<option value="" selected disabled hidden>Choose...</option>';
                optionsHtml += '<option value="1">1</option>';
                $('#installments_number').html(optionsHtml);
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

    });
</script>
@endsection
