@extends('layouts.pdfApp')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <p><b>{{ $entity->name }}</b></p>
            {{-- <h1>Наименование объекта</h1> --}}
            <table style="width: 100%; text-align: center">
                <thead>
                  <tr>
                    <th>Наименование</th>
                    <th>Кол-во</th>
                    <th>Цена</th>
                    <th>Итого</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($estimate as $item)
                    <tr>
                      <td>{{ $item['name'] }}</td>
                      <td>{{ $item['count'] }}</td>
                      <td>{{ $item['amount'] }}</td>
                      <td>{{ $item['price'] }}</td>
                    </tr>
                  @endforeach
                  <tr>
                    <td><b>Итого</b></td>
                    <td></td>
                    <td></td>
                    <td>{{ $total }}</td>
                  </tr>
                </tbody>     
            </table>         
        </div>
    </div>
</div>
@endsection
