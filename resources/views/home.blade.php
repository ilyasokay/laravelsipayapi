@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Home') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        @if ($message = Session::get('success_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>{{ $message }}</strong>                                               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if ($message = Session::get('error_message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
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

                        <div class="row">
                            @foreach($products as $product)
                                <div class="col-sm-12 col-md-6 col-lg-4 mb-4">
                                    <div class="card" style="min-width: 12rem;">
                                        <img src="{{ $product->image_url }}" class="card-img-top" alt="...">
                                        <div class="card-body">
                                            <h5 class="card-title font-weight-bold">{{ ucfirst($product->price) }}TL</h5>
                                            <h5 class="card-title">{{ ucfirst($product->name) }}</h5>
                                            <div style="min-height: 50px;">
                                                <p class="card-text">{{ ucfirst($product->description) }}</p>
                                            </div>

                                            <button data-basket-url="{{ route('basket.add',[$product->id]) }}" data-product-id="{{ $product->id }}" type="button" class="btn btn-lg btn-block btn-outline-primary add-cart-button">Add to Cart</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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
        $('button.add-cart-button').on('click', function(){

            var url = $(this).data('basket-url');
            var data = { "_token" : $('meta[name="csrf-token"]').attr('content') , "quantity" : 1 };

            var post = $.post(url, data);
            post.done(function(xhr){
                $('#cart-items-count').text(xhr.basket_count);


                Swal.fire({
                    title: '',
                    icon: 'success',
                    html:
                        '<b>'+xhr.message+'</b> <br><br>, ' +
                        '<a class="btn btn-outline-primary btn-lg" href="Javascript:Swal.close();">Continue Shopping</a> ' +
                        '<a class="btn btn-outline-success btn-lg" href="{{ route('basket.index') }}">Go to Cart</a> ' ,
                    showConfirmButton: false,

                })

            });

        });
    });
</script>
@endsection
