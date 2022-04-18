@extends('layouts.app')

@section('css')
<style>
.year {
    text-align: center;
}
.month,
.day {
    margin: 10px 20px;
    display: flex;
    justify-content: left;
}

.month__item{
    width: calc(100% / 12);
    text-align: center;
    border-bottom: 1px solid #000;
}

.month__item + .month__item {
    /* border-left: 1px solid #000; */
}
.day__item{
    background-color: #ecf6f1;
    text-align: center;
    vertical-align: middle;
    min-width: 34px;
    padding: 5px;
    margin-left: 5px;
    border-radius: 50%;
}

.month__item, .day__item{
    color: black;
    cursor: context-menu;
    /* border: 1px solid #87706a; */
    text-decoration: none;
}
.active{
    background-color: #23b242;
    color: #fff;
}
.month__item:hover,
.day__item:hover{
    background-color: #23b242;
    color: #fff;
    transition: all .5s;
    cursor: pointer;
}
</style>
@endsection
@section('leftmenu')
    @include('storage.sidemenu'); 
@endsection
@section('content')

<div class="date">
    <!-- <div class="year">2020</div> -->
    <div class="month">
        @foreach($months as $month)
        @if($month->month_active == 1)
            <a href="#" class="month__item active">{{ $month->month_name }}</a>
        @else
            <a href="#" class="month__item">{{ $month->month_name }}</a>
        @endif
        @endforeach
    </div>
    <div class="day">
        <a href="#" class="day__item">1</a>
        <a href="#" class="day__item">2</a>
        <a href="#" class="day__item">3</a>
        <a href="#" class="day__item">4</a>
        <a href="#" class="day__item">5</a>
        <a href="#" class="day__item">6</a>
        <a href="#" class="day__item">7</a>
        <a href="#" class="day__item">8</a>
        <a href="#" class="day__item">9</a>
        <a href="#" class="day__item">10</a>
        <a href="#" class="day__item">11</a>
        <a href="#" class="day__item">12</a>
        <a href="#" class="day__item">13</a>
        <a href="#" class="day__item">14</a>
        <a href="#" class="day__item">15</a>
        <a href="#" class="day__item">16</a>
        <a href="#" class="day__item">17</a>
        <a href="#" class="day__item">18</a>
        <a href="#" class="day__item">19</a>
        <a href="#" class="day__item">20</a>
        <a href="#" class="day__item">21</a>
        <a href="#" class="day__item">22</a>
        <a href="#" class="day__item">23</a>
        <a href="#" class="day__item">24</a>
        <a href="#" class="day__item">25</a>
    </div>
</div>
<div class="py-4 px-4">
    <!-- @if(isset($orders[0]->day_number))
    <h4>Oyning {{ $orders[0]->day_number."-sanasi" }}</h4>
    @endif -->
    <table class="table table-light py-4 px-4">
        <thead>

            <tr>
                <th scope="col">ID</th>
                <th scope="col">Title</th>
                <th scope="col">Date</th>
                <th style="width: 40px;">PDF</th>
            </tr>
        </thead>
        <tbody>
            @foreach($group as $item)
            <tr>
                <td>{{ $item['id'] }}</td>
                <td>{{ $item['group_name'] }}</td>
                <td>{{ $item['day_number'].".".$item['month_name'].".".$item['year_name'] }}</td>
                <td>
                    <a href="/storage/document/{{ $item->id }}" target="_blank">pdf</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <a href="/storage/home">Orqaga</a>
</div>

@endsection

@section('script')
@endsection