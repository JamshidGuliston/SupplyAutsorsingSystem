@extends('layouts.app')
@section('leftmenu')
    @include('storage.sidemenu'); 
@endsection
@section('content')
<!-- DELET -->
<!-- Modal -->
<div class="modal fade" id="exampleModalss" tabindex="-1" aria-labelledby="exampleModalLabelss" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="exampleModalLabel">O'chirish</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bu mahsulotni o'chirasizmi
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                <button type="button" class="btn dele btn-danger">O'chirish</button>
            </div>
        </div>
    </div>
</div>
<!-- DELET -->
<!-- Modal -->
<div class="modal editesmodal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body editesproduct">
                ...
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="button" class="btn editsub btn-warning">Saqlash</button>
            </div>
        </div>
    </div>
</div>
<!-- EDIT -->


<!-- aDD -->
<div class="modal fade" id="Modalsadd" tabindex="-1" aria-labelledby="exampleModalLabelsadd" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="exampleModalLabel">Maxsulot buyurtmasi</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('storage.plusproduct')}}" method="POST">
                @csrf
                <input type="hidden" name="titleid" value="{{$orderid}}">
                <div class="modal-body">
                    <table class="table table-light table-striped table-hover">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Maxsulot</th>
                                <th scope="col">O'lchami</th>
                                <th scope="col">gr</th>
                                <th scope="col">Og'irligi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 0; ?>
                            @foreach($productall as $all)
                            <tr>
                                <th scope="row">{{ ++$i }}</th>
                                <td>{{ $all->product_name }}</td>
                                <td>{{ $all->size_name }}</td>
                                <td>{{ $all->div }}</td>
                                <td><input type="text" onkeypress="javascript:return isNumber(event)" name="orders[{{ $all->id }}]"></td>
                            </tr>
                            @endforeach
                        
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Yopish</button>
                    <button type="submit" class="btn add-age btn-info text-white">Qo'shish</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- End -->

<!-- Data of Weight Modal -->
<div class="modal fade" id="dataOfWeightModal" tabindex="-1" aria-labelledby="dataOfWeightModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="dataOfWeightModalLabel">Maxsulot ma'lumotlari</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="dataOfWeightContent">
                <!-- Ma'lumotlar bu yerda ko'rsatiladi -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Yopish</button>
            </div>
        </div>
    </div>
</div>

<div class="box-products py-4 px-4">
    <div class="row mb-3">
        <div class="col-md-8">
            <div class="text-title">
                <h3 class="documentnumber">{{ $orderid." - ҳужжат, " }} {{ $ordername->kingar_name.", " }} {{ $ordername->order_title }}</h3>
            </div>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#Modalsadd">
                <i class="fas fa-plus"></i> Maxsulot qo'shish
            </button>
        </div>
    </div>    
</div>

@if(session('status'))
    <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
        {{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row py-1 px-4">
    <div class="col-md-12">
        <div class="table">
            <table class="table table-light table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Maxsulot</th>
                        <th scope="col">O'lchami</th>
                        <th scope="col">gr</th>
                        <th scope="col">Og'irligi</th>
                        <th scope="col">Haqiqiy og'irligi</th>
                        <th scope="col" style="text-align: end;">Tahrirlash</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; ?>
                    @foreach($items as $item)
                    <tr>
                        <th scope="row">{{ ++$i }}</th>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->size_name }}</td>
                        <td>{{ $item->div }}</td>
                        <td>{{ $item->product_weight }}</td>
                        <td>{{ $item->actual_weight }}</td>
                        <td style="text-align: end;">
                            <i data-edites-id="{{ $item->id }}" class="editess far fa-edit text-info" data-bs-toggle="modal" data-bs-target="#exampleModal" data-kinid="" style="cursor: pointer; margin-right: 16px;"> </i>
                            <i class="detete  fa fa-trash" aria-hidden="true" data-delet-id="{{$item->id}}" data-bs-toggle="modal" style="cursor: pointer;" data-bs-target="#exampleModalss"></i>
                            
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <a href="/storage/addmultisklad">Orqaga</a>
    </div>
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
    $(document).ready(function() {
        $('#check_char').keypress(function(event){
            var str = $('#check_char').val(); 

            if(String.fromCharCode(event.which) == ','){
                event.preventDefault();
                $('#check_char').val(str + '.'); 
            }
        });
        $('.editess').click(function() {
            var g = $(this).attr('data-edites-id');
            $.ajax({
                method: 'GET',
                url: '/storage/getproduct',
                data: {
                    'id': g
                },
                success: function(data) {
                    var $editesproduct = $('.editesproduct');
                    varproducts = $editesproduct.html(data);
                }

            })
        });

        $('.editsub').click(function() {
            var orderinpval = $('.product_order').val();
            var orderinpid = $('.product_order').attr('data-producy');
            $.ajax({
                method: 'GET',
                url: '/storage/editproduct',
                data: {
                    'producid': orderinpid,
                    'orderinpval': orderinpval
                },
                success: function(data) {
                    location.reload();
                }
            })
        });

        $('.detete').click(function() {
            deletes = $(this).attr('data-delet-id');
        });

        $('.dele').click(function() {
            var del = deletes
            $.ajax({
                method: "GET",
                url: '/storage/deleteid',
                data: {
                    'id': del,
                },
                success: function(data) {
                    location.reload();
                }

            })
        })

        // data_of_weight ma'lumotlarini ko'rsatish
        $('[data-bs-target="#dataOfWeightModal"]').click(function() {
            var itemId = $(this).attr('data-item-id');
            var modalContent = $('#dataOfWeightContent');
            
            console.log('Item ID:', itemId); // Debug uchun
            
            modalContent.html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Ma\'lumotlar yuklanmoqda...</div>');
            
            $.ajax({
                method: "GET",
                url: '/storage/getDataOfWeight',
                data: {
                    'id': itemId,
                },
                success: function(response) {
                    console.log('Response:', response); // Debug uchun
                    if (response.html) {
                        modalContent.html(response.html);
                    } else {
                        modalContent.html('<div class="alert alert-warning">Ma\'lumotlar topilmadi</div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error:', xhr.responseText); // Debug uchun
                    modalContent.html('<div class="alert alert-danger">Ma\'lumotlarni yuklashda xatolik yuz berdi: ' + error + '</div>');
                }
            });
        });
        
        // Ma'lumotlarni yopib ochish funksiyasi
        window.toggleSection = function(sectionId) {
            var section = document.getElementById(sectionId);
            var icon = document.getElementById(sectionId + '-icon');
            
            if (section.style.display === 'none') {
                section.style.display = 'block';
                icon.className = 'fas fa-chevron-up float-end';
            } else {
                section.style.display = 'none';
                icon.className = 'fas fa-chevron-down float-end';
            }
        };

    });
</script>
@endsection
