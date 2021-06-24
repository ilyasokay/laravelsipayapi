@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ __('Sub Merchants') }}
                    <a class="btn btn-primary btn-sm float-right" href="{{ route('submerchant.create') }}"><i class="fas fa-plus"></i> New Sub Merchant</a>
                </div>

                <div class="card-body">
                        <div class="row">
                            <div class="col-lg-8">

                                @if ($message = Session::get('success_message'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <strong>{{ $message }}</strong>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif


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
