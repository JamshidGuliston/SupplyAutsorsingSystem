@extends('layouts.app')

@section('content')
<!-- EDIT -->
<!-- Modal -->
<div class="modal editesmodal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
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
	<form action="/technolog/go" method="post">
		@csrf
		<div class="box-sub" style="
        display: flex;
        justify-content: space-between;">
		<select name="manuone" required style="width: 32%">
			<option value=""></option>
	        @foreach($menus as $menu)
	        <option value="{{ $menu->id }}">{{ $menu->one_day_menu_name }}</option>
	        @endforeach
	    </select>
	    
	    <select name="two" required style="width: 50%">
	    	<option value=""></option>
            @foreach($menus as $menu)
            <option value="{{ $menu->id }}">{{ $menu->one_day_menu_name }}</option>
            @endforeach
        </select>
	</div>
		<br/>
    	<div class="box-sub" style="
        display: flex;
        justify-content: space-between;">
    	<a href="/technolog/home">Orqaga</a>
        <p>Bog'chalar soni: {{ count($temps) }}</p>
        @if(count($temps) == count($activ))
        <input type="submit"  class="btn btn-success text-white mb-2" value="Yuborish">
        @endif
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
                <th scope="col">Xodimlar</th>
                @foreach($ages as $age)
                <th scope="col">{{ $age->age_name }}</th>
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
                <td><i class=" edites far fa-edit text-info" data-bs-toggle="modal" data-bs-target="#exampleModal" data-kinid="{{$temp['id']}}" style="cursor: pointer; margin-right: 16px;"> </i></td>
            </tr>
            @endforeach

        </tbody>
    </table>
</div>


@endsection

@section('script')
<script>
    document.getElementById('select-all').onclick = function() {
        var checkboxes = document.getElementsByName('vehicle');
        for (var checkbox of checkboxes) {
            checkbox.checked = this.checked;
        }
    }
    $(document).ready(function() {
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

        $('.add-age').click(function() {
            var inp = $('.form-control');
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

        var editSub = $('.editsub');
        editSub.click(function() {
            var inp = $('.form-control');
            var k = inp.attr('data-id');
            var b = $('.kingarediteid').val();
            inp.each(function() {
                var j = $(this).attr('data-id');
                if ($(this).val() == "" || $(this).val() == 0) {
                    alert('Maydonlarni to`ldiring');
                } else {
                    var valuess = $(this).val();
                    $.ajax({
                        method: 'GET',
                        url: '/technolog/editage/' + b + '/' + j + '/' + valuess,
                        success: function(data) {
                            location.reload();
                        }
                    })
                };

            })
        })

    });
</script>
@endsection