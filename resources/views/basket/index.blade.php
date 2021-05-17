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
                                    <td>
                                        <div class="input-group w-50">
                                            <input type="text" class="form-control" value="{{ $item["item_quantity"] }}"> <button data-basket-url="{{ route('basket.add',[$item["item_id"]]) }}" data-product-id="{{ $item["item_id"] }}" class="update-cart-button btn btn-success btn-sm">Save</button>
                                        </div>
                                    </td>
                                    <td><span class="price">{{ number_format($item["item_price"] * $item["item_quantity"], 2)  }}</span>TL</td>
                                    <td><a class="btn btn-danger" href="{{ route('basket.item.remove',[$item["item_id"]]) }}"><i class="fa fa-trash"></i></a></td>
                                </tr>
                            @endforeach
                            <tr>
                                <th scope="row">&nbsp;</th>
                                <td>&nbsp;</td>
                                <td class="text-right font-weight-bold">Total :</td>
                                <td><span class="total font-weight-bold">{{ number_format($total, 2) }}</span> </td>
                            </tr>
                            </tbody>
                        </table>

                <div class="container">
                    <a href="{{ route('payment.index') }}" class="btn btn-success float-right">Continue to Pay</a>
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
        $('button.update-cart-button').on('click', function(){

            var quantity = $(this).prev().val();

            var url = $(this).data('basket-url');
            var data = {
                "_token" : $('meta[name="csrf-token"]').attr('content') ,
                "quantity" : quantity,
                "type" : "update"
            };

            var post = $.post(url, data);
            post.done(function(xhr){
                $('#cart-items-count').text(xhr.basket_count);
                window.location = location.href;
/*
                Swal.fire({
                    title: '',
                    icon: 'success',
                    html:
                        '<b>'+xhr.message+'</b> <br><br>, ' +
                        '<a class="btn btn-outline-primary btn-lg" href="Javascript:Swal.close();">Continue Shopping</a> ' +
                        '<a class="btn btn-outline-success btn-lg" href="{{ route('basket.index') }}">Go to Cart</a> ' ,
                    showConfirmButton: false,
                })
*/
            });

        });
    });
</script>
@endsection
