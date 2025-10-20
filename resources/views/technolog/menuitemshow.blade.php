@extends('layouts.app')

@section('css')
<style>
    /* form {
        width: 85%;
        margin-top: 30px;
    } */
    .row{
        margin-bottom: 10px;
    }
    .form-group {
        margin-bottom: 20px;
    }

    .form-group .btn {
        width: 100%;
        background-color: #2f8d2f;
    }
    table, th{
        padding: 10px;
        border-collapse: collapse;
        background-color: white;
    }
    tr:hover {background-color: aliceblue;}
    
    #productTotals {
        margin-top: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    
    #productTotals .table {
        margin-bottom: 0;
    }
    
    #productTotals th {
        background-color: #f8f9fa;
        font-weight: bold;
    }
</style>
@endsection

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection


@section('content')

<!-- AddModal -->
<div class="modal editesmodal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
        <form action="{{route('technolog.createmenucomposition')}}" method="POST">
            @csrf
            <input type="hidden" id="titleid" name="titleid" value="{{$id}}">
            <div id="hiddenid">
            </div>
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Таом қўшиш</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body addfood">    
            </div>
            <div class="modal-body foodcomposition">    
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn editsub btn-success">qo'shish</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- EDIT -->


<!-- DELET -->
<!-- Modal -->
<div class="modal fade" id="deleteModalas" tabindex="-1" aria-labelledby="exampleModalLabelss" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="{{route('technolog.deletemenufood')}}" method="POST">
            @csrf
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="exampleModalLabel">O'chirish</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body deletefood">
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn dele btn-danger">O'chirish</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- DELET -->

<!-- DELET -->
<!-- Modal -->
<div class="modal fade" id="deleteModalfood" tabindex="-1" aria-labelledby="exampleModalLabelss" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="{{route('technolog.deletemenufood')}}" method="POST">
            @csrf
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="exampleModalLabel">Таомни ўчириш</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="deletefood modal-body">
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn dele btn-danger">O'chirish</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- DELET -->

<!-- EDIT -->
<!-- Modal -->
<div class="modal editesmodal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <form action="{{route('technolog.editemenuproduct')}}" method="POST">
            @csrf
            <input type="hidden" id="titleid" name="titleid" value="{{$id}}">
            <div id="edithidden">
            </div>
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Махсулот (гр) ни ўзгартириш</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body menucomposition">    
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn editsub btn-success">Ўзгартириш</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- EDIT -->


<div class="py-5 px-5">
        <input type="hidden" name="menuid" value="{{ $id }}" />
        <!-- Titlemenu ma'lumotlari va boshqaruv tugmalari -->
        <div class="titlemenu-header">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="mb-1">{{$titlemenu->menu_name}}</h4>
                    <p class="mb-0 text-muted">
                        <strong>Mavsum:</strong> {{$titlemenu->season->season_name ?? 'Noma\'lum'}} | 
                        <strong>Yaratilgan:</strong> {{ $titlemenu->created_at }}
                    </p>
                    @if($titlemenu->description)
                        <p class="mb-0"><strong>Tavsif:</strong> {{$titlemenu->description}}</p>
                    @endif
                </div>
            </div>
        </div>
        <!-- Nusxa yaratish qismi tepada -->
        @if(isset($menuitem[0]['menu_season_id']))
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-copy"></i> Menyu nusxasini yaratish</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{route('technolog.copymenuitem')}}" method="POST" class="d-flex align-items-center gap-3">
                            @csrf
                            <input type="hidden" name="seasonid" value="{{ $menuitem[0]['menu_season_id'] }}" >
                            <input type="hidden" name="menuid" value="{{ $menuitem[0]['menuid'] }}" >
                            <div class="flex-grow-1">
                                <input type="text" name="newmenuname" class="form-control" placeholder="{{ $menuitem[0]['menu_name'] }} nusxasi" required>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-copy"></i> Nusxa yaratish
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="table">
            <table style="width:100%">
                <thead style="background-color: floralwhite;">
                    <tr>
                        <th scope="col" style="text-align: center;">Taomlar</th>
                        <th scope="col">Maxsulot</th>
                        @foreach($titlemenu->age_range as $row)
                        <th scope="col" style="text-align: end;">{{ $row['age_name'] }}</th>
                        <th scope="col" style="text-align: end;">Chiqindisiz (gr)</th>
                        <th scope="col" style="text-align: end;">Oqsillar (gr)</th>
                        <th scope="col" style="text-align: end;">Yog'lar (gr)</th>
                        <th scope="col" style="text-align: end;">Uglevodlar (gr)</th>
                        <th scope="col" style="text-align: end;">Kaloriya</th>
                        @endforeach
                        <th scope="col" style="text-align: end;">Tahrirlash</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; $t = 0; ?>
                    @for($it=0; $it < count($menuitem); $it++)
                    @if(empty($mealtime[$menuitem[$it]['meal_timeid']]))
                        <?php 
                            $mealtime[$menuitem[$it]['meal_timeid']] = 1;
                            $mealtime[$menuitem[$it]['meal_timeid']."-".$menuitem[$it]['foodid']] = 1;
                        ?>
                        <tr>
                            <td colspan="{{ count($titlemenu->age_range)+4; }}" style="color: red; padding-left: 14px;">{{ $menuitem[$it]['meal_time_name'] }}</td>
                        </tr>
                        <?php  
                            $fd = 0;
                            for($tek = $it; $tek<count($menuitem) and !empty($mealtime[$menuitem[$tek]['meal_timeid']]) and $menuitem[$it]['foodid'] == $menuitem[$tek]['foodid']; $tek++)
                            {
                                $fd++;
                            }
                        ?>
                        <tr>
                            <td rowspan="{{ round($fd/count($titlemenu->age_range)) }}" style="text-align: center;">{{ $menuitem[$it]['food_name'] }}</td>
                            <td>{{ $menuitem[$it]['product_name'] }}</td>
                           @foreach($titlemenu->age_range as $row)
                            <td style="text-align: end;">{{ $menuitem[$it]['weight']." гр" }}</td>
                            <td style="text-align: end;">{{ $menuitem[$it]['waste_free'] ?? '-' }}</td>
                            <td style="text-align: end;">{{ $menuitem[$it]['proteins'] ?? '-' }}</td>
                            <td style="text-align: end;">{{ $menuitem[$it]['fats'] ?? '-' }}</td>
                            <td style="text-align: end;">{{ $menuitem[$it]['carbohydrates'] ?? '-' }}</td>
                            <td style="text-align: end;">{{ $menuitem[$it]['kcal'] ?? '-' }}</td>
                            
                            <?php $it++; ?>
                            @endforeach
                            <?php $it--;?>
                            <td style="text-align: end;">
                                <!-- <i data-menu-id="{{ $menuitem[$it]['menuid'] }}" data-time-id="{{ $menuitem[$it]['meal_timeid'] }}" data-food-id="{{ $menuitem[$it]['foodid'] }}" data-prod-id="{{ $menuitem[$it]['productid'] }}" class="editess far fa-edit text-info" data-bs-toggle="modal" data-bs-target="#exampleModal" data-kinid="" style="cursor: pointer; margin-right: 16px;"> </i> -->
                                <!-- <i data-menu-id="{{ $menuitem[$it]['menuid'] }}" data-time-id="{{ $menuitem[$it]['meal_timeid'] }}" data-food-id="{{ $menuitem[$it]['foodid'] }}" data-prod-id="{{ $menuitem[$it]['productid'] }}" class="detete  fa fa-trash" aria-hidden="true" data-bs-toggle="modal" style="cursor: pointer;" data-bs-target="#deleteModalas"></i> -->
                            </td>
                        </tr>
                    @elseif(empty($mealtime[$menuitem[$it]['meal_timeid']."-".$menuitem[$it]['foodid']]))
                        <?php 
                            $mealtime[$menuitem[$it]['meal_timeid']."-".$menuitem[$it]['foodid']] = 1;
                        ?>
                        <?php  
                            $fd = 0;
                            for($tek = $it; $tek<count($menuitem) and !empty($mealtime[$menuitem[$tek]['meal_timeid']]) and $menuitem[$it]['foodid'] == $menuitem[$tek]['foodid']; $tek++)
                            {
                                $fd++;
                            }
                        ?>
                        <tr>
                            <td rowspan="{{ round($fd/count($titlemenu->age_range)) }}" style="text-align: center;">{{ $menuitem[$it]['food_name'] }}</td>
                            <td>{{ $menuitem[$it]['product_name'] }}</td>
                            @foreach($titlemenu->age_range as $row)
                            <td style="text-align: end;">{{ $menuitem[$it]['weight']." гр" }}</td>
                            <td style="text-align: end;">{{ $menuitem[$it]['waste_free'] ?? '-' }}</td>
                            <td style="text-align: end;">{{ $menuitem[$it]['proteins'] ?? '-' }}</td>
                            <td style="text-align: end;">{{ $menuitem[$it]['fats'] ?? '-' }}</td>
                            <td style="text-align: end;">{{ $menuitem[$it]['carbohydrates'] ?? '-' }}</td>
                            <td style="text-align: end;">{{ $menuitem[$it]['kcal'] ?? '-' }}</td>
                            
                            <?php $it++; ?>
                            @endforeach
                            <?php $it--;?>
                            <td style="text-align: end;">
                                <!-- <i data-menu-id="{{ $menuitem[$it]['menuid'] }}" data-time-id="{{ $menuitem[$it]['meal_timeid'] }}" data-food-id="{{ $menuitem[$it]['foodid'] }}" data-prod-id="{{ $menuitem[$it]['productid'] }}" class="editess far fa-edit text-info" data-bs-toggle="modal" data-bs-target="#exampleModal" data-kinid="" style="cursor: pointer; margin-right: 16px;"> </i> -->
                                <!-- <i data-menu-id="{{ $menuitem[$it]['menuid'] }}" data-time-id="{{ $menuitem[$it]['meal_timeid'] }}" data-food-id="{{ $menuitem[$it]['foodid'] }}" data-prod-id="{{ $menuitem[$it]['productid'] }}" class="detete  fa fa-trash" aria-hidden="true" data-bs-toggle="modal" style="cursor: pointer;" data-bs-target="#deleteModalas"></i> -->
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td>{{ $menuitem[$it]['product_name'] }}</td>
                            @foreach($titlemenu->age_range as $row)
                            <td style="text-align: end;">{{ $menuitem[$it]['weight']." гр" }}</td>
                            <td style="text-align: end;">{{ $menuitem[$it]['waste_free'] ?? '-' }}</td>
                            <td style="text-align: end;">{{ $menuitem[$it]['proteins'] ?? '-' }}</td>
                            <td style="text-align: end;">{{ $menuitem[$it]['fats'] ?? '-' }}</td>
                            <td style="text-align: end;">{{ $menuitem[$it]['carbohydrates'] ?? '-' }}</td>
                            <td style="text-align: end;">{{ $menuitem[$it]['kcal'] ?? '-' }}</td>
                            
                            <?php $it++; ?>
                            @endforeach
                            <?php $it--;?>
                            <td style="text-align: end;">
                                <!-- <i data-menu-id="{{ $menuitem[$it]['menuid'] }}" data-time-id="{{ $menuitem[$it]['meal_timeid'] }}" data-food-id="{{ $menuitem[$it]['foodid'] }}" data-prod-id="{{ $menuitem[$it]['productid'] }}" class="editess far fa-edit text-info" data-bs-toggle="modal" data-bs-target="#exampleModal" data-kinid="" style="cursor: pointer; margin-right: 16px;"> </i> -->
                                <!-- <i data-menu-id="{{ $menuitem[$it]['menuid'] }}" data-time-id="{{ $menuitem[$it]['meal_timeid'] }}" data-food-id="{{ $menuitem[$it]['foodid'] }}" data-prod-id="{{ $menuitem[$it]['productid'] }}" class="detete  fa fa-trash" aria-hidden="true" data-bs-toggle="modal" style="cursor: pointer;" data-bs-target="#deleteModalas"></i> -->
                            </td>
                        </tr>
                    @endif
                    @endfor
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <h6>Maxsulotlar bo'yicha jami gramlar:</h6>
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Maxsulot ID</th>
                        <th>Maxsulot nomi</th>
                        <th>Jami gramlar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productTotals as $productId => $product)
                    <tr>
                        <td>{{ $productId }}</td>
                        <td>{{ $product['product_name'] }}</td>
                        <td>{{ number_format($product['total_weight'], 3) }} гр</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="form-group row">
        <label for="inputPassword" class="col-sm-2 col-form-label"><a href="/technolog/seasons">Orqaga</a></label>
        <div class="col-sm-6">
        <!-- <button type="submit" class="btn btn-success">Saqlash</button> -->
        </div>
    </div>
</div>
@endsection


@section('script')
<script>
    $(document).ready(function() {
        $('.btn-dark').click(function() {
            var timeid = $('#mealtime').val();
            var div = $('.addfood');
            var mid = $('#mealtime').val();
            if(timeid == "")
            {
                alert("Ovqatlanish vaqtini tanlang!");
            }
            $.ajax({
                method: "GET",
                url: '/technolog/getfood',
                data: {
                    'id': timeid,
                },
                success: function(data) {
                    div.html(data);
                    div.append("<input type='hidden' name='timeid' value="+mid+">");
                }

            })
        });

        $('.editess').click(function(){
            var menuid = $(this).attr('data-menu-id');
            var timeid = $(this).attr('data-time-id');
            var foodid = $(this).attr('data-food-id');
            var prodid = $(this).attr('data-prod-id');
            var hidden = $('#edithidden');
            var div = $('.menucomposition');
            $.ajax({
            method: "GET",
            url: '/technolog/getmenuproduct',
            data: {
                'menuid': menuid,
                'timeid': timeid,
                'foodid': foodid,
                'prodid': prodid,
            },
            success: function(data) {
                div.html(data);
                hidden.append("<input type='hidden' name='menuid' value="+menuid+">");
                hidden.append("<input type='hidden' name='timeid' value="+timeid+">");
                hidden.append("<input type='hidden' name='foodid' value="+foodid+">");
                hidden.append("<input type='hidden' name='prodid' value="+prodid+">");
            }
        })
        });
        $('.fooddel').click(function(){
            var menuid = $(this).attr('data-menu-id');
            var timeid = $(this).attr('data-time-id');
            var foodid = $(this).attr('data-food-id');
            var foodname = $(this).attr('data-foodname-id');
            var div = $('.deletefood');
            div.append("<input type='hidden' name='menuid' value="+menuid+">");
            div.append("<input type='hidden' name='timeid' value="+timeid+">");
            div.append("<input type='hidden' name='foodid' value="+foodid+">");
            div.append("<p>"+foodname+"ни ўчирмоқчимисиз? </p>");  
        });
    });
    function change(){
        var x = document.getElementById("foodid").value;
        var div = $('.foodcomposition');
        var menuid = $('#titleid').val();
        $.ajax({
            method: "GET",
            url: '/technolog/getfoodcomposition',
            data: {
                'id': x,
                'menuid': menuid,
            },
            success: function(data) {
                div.html(data);
            }
        })
    };
</script>
@endsection