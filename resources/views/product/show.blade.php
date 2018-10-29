@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Product Details</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif


                    @if($product)
                    <ul>
                        <li>
                            <span> {{$product->name}} | &#8377;{{$product->price}}</span>
                            <div>{!! $product->description !!}</div>
                        </li>
                        <br/>
                    </ul>
                    @else
                        <div class="alert alert-success" role="alert">
                            No product found <a href="{{route('product.create')}}">Click Here </a> to create;
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
