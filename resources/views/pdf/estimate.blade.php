@extends('layouts.pdfApp')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- <h1>{{ $entity->name }}</h1> --}}
            <h1>Наименование объекта</h1>
            <table>
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Count</th>
                    <th>Price</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody>
                  {{-- @foreach($estimate as $item)
                    <tr>
                      <td>{{ $item['name'] }}</td>
                      <td>{{ $item['count'] }}</td>
                      <td>{{ $item['amount'] }}</td>
                      <td>{{ $item['price'] }}</td>
                    </tr>
                  @endforeach --}}
                  <tr>
                    <td>Наименование</td>
                    <td>Количество}</td>
                    <td>Цена</td>
                    <td>Итого</td>
                  </tr>
                  <tr>
                    <td>Наименование</td>
                    <td>Количество}</td>
                    <td>Цена</td>
                    <td>Итого</td>
                  </tr>
                  <tr>
                    <td><b>Total</b></td>
                    <td></td>
                    <td></td>
                    {{-- <td>{{ $total }}</td> --}}
                    <td>100500р/td>
                  </tr>
                </tbody>     
            </table>         
        </div>
    </div>
</div>
@endsection
