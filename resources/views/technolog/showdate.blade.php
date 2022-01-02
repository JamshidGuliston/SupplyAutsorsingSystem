@extends('layouts.app')

@section('content')
<div class="py-4 px-4">
<table class="table table-light py-4 px-4">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">MTT-nomi</th>
                @foreach($ages as $age)
                <th scope="col">{{ $age->age_name }}</th>
                @endforeach
                <th style="width: 70px;">Naklad</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th scope="row"><input type="checkbox" id="bike" name="vehicle" value="gentra"></th>
                <td>12</td>
                <td>455</td>
                <td>364</td>
                <td><i class="far fa-window-close" style="color: red;"></i></td>
                <td><i class=" edites far fa-edit text-info" data-bs-toggle="modal" data-bs-target="#exampleModal" data-kinid="" style="cursor: pointer; margin-right: 16px;"> </i></td>
            </tr>
        </tbody>
    </table>
</div>
@endsection

@section('script')

@endsection