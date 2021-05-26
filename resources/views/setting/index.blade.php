@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Setting') }}</div>

                <div class="card-body">
                        <div class="row">
                            <div class="col-lg-8">

                                @if ($message = Session::get('success_message'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <strong>{{ $message }}</strong>                                               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                <form action="{{ route('setting.update') }}" method="post">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="apiUrl" class="form-label">API URL</label>
                                        <input type="text" value="{{ config('payment.sipay.api_url') }}" name="api_url" class="form-control" id="apiUrl">
                                    </div>
                                    <div class="mb-3">
                                        <label for="apiUrl" class="form-label">MERCHANT ID</label>
                                        <input type="text" value="{{ config('payment.sipay.merchant_id') }}" name="merchant_id" class="form-control" id="apiUrl">
                                    </div>
                                    <div class="mb-3">
                                        <label for="apiUrl" class="form-label">MERCHANT KEY</label>
                                        <input type="text"  value="{{ config('payment.sipay.api_merchant_key') }}" name="merchant_key" class="form-control" id="apiUrl">
                                    </div>

                                    <div class="mb-3">
                                        <label for="apiUrl" class="form-label">APP KEY</label>
                                        <input type="text"  value="{{ config('payment.sipay.app_key') }}" name="app_key" class="form-control" id="apiUrl">
                                    </div>

                                    <div class="mb-3">
                                        <label for="apiUrl" class="form-label">APP SECRET</label>
                                        <input type="text" value="{{ config('payment.sipay.app_secret') }}" name="app_secret" class="form-control" id="apiUrl">
                                    </div>

                                    <button type="submit" class="btn btn-success">Submit</button>
                                </form>

                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('js')
<script>
    $(function(){

    });
</script>
@endsection
