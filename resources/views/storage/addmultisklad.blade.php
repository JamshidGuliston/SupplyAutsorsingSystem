@extends('layouts.app')

@section('css')
<link href="/css/multiselect.css" rel="stylesheet"/>
<script src="/js/multiselect.min.js"></script>
@endsection
@section('leftmenu')
    @include('storage.sidemenu'); 
@endsection
@section('content')
<!-- AddModal -->
<div class="modal editesmodal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="{{route('storage.newordersklad')}}" method="POST">
            @csrf
            <input type="hidden" id="titleid" name="titleid" value="">
            <div id="hiddenid">
            </div>
            <div class="modal-header">
                <!-- <h5 class="modal-title" id="exampleModalLabel">Продукт хисоби</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                <div class="row">
                    <div class="col-md-5">
                        <div class="product-select">
                            <select id="onemenu" class="form-select" onchange="changeFunc();" aria-label="Default select example">
                                <option value="">3-7 ёш меню</option>
                                @foreach($menus as $row)
                                <option value="{{$row['id']}}">{{$row['menu_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="product-select">
                            <select id="twomenu" class="form-select" aria-label="Default select example">
                                <option value="">Қисқа гурух меню</option>
                                @foreach($menus as $row)
                                <option value="{{$row['id']}}">{{$row['menu_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="product-select">
                            <i class="fas fa-plus me-2" style="color:#23b242; cursor: pointer; padding-top: 10px"></i>
                        </div>
                    </div>
                    <div class="afternoon col-md-12">
                    </div>
                </div>  
            </div>
            <div class="modal-body">
                 
                <div class="table">
                    <table style="width:100%">
                        <thead style="background-color: floralwhite;">
                            <tr>
                                <th scope="col">...</th>
                                <th scope="col" style="text-align: center;">3-4; 4-7 ёш</th>
                                <th scope="col" style="text-align: center;">Ходимлар</th>
                                <th scope="col" style="text-align: center;">Қисқа гурух</th>
                            </tr>
                        </thead>
                        <tbody class="addfood">    
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-body foodcomposition"> 
                <input type="number" name="maxday" class="form-control" placeholder="Сифати тез бузиладиганлар муддати" required>
                <!-- Боғчаларни танлаш -->
                <select id='testSelect2' name="gardens[]" class="form-select" aria-label="Default select example" multiple required>
                    @foreach($gardens as $row)
                        <option value='{{ $row->id }}'>{{ $row->kingar_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <input type="checkbox" required style="padding-right: 10px;">
                Tасдиқлаш
                <button type="submit" class="btn editsub btn-success">Яратиш</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- EDIT -->
<div class="py-4 px-4">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">Яратиш</button>
    <hr>
    <!-- @if(isset($orders[0]->day_number))
    <h4>Oyning {{ $orders[0]->day_number."-sanasi" }}</h4>
    @endif -->
    <table class="table table-light py-4 px-4">
        <thead>

            <tr>
                <th scope="col">ID</th>
                <th scope="col">Title</th>
                <th scope="col">Date</th>
                <th style="width: 40px;">Svod</th>
            </tr>
        </thead>
        <tbody>
            @php
                $bool = []
            @endphp
            @foreach($orders as $row)
                @if(!isset($bool[$row->day_id]))
                    @php $bool[$row->day_id] = 1 @endphp
                    <tr>
                        <td>{{ $row->id }}</td>
                        <td><a href="/storage/onedaymulti/{{ $row->day_id }}">{{ $row->order_title }}</a></td>
                        <td>{{ $row->day_id }}</td>
                        <td><a href="/storage/onedaysvod/{{ $row->day_id }}">PDF</a></td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    <a href="/storage/home/0/0">Orqaga</a>
</div>

@endsection

@section('script')
<script>
    function changeFunc() {
        var selectBox = document.getElementById("onemenu");
        var menuid = selectBox.options[selectBox.selectedIndex].value;
        var div = $('.afternoon');
        $.ajax({
            method: "GET",
            url: '/storage/getworkerfoods',
            data: {
                'menuid': menuid,
            },
            success: function(data) {
                div.html(data);
            }
        })
    }
    $(document).ready(function() {
        var tr = 0;
        $('.fa-plus').click(function() {
            var onemenuid = $('#onemenu').val();
            var onemenutext = $('#onemenu option:selected').text();
            var div = $('.addfood');
            var twomenuid = $('#twomenu').val();
            var twomenutext = $('#twomenu option:selected').text();
            var chkArray = [];
            if(onemenuid == "" || twomenuid == "")
            {
                alert("Menyu tanlang!");
            }
            else{
                tr++;
                var bb = 0;
                $("input:checkbox[id=vehicle]:checked").each(function(){
                    bb = 1;
                    div.append("<input type='hidden' name='workerfoods["+tr+"]["+$(this).val()+"]' value="+onemenuid+">");
                });
                
                div.append("<tr><td>"+tr+"-кун</td><td><input type='hidden' name='onemenu["+tr+"][4]' value="+onemenuid+"><input type='hidden' name='onemenu["+tr+"][1]' value="+onemenuid+"><input type='hidden' name='onemenu["+tr+"][2]' value="+onemenuid+">"+onemenutext+"</td><td>"+(bb ? "+":"-")+"</td><td><input type='hidden' name='onemenu["+tr+"][3]' value="+twomenuid+">"+twomenutext+"</td></tr>");
            }
            
        });
    });
    document.multiselect('#testSelect2')
		.setCheckBoxClick("checkboxAll", function(target, args) {
			console.log("Checkbox 'Select All' was clicked and got value ", args.checked);
		})
		.setCheckBoxClick("1", function(target, args) {
			console.log("Checkbox for item with value '1' was clicked and got value ", args.checked);
		});
    function enable() {
		document.multiselect('#testSelect1').setIsEnabled(true);
	}

	function disable() {
		document.multiselect('#testSelect1').setIsEnabled(false);
	}
</script>
@endsection