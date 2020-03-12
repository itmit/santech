@extends('layouts.pdfApp')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1>{{ $estimate->entity }}</h1>
            <table>
                <thead>
                  <tr>
                    <th>Наименование</th>
                    <th>Кол-во</th>
                    <th>Цена</th>
                    <th>Итого</th>
                  </tr>
                </thead>
                <tbody>
                  {{-- @foreach($estimate as $item)
                    <tr>
                      <td>{{ $customer->id }}</td>
                      <td>{{ $customer->name }}</td>
                      <td>{{ $customer->email }}</td>
                      <td>{{ $customer->phone }}</td>
                    </tr>
                  @endforeach --}}
                  <tr>
                    <td><b>Итого</b></td>
                    <td></td>
                    <td></td>
                    {{-- <td>{{ $total }}</td> --}}
                  </tr>
                </tbody>     
            </table>         
        </div>
    </div>
</div>
@endsection
