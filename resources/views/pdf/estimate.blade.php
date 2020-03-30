@extends('layouts.pdfApp')

@section('content')
<div class="container" style="width: 100%">
    {{-- <div class="row justify-content-center">
        <div class="col-md-12"> --}}
            <p style="text-align: left"><b>{{ $entity->name }}</b></p>
            {{-- <h1>Наименование объекта</h1> --}}
            <table class="table-bordered" style="width: 100%;">
                <thead>
                  <tr>
                    <th style="text-align: center; width: 10%">№ п/п</th>
                    <th style="text-align: center;"">Наименование</th>
                    <th style="text-align: center; width: 10%">Кол-во</th>
                    {{-- <th style="text-align: right">Цена</th>
                    <th style="text-align: right">Итого</th> --}}
                  </tr>
                </thead>
                <tbody>
                  <?php $i=1?>
                  @foreach($estimate as $item)
                    <tr>
                      <td style="text-align: right">{{ $i }}</td>
                      <td style="text-align: left">{{ $item['name'] }}</td>
                      <td style="text-align: right">{{ $item['count'] }}</td>
                      {{-- <td style="text-align: right">{{ $item['amount'] }}</td>
                      <td style="text-align: right">{{ $item['price'] }}</td> --}}
                    </tr>
                    <?php $i++?>
                  @endforeach
                  {{-- <tr>
                    <td style="text-align: left"><b>Итого</b></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: left">{{ $total }}</td>
                  </tr> --}}
                </tbody>     
            </table>         
        {{-- </div>
    </div> --}}
</div>
@endsection
