@extends('layouts.master')

@section('title')
     {{$product->name}}
@endsection

@section('content')
    @if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
    @endif
    @if (count($errors) > 0)
    <div class="alert alert-danger">
        @foreach ($errors->all() as $errors)
            <p>{{ $errors }}</p>    
        @endforeach
    </div>
    @endif 
    <p><a href="{{ url('/shop') }}">{{__('shop.home')}}</a> / {{ $product->name }}</p>
        <h1>{{ $product->name }}</h1>

        <hr>

        <div class="row">
            <div class="col-md-4">
                <img src="{{asset('storage/'.$product->file->filename.'')}}" alt="product" class="img-responsive">
            </div>

            <div class="col-md-8">
                <h3>{{__('shop.price') }}：${{  presentPrice($product->price) }}</h3> 
                <h4>{{__('shop.stock') }}：{{ $product->amount }}</h4>
                @if ($product->amount <= 0)
                <h4 class="text-danger">{{__('shop.oopsnostock') }} </h4>   
                @endif
                @if ($product->buy_yn == 'N')
                <h4 class="text-danger">{{__('shop.notyet') }} </h4>   
                @endif
                <form action="{{ route('cart.store') }}" method="POST" class="side-by-side">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="{{ $product->id }}">
                    <input type="hidden" name="name" value="{{ $product->name }}">
                    <input type="hidden" name="price" value="{{ $product->price }}">
                    <div style="font-size:18px" >{{__('shop.quantity') }}:</div>
                    <div style="position:relative;">
                        <span style="margin-left:100px;width:18px;">
                        <select id='quantity' style="width:100px;margin-left:-100px;height:26px" onfocus="selectFocus(this)"  onchange="this.parentNode.nextSibling.value=this.value">
                            @for ($i = 1; $i <= (($product->amount >= 100) ? 100 : $product->amount ); $i++)
                            <option onclick="selectClick(this)" value="{{$i}}">{{$i}}</option>    
                            @endfor    
                        </select>
                        </span><input id='quantityinput'name="quantity" value="1" style="z-index:1;width:80px;position:absolute;left:0px;">
                    </div>
                    <br>
                    <br>
                    
                    <input {{($product->buy_yn == 'N' || $product->amount <= 0) ? 'disabled' : ""}} type="submit" class="btn btn-success btn-lg" value="{{__('shop.addcart') }}">
                </form>
                <br><br>

                {{ $product->description }}
            </div> <!-- end col-md-8 -->
        </div> <!-- end row -->

        <div class="spacer"></div>

        <div class="row">
            <h3>{{__('shop.like') }}</h3>

            @foreach ($interested as $product)
                <div class="col-md-3">
                    <div class="thumbnail">
                        <div class="caption text-center">
                            <a href="{{ route('shop.show', [$product->id]) }}"><img src="{{asset('storage/'.$product->file->filename.'')}}" alt="product" class="img-responsive"></a>
                            <a href="{{ route('shop.show', [$product->id]) }}"><h3>{{ $product->name }}</h3>
                            <p>${{  presentPrice($product->price) }}</p>
                            </a>
                        </div> <!-- end caption -->
                    </div> <!-- end thumbnail -->
                </div> <!-- end col-md-3 -->
            @endforeach

        </div> <!-- end row -->

        <div class="spacer"></div>
@endsection
@section('scripts')
<script  src="{{ asset('js/productshow.js') }}"></script>
@endsection