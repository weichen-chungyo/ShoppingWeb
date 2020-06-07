@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-md-4 col-md-offset-4">
    <h1>註 冊</h1>
    @if (count($errors) > 0)
    <div class="alert alert-dange">
        @foreach ($errors->all() as $errors)
            <p>{{ $errors }}</p>    
        @endforeach
        
    </div>
    @endif 
    <form action="{{ route('user.signup')}}" method="post">
        <div class="form-group">
            <label for="name"">姓 名</label>
            <input type="text" id="name" name="name" class="form-control">
        </div>
        <div class="form-group">
            <label for="email">信 箱</label>
            <input type="text" id="email" name="email" class="form-control">
        </div>
        <div class="form-group">
            <label for="password">密 碼</label>
            <input type="password" id="password" name="password" class="form-control">
        </div>
        <div class="form-group">
            <label for="password">確認密碼</label>
            <input type="password" id="password" name="password_confirmation" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">註 冊</button>
        {{csrf_field()}}
    </form>
    </div>
</div>
@endsection