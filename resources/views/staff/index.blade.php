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


                    @if($members)
                    <ul>
                        @foreach($members as $member)
                            <li>
                                <span>{{ucwords($member->name)}} | {{$member->email}}</span>
                            </li>
                        @endforeach
                    </ul>
                    @else
                        <div class="alert alert-success" role="alert">
                            No staff member found <a href="{{route('staff.create')}}">Click Here </a> to create;
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
