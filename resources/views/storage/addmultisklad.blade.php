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
                                <option value="{{$row['id']}}">{{$row['menu_name']}} ({{$row['season_name']}})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="product-select">
                            <select id="twomenu" class="form-select" aria-label="Default select example">
                                <option value="">Қисқа гурух меню</option>
                                @foreach($menus as $row)
                                <option value="{{$row['id']}}">{{$row['menu_name']}} ({{$row['season_name']}})</option>
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
                                <th scope="col" style="text-align: center;">3-7 ёш</th>
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
                @foreach($product_categories as $row)
                    <label for="maxdays[{{$row->id}}]">{{ $row->pro_cat_name }}</label>
                    <input type="number" name="maxdays[{{$row->id}}]" class="form-control" required>
                @endforeach
                <input type="number" name="maxday" placeholder="2-3 кунлик" class="form-control" required>
                Боғчаларни танлаш
                <select id='testSelect1' name="gardens[]" class="form-select" aria-label="Default select example" multiple required>
                    @foreach($gardens as $row)
                        <option value='{{ $row->id }}'>{{ $row->kingar_name }}</option>
                    @endforeach
                </select>
                <br>
                Kun tanlang
                <select name="day" class="form-select" aria-label="Default select example" required>
                    @foreach($days as $row)
                        <option value="{{$row['id']}}">{{ $row['day_number'].".".$row['month_name'].".".$row['year_name']; }}</option>
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
{{-- Report --}}
<div class="modal fade" id="modalsettings" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Umumiy Xisobot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('storage.allreport')}}" method="GET" target="_blank">
            <div class="row modal-body">
                @csrf
                <div class="col-sm-4">
                    <select name="garden" class="form-select" aria-label="Default select example" required>
                        @foreach($gardens as $row)
                            <option value='{{ $row->id }}'>{{ $row->kingar_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-select" id="enddayid" name="start" aria-label="Default select example" required>
                        <option value="">-Sanadan-</option>
                        @foreach($days as $row)
                            <option value="{{$row['id']}}">{{ $row['day_number'].".".$row['month_name'].".".$row['year_name']; }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-select" id="enddayid" name="end" aria-label="Default select example" required>
                        <option value="">-Sanaga-</option>
                        @foreach($days as $row)
                            <option value="{{$row['id']}}">{{ $row['day_number'].".".$row['month_name'].".".$row['year_name']; }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6">
                    Ko'rish
                    <button type="submit" name="report" class="btn btn-info form-control">KIRIM-CHIQIM <i class="fas fa-download" aria-hidden="true"></i></button>
                </div>
                <div class="col-sm-6">
                    Ko'rish
                    <button type="submit" name="nakladnoy" class="btn btn-info form-control">Nakladnoy PDF <i class="fas fa-download" aria-hidden="true"></i></button>
                </div>
                <br/>
            </div>
            </form>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button> -->
            </div>
        </div>
    </div>
</div>
{{-- Report of increase --}}
<div class="modal fade" id="modalIncreased" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Oshib ketilgan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('storage.increasedreport')}}" method="GET" target="_blank">
            <div class="row modal-body">
                @csrf
                <div class="col-sm-4">
                    <select name="gardenID" class="form-select" aria-label="Default select example" required>
                        @foreach($gardens as $row)
                            <option value='{{ $row->id }}'>{{ $row->kingar_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-select" id="enddayid" name="start" aria-label="Default select example" required>
                        <option value="">-Sanadan-</option>
                        @foreach($days as $row)
                            <option value="{{$row['id']}}">{{ $row['day_number'].".".$row['month_name'].".".$row['year_name']; }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-select" id="enddayid" name="end" aria-label="Default select example" required>
                        <option value="">-Sanaga-</option>
                        @foreach($days as $row)
                            <option value="{{$row['id']}}">{{ $row['day_number'].".".$row['month_name'].".".$row['year_name']; }}</option>
                        @endforeach
                    </select>
                </div><br/>
                <div class="col-sm-6">
                    Yuklab olish
                    <button type="submit" class="btn btn-info form-control">PDF <i class="fas fa-download" aria-hidden="true"></i></button>
                </div>
                <br/>
            </div>
            </form>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button> -->
            </div>
        </div>
    </div>
</div>

{{-- Rasxod Modal --}}
<div class="modal fade" id="modalRasxod" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Yangi Buyurtma Yaratish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('storage.addrasxodgroup')}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="group_name" class="form-label">Buyurtma nomi</label>
                        <input type="text" class="form-control" id="group_name" name="group_name" placeholder="Buyurtma nomini kiriting" required>
                    </div>
                    <div class="mb-3">
                        <label for="kingar_name_id" class="form-label">Muassasalar</label>
                        <select class="form-select" id="testSelect2" name="kingar_name[]" aria-label="Default select example" required multiple>
                            @foreach($gardens as $row)
                                <option value="{{$row->id}}">{{$row->kingar_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="date_id" class="form-label">Sana</label>
                        <select class="form-select" id="date_id" name="date_id" required>
                            <option value="">Sanani tanlang</option>
                            @foreach($days as $row)
                                <option value="{{$row['id']}}">{{ $row['day_number'].".".$row['month_name'].".".$row['year_name']; }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-success">Yaratish</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- EDIT -->
<div class="py-4 px-4">
    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="row">
        <div class="col-md-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">Bozorlik yaratish</button>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalsettings">Ummumiy jo'natilgan Xisobot</button>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalIncreased">Orttirilgan Xisobot</button>
        </div>
        <div class="col-md-3">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalRasxod">+ Yaratish</button>
        </div>
    </div>
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
                <th scope="col">Yaratilgan sana</th>
                <th style="width: 80px;">Svod</th>
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
                        <td><a href="/storage/onedaymulti/{{ $row->order_title }}">{{ $row->order_title }}</a></td>
                        <td>{{ $row->day_id }}</td>
                        <td>{{ $row->created_at ? $row->created_at->format('d.m.Y H:i') : '-' }}</td>
                        <td>
                            <a href="/storage/onedaysvod/{{ $row->day_id }}" class="btn btn-sm btn-warning" target="_blank">
                                <i class="fa fa-file-pdf"></i> PDF
                            </a>
                        </td>

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
    document.multiselect('#testSelect1')
		.setCheckBoxClick("checkboxAll", function(target, args) {
			console.log("Checkbox 'Select All' was clicked and got value ", args.checked);
		})
		.setCheckBoxClick("1", function(target, args) {
			console.log("Checkbox for item with value '1' was clicked and got value ", args.checked);
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