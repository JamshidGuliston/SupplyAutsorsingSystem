@extends('layouts.app')
@section('css')
<style>
    .table{
        width: 100% !important;
    }
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
        font-size: 0.8rem;
        margin: .3rem .3rem;
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
@include('accountant.sidemenu'); 
@endsection
@section('content')
<!-- ADD -->
<div class="modal fade" id="Modalsadd" tabindex="-1" aria-labelledby="exampleModalLabelsadd" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="exampleModalLabel">Maxsulot narxlarini yangilash</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('accountant.pluscosts')}}" method="POST">
                @csrf
                <input type="hidden" name="regionid" value="{{ $id }}">
                <div class="modal-body">
                    <div class="col-sm-12">
                        <select class="form-select" name="dayid" aria-label="Default select example" required>
                            <option value="">Sana tanlang</option>
                            @foreach($days as $row)
                                <option value="{{$row['id']}}">{{ $row['day_number'].'.'.$row['month_name'].' '.$row['year_name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <hr> 
                    <table class="table table-light table-striped table-hover" style="width: calc(100% - 2rem)!important;">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Maxsulot</th>
                                <th scope="col">Narxi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 0; ?>
                            @foreach($productall as $all)
                                <tr>
                                    <th scope="row">{{ ++$i }}</th>
                                    <td>{{ $all->product_name }}</td>
                                    <td style="width: 50px;"><input type="text" name="orders[{{ $all->id }}]"  placeholder="{{ '1 '.$all->size_name.' '.$all->product_name.' narxi' }}" value="0"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn add-age btn-primary text-white">Tasdiqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- End -->
<!--edit-->
<div class="modal fade" id="allpModal" tabindex="-1" aria-labelledby="exampleModalLabelsadd" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="exampleModalLabel">Maxsulot narxlarini o'zgartirish</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('accountant.editallcosts')}}" method="POST">
                @csrf
                <input type="hidden" name="regionid" value="{{ $id }}">
                <div class="allp_edit">
                </div>
                <div class="modal-body">
                    <!--<div class="row">-->
                    <!--    <div class="col-sm-6">-->
                    <!--        <label>Ustama</label>-->
                    <!--        <input class="form-control" name="raise" placeholder="Ustama %" required>-->
                    <!--    </div>-->
                    <!--    <div class="col-sm-6">-->
                    <!--        <label>QQS</label>-->
                    <!--        <input class="form-control" name="nds" placeholder="QQS %" required>-->
                    <!--    </div>-->
                    <!--</div> -->
                    <hr> 
                    <table class="table table-light table-striped table-hover" style="width: calc(100% - 2rem)!important;">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Maxsulot</th>
                                <th scope="col">Narxi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 0; ?>
                            @foreach($productall as $all)
                                <tr>
                                    <th scope="row">{{ ++$i }}</th>
                                    <td>{{ $all->product_name }}</td>
                                    <td style="width: 50px;"><input type="text" name="orders[{{ $all->id }}]"  placeholder="{{ '1 '.$all->size_name.' '.$all->product_name.' narxi' }}" value="0"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn add-age btn-primary text-white">Tasdiqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--end edit-->
<!-- Modal -->
<div class="modal editesmodal fade" id="pcountModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="{{route('accountant.editcost')}}" method="post">
		    @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Maxsulot narxini o'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 class="gardentitle"></h4>
                <div class="wor_countedit">

                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn btn-success">O'zgartirish</button>
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
        <form action="" method="post">
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
        <form action="" method="post">
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
<!-- Add Protsents Modal -->
<div class="modal fade" id="addProtsentsModal" tabindex="-1" aria-labelledby="addProtsentsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('accountant.addprotsent')}}" method="post">
                @csrf
                <input type="hidden" name="region_id" value="{{ $id }}">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white" id="addProtsentsModalLabel">Yangi protsent qo'shish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="age_range_id" class="form-label">Yosh guruhi</label>
                        <select class="form-select" id="age_range_id" name="age_range_id" required>
                            <option value="">Yosh guruhini tanlang</option>
                            @foreach($age_ranges as $age_range)
                                <option value="{{ $age_range->id }}">{{ $age_range->age_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="eater_cost" class="form-label">Ovqatlanish narxi</label>
                        <input type="number" step="0.01" class="form-control" id="eater_cost" name="eater_cost" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Boshlanish sanasi</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">Tugash sanasi</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label for="nds" class="form-label">QQS (%)</label>
                            <input type="number" step="0.01" class="form-control" id="nds" name="nds" required>
                        </div>
                        <div class="col-md-4">
                            <label for="raise" class="form-label">Ustama (%)</label>
                            <input type="number" step="0.01" class="form-control" id="raise" name="raise" required>
                        </div>
                        <div class="col-md-4">
                            <label for="protsent" class="form-label">Protsent (%)</label>
                            <input type="number" step="0.01" class="form-control" id="protsent" name="protsent" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-success">Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Protsents Modal -->
<div class="modal fade" id="editProtsentsModal" tabindex="-1" aria-labelledby="editProtsentsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('accountant.editprotsent')}}" method="post">
                @csrf
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="editProtsentsModalLabel">Protsent tahrirlash</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="edit-protsents-content">
                        <!-- Content will be loaded via AJAX -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-warning">Yangilash</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Protsents Modal -->
<div class="modal fade" id="deleteProtsentsModal" tabindex="-1" aria-labelledby="deleteProtsentsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{route('accountant.deleteprotsent')}}" method="post">
                @csrf
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white" id="deleteProtsentsModalLabel">Protsent o'chirish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Ushbu protsent ma'lumotini o'chirishni tasdiqlaysizmi?</p>
                    <div class="delete-protsents-content">
                        <!-- Content will be loaded via AJAX -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-danger">O'chirish</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="py-4 px-4">
    <div class="row">
        <div class="col-md-6">
            <b> {{ $region->region_name }}</b>
        </div>
        <div class="col-md-3">
        </div>
    </div>
    <hr>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="costs-tab" data-bs-toggle="tab" data-bs-target="#costs" type="button" role="tab" aria-controls="costs" aria-selected="true">
                <i class="fas fa-shopping-cart"></i> Maxsulot narxlari
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="protsents-tab" data-bs-toggle="tab" data-bs-target="#protsents" type="button" role="tab" aria-controls="protsents" aria-selected="false">
                <i class="fas fa-percentage"></i> Protsentlar
            </button>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content mt-3" id="myTabContent">
        <!-- Costs Tab -->
        <div class="tab-pane fade show active" id="costs" role="tabpanel" aria-labelledby="costs-tab">
            <div class="row mb-3">
                <div class="col-md-12 text-end">
                    <b>Yangi narx:</b>
                    <i class="fas fa-plus-circle" style="color: #3c7a7c; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#Modalsadd"></i>
                </div>
            </div>
            <table class="table table-light py-4 px-4">
                <thead>
                    <th style="width: 30px;">Махсулотлар</th>
                    @foreach($days as $day)
                        @if(isset($day->yes))
                            <th scope="col">{{ $day->day_number.'.'.$day->month_name.'.'.$day->year_name }} 
                            <i class="allpedites far fa-edit text-info" data-bs-toggle="modal" data-bs-target="#allpModal"  monthdata-dayid="{{ $day['id']  }}" style="cursor: pointer; margin-right: 16px;"> </i>
                            </th>
                        @endif
                    @endforeach
                </thead>
                <tbody>
                    @foreach($minusproducts as $key => $row)
                    <tr>
                        <td>{{ $row['productname'] }}</td>
                        @foreach($days as $day)
                        @if(isset($day->yes))
                            @if(isset($row[$day['id']]))
                                <td>
                                    {{ $row[$day['id']] }}
                                    <i class="edites far fa-edit text-info" data-bs-toggle="modal" data-bs-target="#pcountModal" data-regionid="{{ $id }}" data-dayid="{{ $day['id'] }}" data-prodid="{{ $key }}" data-weight="{{ $row[$day['id']] }}" style="cursor: pointer; margin-right: 16px;"> </i>
                                </td>
                            @else
                                <td>
                                    {{ '' }}
                                </td>
                            @endif
                        @endif
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Protsents Tab -->
        <div class="tab-pane fade" id="protsents" role="tabpanel" aria-labelledby="protsents-tab">
            <div class="row mb-3">
                <div class="col-md-12 text-end">
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addProtsentsModal">
                        <i class="fas fa-plus"></i> Yangi protsent qo'shish
                    </button>
                </div>
            </div>
            <table class="table table-light table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Yosh guruhi</th>
                        <th>Ovqatlanish narxi</th>
                        <th>Boshlanish sanasi</th>
                        <th>Tugash sanasi</th>
                        <th>QQS (%)</th>
                        <th>Ustama (%)</th>
                        <th>Protsent (%)</th>
                        <th>Holat</th>
                        <th>Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($protsents))
                        @foreach($protsents as $protsent)
                        <tr>
                            <td>{{ $protsent->id }}</td>
                            <td>{{ $protsent->ageRange ? $protsent->ageRange->age_name : '-' }}</td>
                            <td>{{ number_format($protsent->eater_cost, 0, '.', ' ') ?? '-' }} so'm</td>
                            <td>{{ $protsent->start_date ? $protsent->start_date->format('d.m.Y') : '-'}}</td>
                            <td>{{ $protsent->end_date ? $protsent->end_date->format('d.m.Y') : '-'}}</td>
                            <td>{{ $protsent->nds }}%</td>
                            <td>{{ $protsent->raise }}%</td>
                            <td>{{ $protsent->protsent }}%</td>
                            <td>
                                @if($protsent->isActive())
                                    <span class="badge bg-success">Aktiv</span>
                                @else
                                    <span class="badge bg-secondary">Noaktiv</span>
                                @endif
                            </td>
                            <td>
                                <i class="fas fa-edit text-warning" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#editProtsentsModal" data-protsent-id="{{ $protsent->id }}"></i>
                                <i class="fas fa-trash text-danger ms-2" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#deleteProtsentsModal" data-protsent-id="{{ $protsent->id }}"></i>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10" class="text-center">Ma'lumotlar topilmadi</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="form-group row">
        <label for="inputPassword" class="col-sm-2 col-form-label"><a href="/accountant/costs">Orqaga</a></label>
        <div class="col-sm-6">
        <!-- <button type="submit" class="btn btn-success">Saqlash</button> -->
        </div>
    </div>
    
</div>
@endsection

@section('script')
<script>
    $('.edites').click(function() {
        var regid = $(this).attr('data-regionid');
        var dayid = $(this).attr('data-dayid');
        var prodid = $(this).attr('data-prodid');
        var kg = $(this).attr('data-weight');
        var div = $('.wor_countedit');
        div.html("<input type='hidden' name='prodid' class='form-control' value="+prodid+"><input type='hidden' name='regid' class='form-control' value="+regid+"><input type='hidden' name='dayid' class='form-control' value="+dayid+"><input type='text' name='kg' class='form-control' value="+kg+">");
        // title.html("<p>"+kn+"</p><input type='hidden' name='kingid' class='' value="+king+">");
    });

    $('.allpedites').click(function() {
        var dayid = $(this).attr('monthdata-dayid');
        var div = $('.allp_edit');
        div.html("<input type='hidden' name='dayid' class='form-control' value="+dayid+">");
        $.ajax({
            method: "GET",
            url: '/accountant/getingcosts',
            data: {
                'dayid': dayid,
            },
            success: function(data) {
                
            }
        })
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

<script>
    // Protsents modal functionality
    $(document).ready(function() {
        // Edit protsents modal
        $('#editProtsentsModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var protsent_id = button.data('protsent-id');
            
            $.ajax({
                url: '/accountant/getprotsent/' + protsent_id,
                method: 'GET',
                success: function(data) {
                    $('.edit-protsents-content').html(data);
                }
            });
        });

        // Delete protsents modal
        $('#deleteProtsentsModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var protsent_id = button.data('protsent-id');
            
            $('.delete-protsents-content').html('<input type="hidden" name="protsent_id" value="' + protsent_id + '">');
        });

        // Tab activation
        var hash = window.location.hash;
        if (hash) {
            $('.nav-tabs a[href="' + hash + '"]').tab('show');
        }
        
        // Add hash to URL when tab is clicked
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            window.location.hash = e.target.getAttribute('href');
        });
    });
</script>
@endsection