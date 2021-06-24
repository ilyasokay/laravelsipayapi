@extends('layouts.app')

@section('content')
<div id="loading-content" style="display: none;">
    <div class="spinner-border float-right ml-2" style="width: 1.5rem; height: 1.5rem;" role="status"></div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Healtcheck') }}</div>

                <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">


                                <div class="accordion" id="accordionExample">
                                    <div class="card">
                                        <div class="card-header" id="headingOne">
                                            <h2 class="mb-0">
                                                <button class="btn btn-link btn-block text-left text-decoration-none" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                    Api Config and Card Infos
                                                    <i class="fas fa-chevron-circle-down float-right" style="font-size: 26px"></i>
                                                </button>
                                            </h2>
                                        </div>

                                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                            <div class="card-body">

                                                <div class="row">
                                                    <div class="col-lg-6">

                                                        <div class="card">
                                                            <h5 class="card-header">Merchant Info</h5>
                                                            <div class="card-body">
                                                                <div class="merchant-info">
                                                                    <div class="mb-3">
                                                                        <label for="apiUrl" class="form-label">API URL</label>
                                                                        <select class="form-control" name="api_base_url">
                                                                            @foreach(config('payment.sipay.api_url_array') as $url)
                                                                            <option value="{{ $url }}">{{ $url }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="apiUrl" class="form-label">MERCHANT ID</label>
                                                                        <input type="text" value="{{ config('payment.sipay.merchant_id') }}" name="merchant_id" class="form-control">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="apiUrl" class="form-label">MERCHANT KEY</label>
                                                                        <input type="text"  value="{{ config('payment.sipay.api_merchant_key') }}" name="merchant_key" class="form-control">
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label for="apiUrl" class="form-label">APP KEY</label>
                                                                        <input type="text"  value="{{ config('payment.sipay.app_key') }}" name="app_key" class="form-control">
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label for="apiUrl" class="form-label">APP SECRET</label>
                                                                        <input type="text" value="{{ config('payment.sipay.app_secret') }}" name="app_secret" class="form-control">
                                                                    </div>
                                                                </div>




                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="col-lg-6">

                                                        <div class="card">
                                                            <h5 class="card-header">Card Info</h5>
                                                            <div class="card-body">
                                                                <h5 class="card-title">Special title treatment</h5>
                                                                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                                                                <a href="#" class="btn btn-primary">Go somewhere</a>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    <hr>
                        <div class="row">
                            <div class="col-lg-12">

                                <div class="alert alert-secondary" role="alert">
                                  <button class="btn btn-success font-weight-bold btn-run" data-method="post" data-url="{{ route('healtcheck.token') }}">
                                      <i class="fas fa-play mr-1"></i>
                                      POST
                                      <div class="loading float-right"></div>
                                  </button>
                                    <span class="font-weight-bold h4 align-middle ml-2">/api/token</span>
                                    <span class="text-black-50 font-weight-bold align-middle ml-2">Get Authentication Token</span>
                                    <button class="btn btn-primary float-right btn-collapse"><i class="fas fa-chevron-down"></i></button>
                                    <button class="btn btn-dark float-right mr-2 btn-collapse">Form</button>

                                    <div style="display: none;" class="result bg-light rounded mt-2 p-2"></div>
                                    <div style="display: none;" class="form_data bg-light rounded mt-2 p-2">

                                    </div>
                                </div>


                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    pre {
        white-space: pre-wrap;
        overflow-x: auto;
    }

    pre.success {
        color: green;
    }

    pre.error {
        color: red;
    }

    pre.warning {
        color: red;
    }
</style>
@endsection


@section('js')
<script>
    $(function(){

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });



        var loading_content = $('#loading-content').html();
        $('.btn-run').on('click', function(){
             var dis = $(this);
             dis.parent().find('.result').html('').hide();
             dis.parent().removeClass('alert-success alert-danger').addClass('alert-secondary');
             dis.find('.loading').html(loading_content);

             var url = dis.data('url');
             var method = dis.data('method');
             var apiBaseUrl = $('select[name=api_base_url]').val();

             var base_data = {
                 'api_base_url' : apiBaseUrl,
                 'merchant_id' : $('input[name=merchant_id]').val(),
                 'merchant_key' : $('input[name=merchant_key]').val(),
                 'app_key' : $('input[name=app_key]').val(),
                 'app_secret' : $('input[name=app_secret]').val()
             };

             var jqxhr = $.ajax({
                 url: url,
                 method: method,
                 data: base_data,
                 dataType: "json"
             });

             jqxhr.done(function(xhr){

                 dis.find('.loading').html('');
                 var alertClass = xhr.status;
                 if(xhr.status === "error"){
                     alertClass = "danger";
                 }
                 dis.parent().removeClass('alert-secondary').addClass('alert-'+ alertClass);

                 var content = "";
                 if(xhr.dataType === "json"){
                     content = '<pre class="prettyprint '+ xhr.status +'">'+JSON.stringify(xhr.data, undefined, 4)+'</pre>';
                 }

                 if(xhr.dataType === "html"){
                     content = xhr.data;
                 }

                 dis.parent().find('.result').html(content);
                 dis.parent().find('.result').show(100);
             });

            jqxhr.fail(function(xhr){
                console.log("Fail:");
                console.log(xhr);

                console.log(typeof xhr.responseText);

                dis.find('.loading').html('');
                dis.parent().removeClass('alert-secondary').addClass('alert-danger');

                var errorMessage = "Result Not Found";
                if(xhr.responseText !== ""){
                    try {
                        var parseError = JSON.parse(xhr.responseText);
                        errorMessage = parseError.message;
                        var responseText = "";
                    } catch (err) {
                        if(typeof xhr.responseText === "string"){
                            errorMessage = "Response Type Error";
                            responseText = xhr.responseText;
                        }else{
                            errorMessage = xhr.responseText;
                        }
                    }
                }

                var data = {
                    'status': xhr.status,
                    'statusText': xhr.statusText,
                    'error': errorMessage,
                    'responseType': typeof xhr.responseText,
                    'responseText': responseText
                };

                var content = '<pre class="prettyprint error">'+JSON.stringify(data, undefined, 4)+'</pre>';
                dis.parent().find('.result').html(content);
                dis.parent().find('.result').show(100);
            });
        });

        $('.btn-collapse').on('click', function(){
            var dis = $(this);
            var html = dis.parent().find('.result').html();
            if(html === '' || html === 'Result Not Fount'){
                dis.parent().find('.result').html('Result Not Fount');
            }

            dis.parent().find('.result').fadeToggle(400);
        });

    });
</script>
@endsection
