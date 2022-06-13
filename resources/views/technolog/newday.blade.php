@extends('layouts.app')
@section('css')
<style>
    .loader-box {
        width: 100%;
        background-color: #80afc68a;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        display: flex;
        align-items: center;
        display: none;
        justify-content: center;
    }
    b{
        color: #3c7a7c;
    }
    .loader {
        border: 9px solid #f3f3f3;
        border-radius: 50%;
        border-top: 9px solid #3498db;
        width: 60px;
        display: block;
        height: 60px;
        -webkit-animation: spin 2s linear infinite;
        animation: spin 2s linear infinite;
        position: absolute;
        left: 353px;
        top: 153px;
    }
    th, td{
        text-align: center;
        vertical-align: middle;
        border-bottom-color: currentColor;
        border-right: 1px solid #c2b8b8;
    }
    /* Safari */
    @-webkit-keyframes spin {
        0% {
            -webkit-transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
@endsection
@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection

@section('content')
@if($sendmenu == 0)
<!-- EDIT -->
<!-- Modal -->
<div class="modal editesmodal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="/technolog/editage" method="post">
		    @csrf
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="exampleModalLabel">Bolalar sonini o'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn btn-warning">Saqlash</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- EDIT -->
<!-- EDIT -->
<!-- Modal -->
<div class="modal editesmodal fade" id="menuModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="exampleModalLabel">Keyingi kun menyusi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body editesproduct">
                <select id="tommenu" class="form-control" required>
                    <option value="" selected>Bugungi menyu</option>
                    @foreach($menus as $menu)
                        <option data-menu-id="{{ $menu->id }}" value="{{ $menu->id }}">{{ $menu->menu_name }}</option>
                    @endforeach
                </select>
                <br>
                <div class="hiddiv">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="addmenutom btn btn-success" data-bs-dismiss="modal">ok</button>
                <!-- <button type="submit" class="btn addmenutom btn-warning">Saqlash</button> -->
            </div>
        </div>
    </div>
</div>
<!-- EDIT -->

<!-- DELET -->
<!-- Modal -->
<div class="modal fade" id="exampleModals" tabindex="-1" aria-labelledby="exampleModalLabels" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                <button type="button" class="btn btn-danger">Ok</button>
            </div>
        </div>
    </div>
</div>
<!-- DELET -->


<!-- EDD -->
<div class="modal fade" id="exampleModalsadd" tabindex="-1" aria-labelledby="exampleModalLabelsadd" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content loaders">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="kingarden">
                    <label for="basic-url" class="form-label">MTM nomi</label>
                    <select class="form-select" id="select-add" aria-label="Default select example">
                        <option selected>--</option>
                        @foreach($gardens as $gardenall)
                        @if(!isset($gardenall['ok']))
                        <option value="{{$gardenall['id']}}">{{$gardenall['kingar_name']}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="yang-ages">

                </div>

            </div>
            <div class="loader-box">
                <div class="loader"></div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                <button type="button" class="btn add-age btn-info text-white">Qo'shish</button>
            </div>
        </div>
    </div>
</div>

<!-- EDD -->
<div class="py-4 px-4">
	<form action="/technolog/todaynextdaymenu" method="post">
		@csrf
		<div class="box-sub" style="
        display: flex;
        justify-content: space-between;">
            <div class="col-md-6">
                <div class="today">
                </div>
            </div>
            <div class="col-md-6">
                <b>Taxminiy menyu:</b>
                <div class="tomorrowmenu">
                </div><br>
                <div class="tomorrowmenufood">
                </div>
                <br>
                <input type="checkbox" required> Tasdiqlash
                <br><br>
                <input type="submit"  value="Yuborish">
            </div>
        </div>
		<br/>
    	<div class="box-sub" style="
        display: flex;
        justify-content: space-between;">
    	<a href="/technolog/home">Orqaga</a>
        <p>Bog'chalar soni: {{ count($temps) }}</p>
        <!--@if(count($temps) == count($activ))-->
        <!--<input type="submit"  class="yuborish btn btn-success text-white mb-2" value="Yuborish">-->
        <!--@endif-->
    </div>
    </form>
    <table class="table table-light py-4 px-4">
        <thead>
            <tr>
                <th style="width: 14px;">
                    <input type="checkbox" id="select-all">
                </th>
                <th colspan="3">
                    
                </th>
                <th></th>
                <th>
                    
                </th>
	
                <th> <button class="btn btn-info p-0" style="
                    padding: 3px 16px !important;" data-bs-toggle="modal" data-bs-target="#exampleModalsadd"> <i class="fas fa-plus-square text-white "></i></button> </th>
            </tr>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">MTT-nomi</th>
                <th scope="col">Xodimlar 
                    <!-- shu joyida ishchilar faqat 1 - idli menyudan ovqatlanadi -->
                    <input id="hiddenworkerage" type="hidden" name="workerage" value="1">
                    <!-- <select name="workerege" id="workerege" required>
                        <option value="">---</option>
                        @foreach($ages as $age)
                            <option data-menu-id="{{ $age->id }}" value="{{ $menu->id }}">{{ $age->age_name }}</option>
                        @endforeach
                    </select></th> -->
                @foreach($ages as $age)
                <th scope="col"> <span class="age_name{{ $age->id }}">{{ $age->age_name }} </span>
                    <i data-age-id="{{ $age->id }}" data-bs-toggle="modal" data-bs-target="#menuModal" class="addmenu agehide{{ $age->id }} fas fa-file-alt" style="cursor: pointer;"></i>
                </th>
                @endforeach
                <th style="width: 70px;">Edit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($temps as $temp)
            <tr>
                <th scope="row"><input type="checkbox" id="bike" name="vehicle" value="gentra"></th>
                <td>{{ $temp['name'] }}</td>
                <td>{{ $temp['workers'] }}</td>
                @foreach($ages as $age)
                @if(isset($temp[$age->id]))
                <td>{{ $temp[$age->id] }}</td>
                @else
                <td><i class="far fa-window-close" style="color: red;"></i></td>
                @endif
                @endforeach
                <td><i class="edites far fa-edit text-info" data-bs-toggle="modal" data-bs-target="#exampleModal" data-kinid="{{$temp['id']}}" style="cursor: pointer; margin-right: 16px;"> </i></td>
            </tr>
            @endforeach

        </tbody>
    </table>
</div>
@else
<!-- //////////////////////////////////////////////////////////////Taxminiy menular/////////////////////////////////////////////////////////// -->
<!-- Worker count edit -->
<!-- Modal -->
<div class="modal editesmodal fade" id="wcountModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="/technolog/editnextworkers" method="post">
		    @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ishchilar sonini o'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="gardentitle"></h4>
                <div class="wor_countedit">

                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn btn-success">Saqlash</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- EDIT -->
<!-- Cheldren count edit -->
<!-- Modal -->
<div class="modal editesmodal fade" id="chcountModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="/technolog/editnextcheldren" method="post">
		    @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Bolalar sonini o'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5 class="childrentitle"></h5>
                <div class="chil_countedit">

                </div>
                <div class="temp_count">

                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn btn-success">Saqlash</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- EDIT -->
<!-- Menu edit -->
<!-- Modal -->
<div class="modal editesmodal fade" id="editnextmenuModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="/technolog/editnextmenu" method="post">
		    @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Menyuni o'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5 class="menutitle"></h5>
                <div class="menu_select">

                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn btn-success">Saqlash</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- EDIT -->
<div class="py-4 px-4">
    <div class="row">
        <div class="col-md-6">
            <b>Taxminiy menyular</b>
            <a href="/technolog/createnextdaypdf">
                <i class="far fa-file-pdf" style="color: dodgerblue; font-size: 18px;"></i>
            </a>
        </div>
        <div class="col-md-3">
            <b>Bog'chalarga so'rov yuborish</b>
            <a href="/technolog/sendtoallgarden">
                <i class="far fa-paper-plane" style="color: dodgerblue; font-size: 18px;"></i>
            </a>
        </div>
        <div class="col-md-3">
            <b>Taxminiy menyu yuborish</b>
            <a href="/technolog/nextsendmenutoallgarden">
                <i class="far fa-paper-plane" style="color: dodgerblue; font-size: 18px;"></i>
            </a>
        </div>
    </div>
    <hr>
    <table class="table table-light py-4 px-4">
        <thead>
            <tr>
                <th scope="col" rowspan="2">ID</th>
                <th scope="col" rowspan="2">MTT-nomi</th>
                <th scope="col" rowspan="2">So'rash</th>
                <th scope="col" rowspan="2">Xodimlar 
                @foreach($ages as $age)
                <th scope="col" colspan="2"> 
                    <span class="age_name{{ $age->id }}">{{ $age->age_name }} </span>
                </th>
                @endforeach
                <th style="width: 70px;" rowspan="2">Накладной</th>
                <th style="width: 70px;" rowspan="2">Menyu</th>
            </tr>
            <tr style="color: #888888;">
                @foreach($ages as $age)
                <th><i class="fas fa-users"></i></th>
                <th><i class="fas fa-book-open"></i></th>
                @endforeach
            </tr>
        </thead>
        <tbody>
        <?php $t = 1;  ?>   
        @foreach($nextdayitem as $row)
            <tr>
                <td>{{ $t++ }}</td>
                <td>{{ $row['kingar_name'] }}</td>
                <td><a href="/technolog/sendtoonegarden/{{ $row['kingar_name_id'] }}"><i class="far fa-paper-plane" style="color: dodgerblue;"></i></a></td>
                <td>{{ $row['workers_count'] }} <i class="w_countedit far fa-edit" data-menu-id="{{ $row['kingar_name_id'] }}" data-wor-count="{{ $row['workers_count'] }}" data-king-name="{{ $row['kingar_name'] }}" data-bs-toggle="modal" data-bs-target="#wcountModal" style="color: #727213; font-size: 14px; cursor: pointer;"></i></td>
                @foreach($ages as $age)
                @if(isset($row[$age->id]))
                    <td>
                      {{ $row[$age->id][1]."  " }}
                       @if($row[$age->id][2] != null)
                        <i class="far fa-envelope" style="color: #c40c0c"></i> 
                       @endif
                       <i class="ch_countedit far fa-edit" data-nextrow-id="{{ $row[$age->id][0]; }}" data-child-count="{{ $row[$age->id][1]; }}" data-temprow-id="{{ $row[$age->id][2]; }}" data-tempchild-count="{{ $row[$age->id][3]; }}" data-kinga-name="{{ $row['kingar_name'] }}" data-bs-toggle="modal" data-bs-target="#chcountModal" style="color: #727213; font-size: 14px; cursor: pointer;"></i></td>
                    <td><a href="/nextdaymenuPDF/{{ $row['kingar_name_id'] }}/{{ $age->id }}" target="_blank"><i class="far fa-file-pdf" style="color: dodgerblue; font-size: 18px;"></i></a>  <i class="next_menu far fa-edit" data-nextmenu-id="{{ $row[$age->id][4]; }}" data-nextrow-count="{{ $row[$age->id][0]; }}" data-king-name="{{ $row['kingar_name'] }}" data-bs-toggle="modal" data-bs-target="#editnextmenuModal" style="color: #727213; font-size: 14px; cursor: pointer; margin-left: 11px;"></i></td>
                @else
                    <td>{{ ' ' }}</td>
                    <td>{{ ' ' }}</td>
                @endif
                @endforeach
                <td><a href="/nextnakladnoyPDF/{{ $row['kingar_name_id'] }}" target="_blank"><i class="far fa-file-pdf" style="color: dodgerblue; font-size: 18px;"></i></a></td>
                <td><a href="/technolog/nextsendmenutoonegarden/{{ $row['kingar_name_id'] }}"><i class="far fa-paper-plane" style="color: dodgerblue;"></i></a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <?php $tr = 1 ?>
    @foreach($shops as $shop)
        <b>{{ $shop->shop_name }}</b>
        <a href="/technolog/nextdelivershop/{{ $shop->id }}" target="_blank">
            <i class="fas fa-store-alt" style="color: dodgerblue; font-size: 18px;"></i>
        </a>
        <br>
    @endforeach
</div>
@endif
@endsection

@section('script')
@if($sendmenu == 0)
<script>
    document.getElementById('select-all').onclick = function() {
        var checkboxes = document.getElementsByName('vehicle');
        for (var checkbox of checkboxes) {
            checkbox.checked = this.checked;
        }
    }
    $(document).ready(function() {
        var menuinp = $('.menucounts');
        if(menuinp.length == ''){
            var button = document.getElementsByClassName("yuborish");
            for(var i = 0; i < button.length; i++){
                button[i].style.display = "none"; // depending on what you're doing
            }
        }
        $('#select-add').change(function() {
            g = $(this).val();
            h = $('.yang-ages');
            $.ajax({
                method: "GET",
                url: '/technolog/ageranges/' + g,
                beforeSend: function() {
                    $('.loader-box').show();
                },
                success: function(data) {
                    h.html(data);
                    $('.loader-box').hide();
                }
            })
        });

        $('.addmenu').click(function() {
            var k = $(this).attr('data-age-id');
            var div = $('.hiddiv');
            div.html("<input type='hidden' name='ageid' class='ageid' value="+k+">");
        });

        $('.addmenutom').click(function() {
            var menuid = $("#tommenu").val();
            document.getElementById('tommenu').getElementsByTagName('option')[0].selected = 'selected';
            var wage = $('#hiddenworkerage').val();
            var divtom = $('.tomorrowmenufood');
            var agename = $('.age_name'+wage).text();
            var checkedValue = null; 
            var inputElements = document.getElementsByClassName('checkfood');
            if(inputElements.length>0){
                divtom.append("<b>Xodimlar ovqati "+agename+" guruh menusidan: </b>");
            }
            for(var i=0; inputElements[i]; ++i){
                if(inputElements[i].checked){
                    var fodname = $('#worfood'+inputElements[i].value).text();
                    divtom.append("<input type='hidden' class='foodcounts' name='dmf[]' value="+wage+"_"+menuid+"_"+inputElements[i].value+"> "+fodname+", ");
                }
            }
            var menuinp = $('.menucounts');
            var foodinp = $('.foodcounts');
            if(menuinp.length < 3 || foodinp.length == ''){
                var button = document.getElementsByClassName("yuborish");
                for(var i = 0; i < button.length; i++){
                    button[i].style.display = "none"; // depending on what you're doing
                }
            }
            else{
                var button = document.getElementsByClassName("yuborish");
                for(var i = 0; i < button.length; i++){
                    button[i].style.display = "block"; // depending on what you're doing
                }
            }
        });

        $('.add-age').click(function() {
            var inp = $('.ageranges');
            var k = inp.attr('data-id');
            inp.each(function() {
                var j = $(this).attr('data-id');
                console.log(j);
                var valuess = $(this).val();
                console.log(valuess);
                console.log(g)
                $.ajax({
                    method: 'GET',
                    url: '/technolog/addage/' + g + '/' + j + '/' + valuess,
                    success: function(data) {
                        location.reload();
                    }
                })
            })
        })

        var edite = $('.edites');
        edite.click(function() {
            var ll = $(this).attr('data-kinid');
            $.ajax({
                method: 'GET',
                url: '/technolog/getage/' + ll,
                success: function(data) {
                    var modaledite = $('.editesmodal .modal-body');
                    modaledite.html(data);
                },
            })
        })

    });

    $('#today').change(function() {
        var menuid = $("#today option:selected").val();
        var div = $('.today');
        $.ajax({
            method: "GET",
            url: '/technolog/getfoodnametoday',
            data: {
                'menuid': menuid,
            },
            success: function(data) {
                div.html(data);
            }
        })
    });
    $('#tommenu').change(function() {
        var menuid = $("#tommenu option:selected").val();
        var menutext = $("#tommenu option:selected").text();
        var age = $('.ageid').val();
        var wage = $('#hiddenworkerage').val();
        var div = $('.hiddiv');
        var agename = $('.age_name'+age).text();
        var divtom = $('.tomorrowmenu');
        var icon = $(".agehide"+age).hide();
        $.ajax({
            method: "GET",
            url: '/technolog/getfoodnametoday',
            data: {
                'menuid': menuid,
            },
            success: function(data) {
                divtom.append("<input type='hidden' class='menucounts' name='mid[]' value="+age+"_"+menuid+"><b>"+agename+":</b> "+menutext+";    ");
                if(age == wage){
                    div.append(data);
                }
            }
        })
    });
</script>
@else
<script>
    $('.w_countedit').click(function() {
        var king = $(this).attr('data-menu-id');
        var wc = $(this).attr('data-wor-count');
        var kn = $(this).attr('data-king-name');
        var div = $('.wor_countedit');
        var title = $('.gardentitle');
        div.html("<input type='number' name='workers' class='form-control' value="+wc+">");
        title.html("<p>"+kn+"</p><input type='hidden' name='kingid' class='' value="+king+">");
    });

    $('.ch_countedit').click(function() {
        var nextrow = $(this).attr('data-nextrow-id');
        var chc = $(this).attr('data-child-count');
        var kn = $(this).attr('data-kinga-name');
        var temprow = $(this).attr('data-temprow-id');
        var tempchild = $(this).attr('data-tempchild-count');
        var div1 = $('.chil_countedit');
        var div2 = $('.temp_count');
        var title = $('.childrentitle');
        title.html("<p>"+kn+"</p><input type='hidden' name='nextrow' class='' value="+nextrow+"><input type='hidden' name='temprow' class='' value="+temprow+">");
        div1.html("<input type='number' name='agecount' class='form-control' value="+chc+">");
        div2.html("<br><p style='color: red'>Xabarnoma: <i class='far fa-envelope' style='color: #c40c0c'></i> "+tempchild+"</p>");
    });

    $('.next_menu').click(function() {
        var nextmenu = $(this).attr('data-nextmenu-id');
        var nextrow = $(this).attr('data-nextrow-count');
        var king = $(this).attr('data-king-name');
        var div = $('.menutitle');
        var select = $('.menu_select');
        div.html("<p>"+king+"</p><input type='hidden' name='nextrow' class='' value="+nextrow+">");
        $.ajax({
            method: "GET",
            url: '/technolog/fornextmenuselect',
            data: {
                'menuid': nextmenu,
            },
            success: function(data) {
                select.html(data);
            }
        })
    });
</script>
@endif
@endsection