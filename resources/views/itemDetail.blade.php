@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Панель управления</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div>
                    <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('updateItem') }}">
                        {{ csrf_field() }}
                        <div class="row">
                            <input type="text" name="name" value="{{$item->name}}" class="form-control" require>
                        </div>
                        <div class="row">
                            <img src="{{$item->photo}}">
                        </div>
                        <div class="row">
                            <input type="file" name="photo" class="form-control">
                        </div>
                        <div class="row">
                            <input type="submit" value="Сохранить">
                        </div>
                    </form>
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
