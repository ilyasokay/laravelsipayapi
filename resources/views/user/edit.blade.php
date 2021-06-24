@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Customer ID: ') . ($customer->id ?? "") }}</div>

                <div class="card-body">

                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif



                        @if ($message = session('success_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>{{ $message }}</strong>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if ($message = session('error_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>{{ $message }}</strong>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <form action="{{ route('user.update',[$customer->id]) }}" method="post">
                            {{ method_field('put') }}
                            @csrf
                            <div class="mb-3">
                                <label for="input" class="form-label">Customer Name</label>
                                <input type="text" value="{{ $customer->name ?? "" }}" name="name" class="form-control" id="input">
                            </div>
                            <div class="mb-3">
                                <label for="input" class="form-label">Customer Email</label>
                                <input type="text" value="{{ $customer->email ?? "" }}" name="email" class="form-control" id="input">
                            </div>
                            <div class="mb-3">
                                <label for="input" class="form-label">Customer Phone</label>
                                <input type="text" value="{{ $customer->phone ?? "" }}" name="phone" class="form-control" id="input">
                            </div>
                            <div class="mb-3">
                                <label for="input" class="form-label">Customer Number</label>
                                <input type="text" value="{{ $customer->customer_number ?? "" }}" name="customer_number" class="form-control" id="input">
                            </div>

                            <button type="submit" class="btn btn-success">Submit</button>
                        </form>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('js')
<script>

</script>
@endsection
