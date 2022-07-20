@extends('layouts.app')

@section('css')
<style>
    /* GLOBAL STYLES
    -------------------------------------------------- */

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
<!-- AddModal -->
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
                        <div class="col-md-3">
                            <span class="input-note">Махсулот:</span>
                            <select id="input-note-bar" class="form-select" required>
                                @foreach($products as $row)
                                    <option value="{{$row['id']}}">{{$row['product_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <span class="input-income">Оғирлиги:</span>
                            <br>
                            <input id="input-expense-bar" class="form-control" type="text" onkeypress="javascript:return isNumber(event)">        
                        </div>
                        <div class="col-md-3">
                            <span class="input-expense">Келган нархи:</span>
                            <br>
                            <input id="input-income-bar" class="form-control" type="number">
                        </div>
                        <div class="col-md-3">
                            <input id="add-item" style="margin-top: 35px;" class="button" type="button" value="+">
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
                                <th id="note">Маҳсулот</th>
                                <th id="expense">Оғирлиги</th>
                                <th id="income">Нархи</th>
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
                                @foreach($days as $row)
                                    <option value="{{$row['id']}}">{{$row['day_number'].".".$row['month_name'].".".$row['year_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-4"></div>
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
    <!-- <div class="year">2020</div> -->
    <div class="month">
        @foreach($months as $month)
            <a href="/storage/addedproducts/{{ $month->id }}" class="month__item {{ (Request::is('storage/addedproducts/'.$month->id) or ($month->month_active == 1 and $id == 0)) ? 'active' : null }}">{{ $month->month_name }}</a>
        @endforeach
    </div>
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
        </div>
        <div class="col-md-4">
        </div>
        <div class="col-md-2">
        </div>
        <div class="col-md-2" style="text-align: end;">
            <button class="form-control" data-bs-toggle="modal" data-bs-target="#addModal">+</button>
        </div>
    </div>
    <hr>
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
                <td><a href="/storage/ingroup/{{ $item->id }}">{{ $item['group_name'] }}</a></td>
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
<script>
	function isNumber(evt) {
        let charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }
    // All this is based on the concept of not manipulating the HTML. 

    // container for balance, income, expenses
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

    // Get current products and then we will update the money information


    // iterate through items to add up curren prices.
    product.iterate = function() {
        product.items = $('#tablebody tr');
        money.income = 0;
        money.expenses = 0;
    
        product.items.each(function() {
            var this_row = $(this);
            //add delete reference 
            $(this).find('td input').click(function() {
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

    // Call product iterate for price updates.
    product.iterate();


    // add product
    product.add_product = $('#add-item');

    product.add_product.click(function(i, el) {
    // if(fields_validate()) {
        add_product();
        product.iterate();
    
    // }
    
    }); 

    // delete product row

    function product_delete_row(row) {
        row.remove();
        product.iterate();
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
        // .append($('<td>').html(get_date_input()))
        // add delete button
        .append($('<td>').html('<input type="button" style="background: red; border: none" value="Delete">'));

        row.appendTo('#tablebody');
    }



    // Get inputed value for income
    function get_income_input() {
        if($('#input-income-bar').val() != "") {
            return $('#input-income-bar').val();
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