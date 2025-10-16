@extends('layouts.app')

@section('css')
<style>
    /* GLOBAL STYLES
    -------------------------------------------------- */
    
    /* Maxsulotlar jadvali uslublari */
    .card {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .card-header {
        padding: 15px 20px;
        border-bottom: 3px solid rgba(255, 255, 255, 0.2);
    }
    
    .table-responsive {
        overflow-x: auto;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
        transition: background-color 0.3s ease;
    }
    
    .badge {
        border-radius: 5px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    /* CURRENT BALANCE, INCOME & EXPENSES DISPLAY
    -------------------------------------------------- */
    #topbar-balance, #topbar-income, #topbar-expenses{
        background-color: #f2efef; /* Old browsers */
            background: -moz-linear-gradient(top,  #f2efef 0%, #e2e2e2 100%); /* FF3.6+ */
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f2efef), color-stop(100%,#e2e2e2)); /* Chrome,Safari4+ */
            background: -webkit-linear-gradient(top,  #f2efef 0%,#e2e2e2 100%); /* Chrome10+,Safari5.1+ */
            background: -o-linear-gradient(top,  #f2efef 0%,#e2e2e2 100%); /* Opera 11.10+ */
            background: -ms-linear-gradient(top,  #f2efef 0%,#e2e2e2 100%); /* IE10+ */
            background: linear-gradient(to bottom,  #f2efef 0%,#e2e2e2 100%); /* W3C */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f2efef', endColorstr='#e2e2e2',GradientType=0 ); /* IE6-9 */
            ;
    }

    /* Balance */
    #topbar-balance{color: #1C1C72;
        
        margin-top: 10px;
        padding-left: 10px;
        padding-top: 11px;
        padding-bottom: 11px;
        -webkit-border-radius: 10px;
        -moz-border-radius: 10px;
        border-radius: 10px;
        border: 2.5px solid #F49037;
        width: 470px;
        float: left;
    }
    /* Income */
    #topbar-income, #topbar-expenses{
        color: green;
        margin-top: 10px;
        margin-left: 6px;
        padding: 3px;
        padding-left: 10px;
        -webkit-border-radius: 10px;
        -moz-border-radius: 10px;
        border-radius: 10px;
        border: 2px solid #CCC9C9;
        width: 450px;
        float: left;
        font-size: 18px;

    }
    /* Expense */
    #topbar-expenses{
        color: #ED0300;
        margin-top: -14px;
        border: 2px solid #CCC9C9;
    }


    /* INPUT FORM
    -------------------------------------------------- */
    /* form{
        height: 66px;
        width: 950px;
        font-size: 15px;
        line-height: 24px;
        font-weight: bold;
        color: #1C1A88;
        text-decoration: none;
        -webkit-border-radius: 10px;
        -moz-border-radius: 10px;
        border-radius: 10px;
        padding-left: 10px;
        padding-bottom: 15px;
        border: 1px solid #999;
        border: inset 1px solid #333;
        -webkit-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3);
        -moz-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3);
        box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3);
        float: left;
        margin-bottom: 10px;
        margin-top: -7px;
    } */
    /* Description Line */
    .input-note{
    margin-right: 415px;
    }
    .input-income{
        margin-right: 53px;
    }
    .input-expense{
        margin-right: 55px;
    }

    /* Entry Line */
    #input-date-bar{
        position: relative;
        top: -5px;	
    }
    
    #input-date-bar{
        width: 80px;
    }

    /* Sumbit Button */
    input.button {
        position: relative;
        top: -10px;
        width:100px;
        background: #f7be54; /* Old browsers */
            background: -moz-linear-gradient(top,  #f7be54 0%, #f7a241 44%, #f7852d 100%); /* FF3.6+ */
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f7be54), color-stop(44%,#f7a241), color-stop(100%,#f7852d)); /* Chrome,Safari4+ */
            background: -webkit-linear-gradient(top,  #f7be54 0%,#f7a241 44%,#f7852d 100%); /* Chrome10+,Safari5.1+ */
            background: -o-linear-gradient(top,  #f7be54 0%,#f7a241 44%,#f7852d 100%); /* Opera 11.10+ */
            background: -ms-linear-gradient(top,  #f7be54 0%,#f7a241 44%,#f7852d 100%); /* IE10+ */
            background: linear-gradient(to bottom,  #f7be54 0%,#f7a241 44%,#f7852d 100%); /* W3C */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f7be54', endColorstr='#f7852d',GradientType=0 ); /* IE6-9 */
        color: #34346D;
        font-family: Tahoma, Geneva, sans-serif;
        font-weight: bold;
        font-size: 14px;
        height:35px;
        -webkit-border-radius: 20px;
        -moz-border-radius: 20px;
        border-radius: 20px;
        border: 0px;
        text-shadow: 0.0em 0.7px #FFCA97;
        cursor: pointer;
    }

    input.button:hover {
        background: #f4b849; /* Old browsers */
            background: -moz-linear-gradient(top,  #f4b849 0%, #f4973a 44%, #f47d2e 100%); /* FF3.6+ */
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f4b849), color-stop(44%,#f4973a), color-stop(100%,#f47d2e)); /* Chrome,Safari4+ */
            background: -webkit-linear-gradient(top,  #f4b849 0%,#f4973a 44%,#f47d2e 100%); /* Chrome10+,Safari5.1+ */
            background: -o-linear-gradient(top,  #f4b849 0%,#f4973a 44%,#f47d2e 100%); /* Opera 11.10+ */
            background: -ms-linear-gradient(top,  #f4b849 0%,#f4973a 44%,#f47d2e 100%); /* IE10+ */
            background: linear-gradient(to bottom,  #f4b849 0%,#f4973a 44%,#f47d2e 100%); /* W3C */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f4b849', endColorstr='#f47d2e',GradientType=0 ); /* IE6-9 */
    }

    input.button:active{
        background: #f2ae3a; /* Old browsers */
            background: -moz-linear-gradient(top,  #f2ae3a 0%, #f4973a 44%, #f27121 100%); /* FF3.6+ */
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f2ae3a), color-stop(44%,#f4973a), color-stop(100%,#f27121)); /* Chrome,Safari4+ */
            background: -webkit-linear-gradient(top,  #f2ae3a 0%,#f4973a 44%,#f27121 100%); /* Chrome10+,Safari5.1+ */
            background: -o-linear-gradient(top,  #f2ae3a 0%,#f4973a 44%,#f27121 100%); /* Opera 11.10+ */
            background: -ms-linear-gradient(top,  #f2ae3a 0%,#f4973a 44%,#f27121 100%); /* IE10+ */
            background: linear-gradient(to bottom,  #f2ae3a 0%,#f4973a 44%,#f27121 100%); /* W3C */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f2ae3a', endColorstr='#f27121',GradientType=0 ); /* IE6-9 */
    }
    /* TABLE 
    -------------------------------------------------- */
    table{
        border-collapse:collapse;
        margin:auto;
        position:relative;
        width: 100%;
        text-align: center;
    }
    table, th, td{
        border: 1px solid black;
    }
    th{
        background-color:  #3a3a3a; /* Old browsers */
            background: -moz-linear-gradient(top,  #3a3a3a 0%, #333333 100%); /* FF3.6+ */
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#3a3a3a), color-stop(100%,#333333)); /* Chrome,Safari4+ */
            background: -webkit-linear-gradient(top,  #3a3a3a 0%,#333333 100%); /* Chrome10+,Safari5.1+ */
            background: -o-linear-gradient(top,  #3a3a3a 0%,#333333 100%); /* Opera 11.10+ */
            background: -ms-linear-gradient(top,  #3a3a3a 0%,#333333 100%); /* IE10+ */
            background: linear-gradient(to bottom,  #3a3a3a 0%,#333333 100%); /* W3C */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#3a3a3a', endColorstr='#333333',GradientType=0 ); /* IE6-9 */
            ;
        color: #F3F3F3;
        padding: 10px;
    }
    td{
        vertical-align:bottom;
        padding-left: 10px;
        padding-top: 5px;
        padding-bottom: 5px;
    }

    table, th{
        padding: 10px;
        border-collapse: collapse;
        background-color: white;
    }
    /* Section Widths */

    /* Delete Button */
    button{
        cursor: pointer;
        margin: 3px 3px 3px 3px;
        padding-bottom: 3px;
        width: 74px;
        height: 40px;
        font-family: Tahoma, Geneva, sans-serif;
        font-weight: bold;	
        background: #21cd49; /* Old browsers */
            background: linear-gradient(to bottom, #21cd49 0%,#32d23a 44%,#46ab2b 100%);
        color: #1B1988;
        border: 0.5px solid #888889;
        text-shadow: 0.0em 0.7px #FFFEFA;
    }

    button:hover{
        background: #21cd49; /* Old browsers */
            background: linear-gradient(to bottom, #21cd49 0%,#32d23a 44%,#46ab2b 100%);
        color: #3B3B3D;
    }

    button:active{
        background: #bfbfbf; /* Old browsers */
            background: -moz-linear-gradient(top,  #bfbfbf 0%, #bfbfbf 44%, #939393 100%); /* FF3.6+ */
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#bfbfbf), color-stop(44%,#bfbfbf), color-stop(100%,#939393)); /* Chrome,Safari4+ */
            background: -webkit-linear-gradient(top,  #bfbfbf 0%,#bfbfbf 44%,#939393 100%); /* Chrome10+,Safari5.1+ */
            background: -o-linear-gradient(top,  #bfbfbf 0%,#bfbfbf 44%,#939393 100%); /* Opera 11.10+ */
            background: -ms-linear-gradient(top,  #bfbfbf 0%,#bfbfbf 44%,#939393 100%); /* IE10+ */
            background: linear-gradient(to bottom,  #bfbfbf 0%,#bfbfbf 44%,#939393 100%); /* W3C */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#bfbfbf', endColorstr='#939393',GradientType=0 ); /* IE6-9 */
        color: #3B3B3D;
    }

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
<!-- deleteModal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">O'chirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body foodcomposition"> 
                <label>Miqdori</label>
                <input type="hidden" id="ddebt_id" name="ddebt_id" class="form-control" ><br>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn editsub btn-success">O'chirish</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- Delete -->
<!-- EditModal -->
<div class="modal editesmodal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="{{route('storage.editegroup')}}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">O'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body foodcomposition"> 
            	<input type="text" class="form-select"  id="title" name="nametitle"><br>
                <select id='daySelect' name="editedayid" class="form-select" aria-label="Default select example" required>
                    @foreach($days as $row)
                        <option value='{{ $row->id }}'>{{ $row->day_number.'.'.$row->month_name.'.'.$row->year_name }}</option>
                    @endforeach
                </select><br>
                <input type="hidden" id="group_id" name="group_id" class="form-control">
                <input type="hidden" id="gyear_id" name="year_id" class="form-control">
                <input type="hidden" id="gmonth_id" name="month_id" class="form-control" >
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn editsub btn-success">Saqlash</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- EDIT -->
<!-- Add residual -->
<div class="modal fade" id="addresidual" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Продукт қўшиш</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body addfood">  
                <form id="add-form" action="" method="get">
                    <div class="row">
                        <div class="col-md-6">
                            <span class="input-note">Махсулот:</span>
                            <select id="input-notebar" class="form-select" required>
                                @foreach($products as $row)
                                    <option value="{{$row['id']}}">{{$row['product_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <span class="input-note">::</span>
                            <select class="form-select" required>
                               <option>----</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <span class="input-income">Оғирлиги ёки Дона:</span>
                            <br>
                            <input id="input-expensebar" class="form-control" type="text" onkeypress="javascript:return isNumber(event)" placeholder="kg yoki ta">        
                        </div>
                        <div class="col-md-3">
                            <span class="input-expense">Келган нархи:</span>
                            <br>
                            <input id="input-incomebar" class="form-control" type="number">
                        </div>
                        <div class="col-md-3">
                            <i id="additem" style="margin-top: 35px; cursor: pointer" class="icon fas fa-plus" aria-hidden="false"></i>
                            <!-- <input  style="margin-top: 35px;" class="button" type="button" value="+"> -->
                        </div>
                    </div>
                </form> 
                <br>
                <!-- TABLE -->
                <form method="POST" action="{{route('storage.addr_products')}}">
                    @csrf
                    <input type="hidden" id="titleid" name="month_id" value="{{ $id }}">
                    <table id="test1">
                        <thead>
                            <tr>
                                <th id="note">Mahsulot</th>
                                <th id="expense">Og'irlik</th>
                                <th id="income">Narxi</th>
                                <th>O'chirish</th>
                            </tr>
                        </thead>
                            <tbody id="table-body">
                            </tbody>
                    </table>
                    <br>
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-4">
                            <input type="text" name="title" class="form-control" placeholder="Izoh" required>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" name="date_id" required>
                                <option value="">--Sana--</option>
                                @foreach($start as $row)
                                    <option value="{{$row['id']}}">{{$row['day_number'].".".$row['month_name'].".".$row['year_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-4">
                        </div>
                        <div class="col-md-4">
                            <input type="checkbox" id="residual" name="residual" value="True">
                            <label for="residual"> Qoldiq</label>
                            <br>
                            <button type="submit" class="form-control">Qo'shish</button>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                </form>  
            </div>
            <hr>
        </div>
    </div>
</div>
<!-- Add Product -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Продукт қўшиш</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body addfood">  
                <form id="add-form" action="" method="get">
                    <div class="row">
                        <div class="col-md-6">
                            <span class="input-note">Махсулот:</span>
                            <select id="input-note-bar" class="form-select" required>
                                @foreach($products as $row)
                                    <option value="{{$row['id']}}">{{$row['product_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <span class="input-note">Do'kon:</span>
                            <select id="get_shop_select" class="form-select" required>
                                @foreach($shops as $row)
                                    <option value="{{$row['id']}}">{{$row['shop_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <span class="input-income">Бирлик:</span>
                            <br>
                            <input id="input-expense-bar" class="form-control" type="text" onkeypress="javascript:return isNumber(event)" placeholder="kg yoki ta">        
                        </div>
                        <div class="col-md-3">
                            <span class="input-expense">Келган нархи:</span>
                            <br>
                            <input id="input-income-bar" class="form-control" type="number">
                        </div>
                        <div class="col-md-3">
                            <span class="input-expense">Berilgan summa:</span>
                            <br>
                            <input id="input-summa-bar" class="form-control" type="number" value="0" disabled>
                        </div>
                        <div class="col-md-3">
                            <i id="add-item" style="margin-top: 35px; cursor: pointer" class="icon fas fa-plus" aria-hidden="false"></i>
                            <!-- <input  style="margin-top: 35px;" class="button" type="button" value="+"> -->
                        </div>
                    </div>
                </form> 
                <br>
                <!-- TABLE -->
                <form method="POST" action="{{route('storage.addproducts')}}">
                    @csrf
                    <input type="hidden" id="titleid" name="month_id" value="{{ $id }}">
                    <table id="test1">
                        <thead>
                            <tr>
                                <th id="note">Mahsulot</th>
                                <th id="expense">Og'irlik</th>
                                <th id="income">Narxi</th>
                                <th id="shop">Do'kon</th>
                                <th id="pay">To'landi</th>
                                <th>O'chirish</th>
                            </tr>
                        </thead>
                            <tbody id="tablebody">
                            </tbody>
                    </table>
                    <br>
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-4">
                            <input type="text" name="title" class="form-control" placeholder="Izoh" required>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" name="date_id" required>
                                <option value="">--Sana--</option>
                                @foreach($start as $row)
                                    <option value="{{$row['id']}}">{{$row['day_number'].".".$row['month_name'].".".$row['year_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-4">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="form-control">Qo'shish</button>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                </form>  
            </div>
            <hr>
        </div>
    </div>
</div>
<div class="date">
    <div class = "year first-text fw-bold">
        {{ $year->year_name }}
    </div>
    <div class="month">
        @if($year->id != 1)
            <a href="/storage/addedproducts/{{ $year->id-1 }}/0" class="month__item">{{ $year->year_name - 1 }}</a>
        @endif
        @foreach($months as $month)
            <a href="/storage/addedproducts/{{ $year->id }}/{{ $month->id }}" class="month__item {{ ( $month->id == $id) ? 'active first-text' : 'second-text' }} fw-bold">{{ $month->month_name }}</a>
        @endforeach
        <a href="/storage/addedproducts/{{ $year->id+1 }}/0" class="month__item">{{ $year->year_name + 1 }}</a>
    </div>
</div>
<div class="date">
    
    <!-- <div class="day">
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
    </div> -->
</div> 
<div class="py-4 px-4">
    <div class="row">
        <div class="col-md-4">
            <button class="form-control"  onclick="hideModal(1)" data-bs-toggle="modal" data-bs-target="#addresidual">Qoldiq</button>
        </div>
        <div class="col-md-4">
        </div>
        <div class="col-md-2">
        </div>
        <div class="col-md-2" style="text-align: end;">
            <button class="form-control" onclick="hideModal(2)" data-bs-toggle="modal" data-bs-target="#addModal">+</button>
        </div>
    </div>
    <hr>
       <!-- Kirim guruhlari jadvali -->
       <div class="card">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-list"></i> Kirim guruhlari</h5>
        </div>
        <div class="card-body">
            <table class="table table-light table-hover">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Title</th>
                        <th scope="col">Date</th>
                        <th style="width: 40px;">PDF</th>
                        <th style="width: 60px;">...</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($group as $item)
                    <tr>
                        <td>{{ $item['id'] }}</td>
                        <td><a href="/storage/ingroup/{{ $item->id }}">{{ $item['group_name'] }}</a></td>
                        <td>{{ $item['day_number'].".".$item['month_name'].".".$item['year_name'] }}</td>
                        <td>
                            <a href="/storage/document/{{ $item->id }}" target="_blank">pdf</a>
                        </td>
                        <td>
                        	<i class="edite_  fa fa-edit" aria-hidden="true" 
                                    data-title = "{{ $item['group_name'] }}" 
                                    data-id = "{{ $item['id'] }}"
                                    data-dayid = "{{ $item['dayid'] }}"
                                    data-yearid = "{{ $year->id }}"
                                    data-monthid = "{{ $id }}"
                                    data-bs-toggle="modal" style="cursor: pointer; color:cadetblue" data-bs-target="#editModal"></i>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <br>
 
    <!-- Maxsulotlar qoldiqlari jadvali -->
    <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(to bottom, #3a3a3a 0%, #333333 100%); color: #F3F3F3;">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h5 class="mb-0"><i class="fas fa-boxes"></i> Maxsulotlar qoldiqlari</h5>
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" id="searchProduct" class="form-control form-control-sm" placeholder="🔍 Maxsulot qidirish...">
                        </div>
                        <div class="col-md-4">
                            <select id="filterCategory" class="form-select form-select-sm">
                                <option value="">Barcha kategoriyalar</option>
                                @if(isset($categories))
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->pro_cat_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="btn-group btn-group-sm w-100">
                                <button onclick="exportToExcel()" class="btn btn-success" title="Excel yuklab olish">
                                    <i class="fas fa-file-excel"></i>
                                </button>
                                <button onclick="exportToPDF()" class="btn btn-danger" title="PDF yuklab olish">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="productsTable" class="table table-bordered table-hover table-striped">
                    <thead style="background: linear-gradient(to bottom, #3a3a3a 0%, #333333 100%); color: #F3F3F3;">
                        <tr>
                            <th scope="col" class="text-center">#</th>
                            <th scope="col">Maxsulot nomi</th>
                            <th scope="col" class="text-center">O'lchov birligi</th>
                            <th scope="col" class="text-center" style="background-color: #28a745; color: white;">
                                <i class="fas fa-arrow-down"></i> Kirim
                            </th>
                            <th scope="col" class="text-center" style="background-color: #dc3545; color: white;">
                                <i class="fas fa-arrow-up"></i> Chiqim
                            </th>
                            <th scope="col" class="text-center" style="background-color: #17a2b8; color: white;">
                                <i class="fas fa-warehouse"></i> Qoldiq
                            </th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody">
                        @php $counter = 1; @endphp
                        @forelse($productsData as $key => $item)
                        <tr class="product-row" 
                            data-product-name="{{ strtolower($item['p_name']) }}" 
                            data-category-id="{{ $item['category_id'] ?? '' }}">
                            <td class="text-center">{{ $counter++ }}</td>
                            <td><strong>{{ $item['p_name'] }}</strong></td>
                            <td class="text-center">{{ $item['size_name'] }}</td>
                            <td class="text-center text-success kirim-value">
                                <strong>{{ number_format($item['kirim'], 2, '.', ' ') }}</strong>
                            </td>
                            <td class="text-center text-danger chiqim-value">
                                <strong>{{ number_format($item['chiqim'], 2, '.', ' ') }}</strong>
                            </td>
                            <td class="text-center qoldiq-value" data-qoldiq="{{ $item['qoldiq'] }}">
                                @if($item['qoldiq'] > 0)
                                    <span class="badge bg-success" style="font-size: 14px; padding: 8px 12px;">
                                        <i class="fas fa-plus-circle"></i> {{ number_format($item['qoldiq'], 2, '.', ' ') }}
                                    </span>
                                @elseif($item['qoldiq'] < 0)
                                    <span class="badge bg-danger" style="font-size: 14px; padding: 8px 12px;">
                                        <i class="fas fa-minus-circle"></i> {{ number_format($item['qoldiq'], 2, '.', ' ') }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary" style="font-size: 14px; padding: 8px 12px;">
                                        <i class="fas fa-equals"></i> 0.00
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr id="noDataRow">
                            <td colspan="6" class="text-center text-muted">
                                <i class="fas fa-info-circle"></i> Maxsulotlar topilmadi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($productsData) > 0)
                    <tfoot class="table-secondary">
                        <tr>
                            <td colspan="3" class="text-end"><strong>JAMI:</strong></td>
                            <td class="text-center text-success">
                                <strong>{{ number_format(array_sum(array_column($productsData, 'kirim')), 2, '.', ' ') }}</strong>
                            </td>
                            <td class="text-center text-danger">
                                <strong>{{ number_format(array_sum(array_column($productsData, 'chiqim')), 2, '.', ' ') }}</strong>
                            </td>
                            <td class="text-center">
                                @php 
                                    $jamiQoldiq = array_sum(array_column($productsData, 'qoldiq'));
                                @endphp
                                @if($jamiQoldiq > 0)
                                    <strong class="text-success">
                                        <i class="fas fa-plus-circle"></i> {{ number_format($jamiQoldiq, 2, '.', ' ') }}
                                    </strong>
                                @elseif($jamiQoldiq < 0)
                                    <strong class="text-danger">
                                        <i class="fas fa-minus-circle"></i> {{ number_format($jamiQoldiq, 2, '.', ' ') }}
                                    </strong>
                                @else
                                    <strong class="text-secondary">0.00</strong>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
    <a href="/storage/home" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Orqaga</a>
</div>

@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
<script>
    // Search funksiyasi
    document.getElementById('searchProduct').addEventListener('keyup', function() {
        filterTable();
    });
    
    // Category filter funksiyasi
    document.getElementById('filterCategory').addEventListener('change', function() {
        filterTable();
    });
    
    function filterTable() {
        const searchValue = document.getElementById('searchProduct').value.toLowerCase();
        const categoryValue = document.getElementById('filterCategory').value;
        const rows = document.querySelectorAll('.product-row');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const productName = row.getAttribute('data-product-name');
            const categoryId = row.getAttribute('data-category-id');
            
            let showBySearch = productName.includes(searchValue);
            let showByCategory = categoryValue === '' || categoryId === categoryValue;
            
            if (showBySearch && showByCategory) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Agar hech narsa topilmasa
        const noDataRow = document.getElementById('noDataRow');
        if (noDataRow) {
            noDataRow.style.display = visibleCount === 0 ? '' : 'none';
        }
    }
    
    // Excel export funksiyasi
    function exportToExcel() {
        const table = document.getElementById('productsTable');
        const rows = [];
        
        // Header qo'shish
        rows.push(['#', 'Maxsulot nomi', 'O\'lchov birligi', 'Kirim', 'Chiqim', 'Qoldiq']);
        
        // Ma'lumotlarni olish
        const productRows = document.querySelectorAll('.product-row');
        let counter = 1;
        productRows.forEach(row => {
            if (row.style.display !== 'none') {
                const cols = row.querySelectorAll('td');
                const qoldiq = row.querySelector('.qoldiq-value').getAttribute('data-qoldiq');
                rows.push([
                    counter++,
                    cols[1].textContent.trim(),
                    cols[2].textContent.trim(),
                    cols[3].textContent.trim(),
                    cols[4].textContent.trim(),
                    qoldiq
                ]);
            }
        });
        
        // Workbook yaratish
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(rows);
        
        // Column kengligini o'rnatish
        ws['!cols'] = [
            {wch: 5},
            {wch: 40},
            {wch: 15},
            {wch: 15},
            {wch: 15},
            {wch: 15}
        ];
        
        XLSX.utils.book_append_sheet(wb, ws, 'Maxsulotlar qoldiqlari');
        
        // Faylni yuklab olish
        const fileName = 'maxsulotlar_qoldiqlari_' + new Date().toISOString().slice(0,10) + '.xlsx';
        XLSX.writeFile(wb, fileName);
    }
    
    // PDF export funksiyasi
    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'mm', 'a4');
        
        // Font o'rnatish
        doc.setFont('helvetica');
        
        // Sarlavha
        doc.setFontSize(16);
        doc.text('Maxsulotlar qoldiqlari hisoboti', 14, 15);
        
        // Sana
        doc.setFontSize(10);
        doc.text('Sana: ' + new Date().toLocaleDateString('uz-UZ'), 14, 22);
        
        // Ma'lumotlarni to'plash
        const tableData = [];
        const productRows = document.querySelectorAll('.product-row');
        let counter = 1;
        
        productRows.forEach(row => {
            if (row.style.display !== 'none') {
                const cols = row.querySelectorAll('td');
                const qoldiq = row.querySelector('.qoldiq-value').getAttribute('data-qoldiq');
                tableData.push([
                    counter++,
                    cols[1].textContent.trim(),
                    cols[2].textContent.trim(),
                    cols[3].textContent.trim(),
                    cols[4].textContent.trim(),
                    qoldiq
                ]);
            }
        });
        
        // Jadval yaratish
        doc.autoTable({
            head: [['#', 'Maxsulot nomi', 'O\'lchov birligi', 'Kirim', 'Chiqim', 'Qoldiq']],
            body: tableData,
            startY: 28,
            theme: 'grid',
            headStyles: {
                fillColor: [58, 58, 58],
                textColor: [243, 243, 243],
                fontSize: 10,
                fontStyle: 'bold'
            },
            columnStyles: {
                0: {cellWidth: 10, halign: 'center'},
                1: {cellWidth: 80},
                2: {cellWidth: 30, halign: 'center'},
                3: {cellWidth: 30, halign: 'center'},
                4: {cellWidth: 30, halign: 'center'},
                5: {cellWidth: 30, halign: 'center'}
            },
            didParseCell: function(data) {
                // Qoldiq ustuni uchun rang berish
                if (data.column.index === 5 && data.section === 'body') {
                    const value = parseFloat(data.cell.raw);
                    if (value > 0) {
                        data.cell.styles.textColor = [40, 167, 69]; // Yashil
                        data.cell.styles.fontStyle = 'bold';
                    } else if (value < 0) {
                        data.cell.styles.textColor = [220, 53, 69]; // Qizil
                        data.cell.styles.fontStyle = 'bold';
                    }
                }
            }
        });
        
        // PDF ni yuklab olish
        const fileName = 'maxsulotlar_qoldiqlari_' + new Date().toISOString().slice(0,10) + '.pdf';
        doc.save(fileName);
    }

	$('.edite_').click(function() {
        var id = $(this).attr('data-id');
        document.getElementById("group_id").value = id;
        var title = $(this).attr('data-title');
        document.getElementById("title").value = title;
        var dayid = $(this).attr('data-dayid');
        var options = document.getElementById("daySelect").options;
        for (var i = 0; i < options.length; i++) {
            if (options[i].value == dayid) {
                options[i].selected = true;
                break;
            }
        }
        var yearid = $(this).attr('data-yearid');
        var monthid = $(this).attr('data-monthid');
        
        document.getElementById("gyear_id").value = yearid;
        document.getElementById("gmonth_id").value = monthid;
    });
    $('.detete').click(function() {
            var debtid = $(this).attr('data-debt-id');
            document.getElementById("ddebt_id").value = debtid;
            var shopid = $(this).attr('data-shop-id');
    });
    // function hideModal(t) {
    //     var x2 = document.getElementById("addModal");
    //     var x1 = document.getElementById("addresidual");
    //     if (t == 1) {
    //         x2.remove();
    //     } else {
    //         x1.style.display = "none";
    //     }
    // }
	function isNumber(evt) {
        let charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }
    // All this is based on the concept of not manipulating the HTML. 

    // container for balance, income, expenses
    var r_money = {};
    r_money.current_income = $('#current-income');
    r_money.current_balance = $('#current-balance');
    r_money.current_expenses = $('#current-expenses');

    // get current values for money
    r_money.balance = 0;
    r_money.income = 0;
    r_money.expenses = 0;

    // Define an update method
    r_money.update = function() {
        r_money.current_income.html(r_money.income);
        r_money.current_expenses.html(r_money.expenses);
        r_money.current_balance.html(r_money.balance);
    }

    var money = {};
    money.current_income = $('#current-income');
    money.current_balance = $('#current-balance');
    money.current_expenses = $('#current-expenses');

    // get current values for money
    money.balance = 0;
    money.income = 0;
    money.expenses = 0;

    // Define an update method
    money.update = function() {
        money.current_income.html(money.income);
        money.current_expenses.html(money.expenses);
        money.current_balance.html(money.balance);
    }

 
    // container for product and actions
    var product = {};
    var r_product = {};

    // Get current products and then we will update the money information


    // iterate through items to add up curren prices.
    product.iterate = function() {
        product.items = $('#tablebody tr');
        product.items = $('#tablebody tr');
        money.income = 0;
        money.expenses = 0;
    
        product.items.each(function() {
            var this_row = $(this);
            //add delete reference 
            $(this).find('td span input').click(function() {
                product_delete_row(this_row);
            });
            
            // get Expense
            var product_expense = parse_currency($(this).find('td')[1].innerHTML);

            // get Income
            var product_income  = parse_currency($(this).find('td')[2].innerHTML);
        
            // Math it together to get some numbers for output later.
            money.income += product_income;
            money.expenses += product_expense;
    
        });
    
        // update balance
        money.balance = money.income - money.expenses; 

        // update details
        money.update();
  
    }

    // residual
    r_product.iterate = function() {
        r_product.items = $('#table-body tr');
        r_product.items = $('#table-body tr');
        r_money.income = 0;
        r_money.expenses = 0;
    
        r_product.items.each(function() {
            var this_row = $(this);
            //add delete reference 
            $(this).find('td i').click(function() {
                r_product_delete_row(this_row);
            });
            
            // get Expense
            var product_expense = parse_currency($(this).find('td')[1].innerHTML);

            // get Income
            var product_income  = parse_currency($(this).find('td')[2].innerHTML);
        
            // Math it together to get some numbers for output later.
            r_money.income += product_income;
            r_money.expenses += product_expense;
    
        });
    
        // update balance
        r_money.balance = r_money.income - r_money.expenses; 

        // update details
        r_money.update();
  
    }

    // Call product iterate for price updates.
    product.iterate();
    r_product.iterate();


    // add product
    product.add_product = $('#add-item');
    r_product.add_product = $('#additem');

    product.add_product.click(function(i, el) {
    // if(fields_validate()) {
        add_product();
        product.iterate();
    
    // }
    
    }); 

    r_product.add_product.click(function(i, el) {
    // if(fields_validate()) {
        r_add_product();
        r_product.iterate();
    
    // }
    
    }); 

    // delete product row

    function product_delete_row(row) {
        row.remove();
        product.iterate();
    }

    function r_product_delete_row(row) {
        row.remove();
        r_product.iterate();
    }


    // Validate fields 
    function fields_validate() {
    /*  var fields = $('#add-form input[type="text"]');
    var required = []
    fields.each(function(i, el) {
        if (i == 0 && $(this).val() == "") {alert('Description required'); return false;   }
    });*/
    
    } 

    // Add product funtionalilty that updates
    function add_product() {
        
        var row = $('<tr>')
        // add description
        .append($('<td>').html($('#input-note-bar').find('option:selected').text() + "<input type='hidden' name='productsid[]' value="+$('#input-note-bar').val()+">"))
        // update expense
        .append($('<td>').html(get_expense_input() + "<input type='hidden' name='weights[]' value="+get_expense_input()+">"))
        // add income 
        .append($('<td>').html(get_income_input() + "<input type='hidden' name='costs[]' value="+get_income_input()+">"))
        .append($('<td>').html($('#get_shop_select').find('option:selected').text() + "<input type='hidden' name='shops[]' value="+get_shop_select()+">"))
        .append($('<td>').html(get_summa_input() + "<input type='hidden' name='pays[]' value="+get_summa_input()+">"))
        // .append($('<td>').html(get_date_input()))
        // add delete button
        .append($('<td>').html('<span><input type="button" style="background: red; border: none" value="Delete"></span>'));

        var find = 0;
        $('#tablebody').find("td").each(function() {
            if ( $(this).text() == $('#input-note-bar').find('option:selected').text() ){
                find = 1;
            }
        });
        if(get_expense_input() == ""){
            find = 1;
        }
        if(get_income_input() == ""){
            find = 1;
        }

        if(find == 0){
            row.prependTo('#tablebody');
        }
    }

    function r_add_product() {
        
        var row = $('<tr>')
        // add description
        .append($('<td>').html($('#input-notebar').find('option:selected').text() + "<input type='hidden' name='productsid[]' value="+$('#input-notebar').val()+">"))
        // update expense
        .append($('<td>').html(get_weight_input() + "<input type='hidden' name='weights[]' value="+get_weight_input() +">"))
        // add income 
        .append($('<td>').html(get_cost_input() + "<input type='hidden' name='costs[]' value="+get_cost_input()+">"))
        // .append($('<td>').html(get_date_input()))
        // add delete button
        .append($('<td>').html('<i style="background: red; border: none; cursor: pointer">Delete</i>'));

        var find = 0;
        $('#table-body').find("td").each(function() {
            if ( $(this).text() == $('#input-notebar').find('option:selected').text() ){
                find = 1;
            }
        });
        
        if(find == 0){
            row.prependTo('#table-body');
        }
    }


    // Get inputed value for income
    function get_income_input() {
        if($('#input-income-bar').val() != "") {
            return $('#input-income-bar').val();
        } else {
            return 0;
        }
    }

    function get_cost_input() {
        if($('#input-incomebar').val() != "") {
            return $('#input-incomebar').val();
        } else {
            return 0;
        }
    }

    // Get inputed value for expense
    function get_expense_input() {
        if($('#input-expense-bar').val() != "") {
            return $('#input-expense-bar').val();
        } else {
            return 0;
        }
    }

    function get_weight_input() {
        if($('#input-expensebar').val() != "") {
            return $('#input-expensebar').val();
        } else {
            return 0;
        }
    }
    

    function get_summa_input() {
        if($('#input-summa-bar').val() != "") {
            return $('#input-summa-bar').val();
        } else {
            return 0;
        }
    }
    

    function get_shop_select() {
        if($('#get_shop_select').val() != "") {
            return $('#get_shop_select').val();
        } else {
            return 0;
        }
    }

    $("#input-summa-bar").click(function(){
        $("#input-summa-bar").val(get_expense_input() * get_income_input());
    });
    // Get input value for the date check and see if one is provided.
    function get_date_input(){
        var date_value = $('#input-date-bar').val() != "" ? $('#input-date-bar').val() : get_date();
        return date_value;
    }

    // Parse text string to number value
    function parse_currency(value) {
        return Number(parseFloat(value.replace(/[^0-9\.]+/g,"")));
    }

 
    // Date ouput
    function get_date() {
        var d = new Date();

        var month = d.getMonth()+1;
        var day = d.getDate();

    return output = (month<10 ? '0' : '') + month + '/' +
        (day<10 ? '0' : '') + day + '/' +
        d.getFullYear();
    }
</script>
@endsection