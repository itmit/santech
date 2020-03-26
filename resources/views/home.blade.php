@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('uploadCatalog') }}">
                        {{ csrf_field() }}

                        <br>

                        <div class="row form-group{{ $errors->has('file') ? ' has-error' : '' }}">

                            <label for="file" class="col-md-4 form-control-file">.zip-папка для импорта</label>
                
                            <div class="col-md-6">
                                <input type="file" name="file" id="file" accept=".zip">
                            </div>
                
                            @if ($errors->has('file'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('file') }}</strong>
                                </span>
                            @endif
                        </div>
                
                        <div class="form-group">
                            <button type="submit" class="btn-card btn-tc-ct">
                                    Загрузить каталог из .zip
                            </button>
                        </div>
                    </form>
                    <ul class="nav nav-tabs">
                        @foreach ($catalogs as $catalog)
                        <li>
                            <a data-toggle="tab" href="#c{{$catalog->id}}" class="active show">{{$catalog->name}}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
