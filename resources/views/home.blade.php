@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
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
                    <hr>
                    <select name="js-catalog" id="js-catalog">
                        <option value="" selected disabled>Выберите каталог</option>
                        @foreach ($catalogs as $catalog)
                            <option value="{{$catalog->id}}">{{$catalog->name}}</option>
                        @endforeach
                    </select>
                    <button>Удалить каталог</button>
                    <br>
                    <select name="js-category" id="js-category" disabled>
                        <option value="" selected disabled>Выберите категорию</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(document).on('change', 'select[name="js-catalog"]', function() {
            $('select[name="js-category"]').removeAttr("disabled");
            let catalog = $(this).children("option:selected").val();
            $.ajax({
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            dataType: "json",
            url     : 'catalog/getCategories',
            data    : {catalog: catalog},
            method    : 'post',
            success: function (response) {
                $('select[name="js-category"] > option').remove();
                result = '<option value="" selected disabled>Выберите категорию</option>';
                response.forEach(element => {
                    result += '<option value='+element['id']+'>';
                    result += element['name'];
                    result += '</option>';
                });
                $('select[name="js-category').html(result);
            },
            error: function (xhr, err) { 
                console.log("Error: " + xhr + " " + err);
            }
        });
        })
    })
</script>

@endsection
