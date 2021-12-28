@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div id="timeline">
        <!-- //date -->
        @if(!empty($date) and count($date)>2)
        <div class="dot" id="one">
            <span>{{ $date[count($date)-1]->day_number }}</span>
            <date>{{ $date[count($date)-1]->month_name }}</date>
        </div>
        @endif
        @if(!empty($date) and count($date)>1)
        <div class="dot" id="two">
            <span>{{ $date[1]->day_number }}</span>
            <date>{{ $date[1]->month_name }}</date>
        </div>
        @endif
        @if(!empty($date) and count($date)>0)
        <div class="dot" id="three">
            <span>{{ $date[0]->day_number }}</span>
            <date>{{ $date[1]->month_name }}</date>
        </div>
        @endif
        @if($date[0]->day_number != date("d", $tomm))
        <div class="dot" id="four" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal">
            <span>{{ date("d", $tomm) }}</span>
            <date>{{ date("M", $tomm) }}</date>
        </div>
        @endif
        @if($date[0]->day_number == date("d", $tomm))
        <div class="dot" id="four2" data-bs-toggle="modal" data-bs-target="#exampleModal">
            <span>{{ date("d", $tomm) }}</span>
            <date>{{ date("M", $tomm) }}</date>
        </div>
        @endif
        <div class="inside"></div>
    </div>

    <!-- Modals -->
    <!-- Button trigger modal -->
    <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
    Launch demo modal
    </button> -->

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            ...
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Save changes</button>
        </div>
        </div>
    </div>
    </div>
    <!-- end date -->

    <div class="row g-3 my-2">
        @foreach($kingardens as $item)
        <div class="col-md-2">
            <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                <div>
                    <h3 class="fs-2">{{ $item->id }}</h3>
                    <p class="fs-5">Products</p>
                </div>
                <i class="fas fa-gift fs-1 primary-text border rounded-full secondary-bg p-3"></i>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row my-5">
        <h3 class="fs-4 mb-3">Recent Orders</h3>
        <div class="col">
            <table class="table bg-white rounded shadow-sm  table-hover">
                <thead>
                    <tr>
                        <th scope="col" width="50">#</th>
                        <th scope="col">Product</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Price</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">1</th>
                        <td>Television</td>
                        <td>Jonny</td>
                        <td>$1200</td>
                    </tr>
                    <tr>
                        <th scope="row">2</th>
                        <td>Laptop</td>
                        <td>Kenny</td>
                        <td>$750</td>
                    </tr>
                    <tr>
                        <th scope="row">3</th>
                        <td>Cell Phone</td>
                        <td>Jenny</td>
                        <td>$600</td>
                    </tr>
                    <tr>
                        <th scope="row">4</th>
                        <td>Fridge</td>
                        <td>Killy</td>
                        <td>$300</td>
                    </tr>
                    <tr>
                        <th scope="row">5</th>
                        <td>Books</td>
                        <td>Filly</td>
                        <td>$120</td>
                    </tr>
                    <tr>
                        <th scope="row">6</th>
                        <td>Gold</td>
                        <td>Bumbo</td>
                        <td>$1800</td>
                    </tr>
                    <tr>
                        <th scope="row">7</th>
                        <td>Pen</td>
                        <td>Bilbo</td>
                        <td>$75</td>
                    </tr>
                    <tr>
                        <th scope="row">8</th>
                        <td>Notebook</td>
                        <td>Frodo</td>
                        <td>$36</td>
                    </tr>
                    <tr>
                        <th scope="row">9</th>
                        <td>Dress</td>
                        <td>Kimo</td>
                        <td>$255</td>
                    </tr>
                    <tr>
                        <th scope="row">10</th>
                        <td>Paint</td>
                        <td>Zico</td>
                        <td>$434</td>
                    </tr>
                    <tr>
                        <th scope="row">11</th>
                        <td>Carpet</td>
                        <td>Jeco</td>
                        <td>$1236</td>
                    </tr>
                    <tr>
                        <th scope="row">12</th>
                        <td>Food</td>
                        <td>Haso</td>
                        <td>$422</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection