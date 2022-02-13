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
#input-note-bar, #input-expense-bar, #input-income-bar, #input-date-bar{
	position: relative;
	top: -5px;	
}
#input-note-bar{
	width: 500px;
}
#input-expense-bar{
	width: 112px;
	text-align: right;
}
#input-income-bar{
	width: 112px;
	text-align: right;
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
	/* width: 960px; */
	float: left;
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
#note{
	width: 500px;
}
#expense{
	width: 100px;
}
#income{
	width: 100px;
}
#date{
	width: 92px;
}
/* Delete Button */
button{
	cursor: pointer;
	margin: 3px 3px 3px 3px;
	-webkit-border-radius: 15px;
	-moz-border-radius: 15px;
	padding-bottom: 3px;
	border-radius: 15px;
	width: 74px;
	height: 24px;
	font-family: Tahoma, Geneva, sans-serif;
	font-weight: bold;	
	background: #e2e2e2; /* Old browsers */
		background: -moz-linear-gradient(top,  #e2e2e2 0%, #d1d1d1 44%, #b7b7b7 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#e2e2e2), color-stop(44%,#d1d1d1), color-stop(100%,#b7b7b7)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  #e2e2e2 0%,#d1d1d1 44%,#b7b7b7 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  #e2e2e2 0%,#d1d1d1 44%,#b7b7b7 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  #e2e2e2 0%,#d1d1d1 44%,#b7b7b7 100%); /* IE10+ */
		background: linear-gradient(to bottom,  #e2e2e2 0%,#d1d1d1 44%,#b7b7b7 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e2e2e2', endColorstr='#b7b7b7',GradientType=0 ); /* IE6-9 */
	color: #1B1988;
	border: 0.5px solid #888889;
	text-shadow: 0.0em 0.7px #FFFEFA;
}

button:hover{
	background: #d1d1d1; /* Old browsers */
		background: -moz-linear-gradient(top,  #d1d1d1 0%, #bfbfbf 44%, #a8a8a8 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#d1d1d1), color-stop(44%,#bfbfbf), color-stop(100%,#a8a8a8)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  #d1d1d1 0%,#bfbfbf 44%,#a8a8a8 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  #d1d1d1 0%,#bfbfbf 44%,#a8a8a8 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  #d1d1d1 0%,#bfbfbf 44%,#a8a8a8 100%); /* IE10+ */
		background: linear-gradient(to bottom,  #d1d1d1 0%,#bfbfbf 44%,#a8a8a8 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#d1d1d1', endColorstr='#a8a8a8',GradientType=0 ); /* IE6-9 */
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

</style>
@endsection

@section('leftmenu')
<div class="list-group list-group-flush my-3">
    <a href="/technolog/home" class="list-group-item list-group-item-action bg-transparent second-text"><i class="fas fa-tachometer-alt me-2"></i>Bosh sahifa</a>
    <a href="/storage/addproducts" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('storage/addproducts') ? 'active' : null }}"><i class="fas fa-plus"></i> Maxsulot qo'shish</a>
    <a href="/technolog/food" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/food') ? 'active' : null }}"><i class="fas fa-hamburger"></i> Taomlar</a>
    <a href="/technolog/getbotusers" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/getbotusers') ? 'active' : null }}"><i class="fas fa-comment-dots me-2"></i>Chat bot</a>
    <a href="/technolog/shops" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/shops') ? 'active' : null }}"><i class="fas fa-store-alt"></i> Shops</a>
    <!-- <a href="#" class="list-group-item list-group-item-action bg-transparent text-danger fw-bold"><i class="fas fa-power-off me-2"></i>Logout</a> -->
</div>
@endsection

@section('content')
<div class="container-fluid px-4">
    
    <!-- CURRENT BALANCE, INCOME & EXPENSES DISPLAY-->
    <!-- <h1 id="topbar-balance">Balance : $ 
        <span id="current-balance">80</span> 
    </h1>
    <h2 id="topbar-income">Income : $
        <span id="current-income">100</span>
    </h2>
    <h2 id="topbar-expenses">Expenses : $
        <span id="current-expenses">20</span>
    </h2> -->


    <!-- INPUT FORM -->
    <form id="add-form" action="" method="get">
        
        <!-- Description Line -->
        <span class="input-note">Махсулот:</span>
        <span class="input-income">Оғирлиги:</span>
        <span class="input-expense">Келган нархи:</span>
        <br>

        <select id="input-note-bar" required style="height: 30px;">
            @foreach($products as $row)
                <option value="{{$row['id']}}">{{$row['product_name']}}</option>
            @endforeach
        </select>
        <!-- Expense -->
        <input id="input-expense-bar" type="text">
        <!-- Income -->
        <input id="input-income-bar" type="text">
        <!-- Button -->
        <input id="add-item" class="button" type="button" value="Submit">
    </form> 
	<br>
    <!-- TABLE -->
	<form method="POST" action="{{route('storage.addproducts')}}">
    @csrf
    <table id="test1">
        <thead>
            <tr>
                <th id="note">Маҳсулот</th>
                <th id="expense">Оғирлиги</th>
                <th id="income">Нархи</th>
                <th><button type="submit" class="button">Қўшиш</button></th>
            </tr>
        </thead>
			<tbody id="tablebody">
			</tbody>
    </table>
	</form>

</div>
@endsection

@section('script')
<script>
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
    $(this).find('td button').click(function() {
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
    .append($('<td>').html('<button>Delete</button>'));

  row.appendTo('#tablebody');
}



// Get inputed value for income
function get_income_input() {
  if($('#input-income-bar').val() != "") {
    return parse_currency($('#input-income-bar').val());
  } else {
    return 0;
  }
}

// Get inputed value for expense
function get_expense_input() {
  if($('#input-expense-bar').val() != "") {
    return parse_currency($('#input-expense-bar').val());
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