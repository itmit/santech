@extends('layouts.pdfApp')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1>{{ $entity->name }}</h1>
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
                  @foreach($estimate as $item)
                    <tr>
                      <td>{{ $item->name }}</td>
                      <td>{{ $item }}</td>
                      <td>{{ $item }}</td>
                      <td>{{ $item }}</td>
                    </tr>
                  @endforeach
                  <tr>
                    <td><b>Total</b></td>
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
