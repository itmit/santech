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
                    <button name="js-catalog-delete" disabled>Удалить каталог</button>
                    <input type="text" name="js-catalog-name" value="" disabled>
                    <button name="js-catalog-rename" disabled>Переименовать каталог</button>
                    <br>

                    <select name="js-category" id="js-category" disabled>
                        <option value="" selected disabled>Выберите категорию</option>
                    </select>
                    <button name="js-category-delete" disabled>Удалить категорию</button>
                    <input type="text" name="js-catalog-name" value="" disabled>
                    <button name="js-category-rename" disabled>Переименовать категорию</button>
                    <br>

                    <table class="table-bordered" style="display: none; width: 100%">
                        <thead style="width: 100%">
                        <tr>
                            <th scope="col">Наименование</th>
                            <th scope="col">Фото</th>
                            <th scope="col">Удалить</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(document).on('change', 'select[name="js-catalog"]', function() {
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
                $('select[name="js-category"]').removeAttr("disabled");
                $('button[name="js-catalog-delete"]').removeAttr("disabled");
                $('button[name="js-catalog-rename"]').removeAttr("disabled");
                $('input[name="js-catalog-name"]').removeAttr("disabled");
                $('input[name="js-catalog-name"]').val($(this).children("option:selected").html());
                $('select[name="js-category').html(result);
            },
            error: function (xhr, err) { 
                console.log("Error: " + xhr + " " + err);
            }
        });
        })

        $(document).on('change', 'select[name="js-category"]', function() {
            let category = $(this).children("option:selected").val();
            $.ajax({
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            dataType: "json",
            url     : 'catalog/getItems',
            data    : {category: category},
            method    : 'post',
            success: function (response) {
                $('table > td').remove();
                result = '';
                response.forEach(element => {
                    result += '<tr>';
                    result += '<td><input type="text" name="item-name" data-i="'+element['id']+'" value="'+element['name']+'"></td>';
                    result += '<td><input type="file" name="item-photo" data-i="'+element['id']+'"></td>';
                    result += '<td><span class="material-icons" name="item-delete" style="cursor: pointer" data="'+element['id']+'">delete</span></td>';
                    result += '</tr>';
                });
                $('button[name="js-category-delete"]').removeAttr("disabled");
                $('tbody').html(result);
                $('table').css('display', 'block');
            },
            error: function (xhr, err) { 
                console.log("Error: " + xhr + " " + err);
            }
        });
        })

        $(document).on('click', 'button[name="js-catalog-delete"]', function() {
            let isDelete = confirm("Удалить каталог? При удалении будут удалены все категории и материалы!");
            if(isDelete)
            {

            }
        })

        $(document).on('click', 'button[name="js-category-delete"]', function() {
            let isDelete = confirm("Удалить категорию? При удалении будут удалены все материалы!");
            if(isDelete)
            {
                
            }
        })
    })
</script>

@endsection
