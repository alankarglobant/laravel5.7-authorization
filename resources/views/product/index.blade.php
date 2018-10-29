@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Products</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif


                    @if($products)
                    <ul>
                    @foreach($products as $product)
                        <li>
                            <span> {{$product->name}} | &#8377;{{$product->price}}</span>
                            <div>
                                <span>
                                @can('update', $product) <a href="{{ route('product.edit',['id' => $product->id])}}">Edit</a> | @endcan
                                @can('view', $product) <a href="{{ route('product.show',['id' => $product->id])}}">View</a> | @endcan
                                @can('delete', $product) <a href="javascript:void(0);"
                                onclick="event.preventDefault();
                                                     document.getElementById('delete-form-{{$product->id}}').submit();">Remove</a></span>

                                <form id="delete-form-{{$product->id}}" action="{{ route('product.destroy',['id' => $product->id]) }}" method="POST" style="display: none;">
                                    @method('DELETE')
                                    @csrf
                                </form>
                                @endcan
                            </div>
                            <div>{!! $product->description !!}</div>
                        </li>
                        <br/>
                    @endforeach
                    </ul>
                    @else
                        <div class="alert alert-success" role="alert">
                            No products found <a href="{{route('product.create')}}">Click Here </a> to create;
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
