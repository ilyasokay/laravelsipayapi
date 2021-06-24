@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('User ID: ') . ($merchant->id ?? "") }}</div>

                <div class="card-body">

                Burası index
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
