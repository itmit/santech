@extends('layouts.pdfApp')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <p style="text-align: left"><b>Наименование объекта</b></p>
            {{-- <h1>Наименование объекта</h1> --}}
            <div class="table-responsive">
                <table class="table-bordered" style="width: 100%;">
                    <thead>
                    <tr>
                        <th style="text-align: left">№ п/п</th>
                        <th style="text-align: left">Наименование</th>
                        <th style="text-align: right">Кол-во</th>
                        {{-- <th style="text-align: right">Цена</th>
                        <th style="text-align: right">Итого</th> --}}
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align: right">1</td>
                            <td style="text-align: left">Штука 1</td>
                            <td style="text-align: right">5</td>
                        </tr>
                        <tr>
                            <td style="text-align: right">2</td>
                            <td style="text-align: left">Штука 2</td>
                            <td style="text-align: right">10</td>
                        </tr>
                        <tr>
                            <td style="text-align: right">3</td>
                            <td style="text-align: left">Штука 3</td>
                            <td style="text-align: right">15</td>
                        </tr>
                    </tbody>     
                </table> 
            </div>        
        </div>
    </div>
</div>

@endsection
