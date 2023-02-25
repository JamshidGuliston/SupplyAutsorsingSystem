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
<div class="modal editesmodal" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form action="/storage/addtakingsmallbase" method="post">
		    @csrf
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="exampleModalLabel">Qo'shish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" name="title" class="form-control" required>
                <br>
                <select class="form-select" name="day_id" required>
                    <option value="">--Sana--</option>
                    @foreach($days as $row)
                        <option value="{{$row['id']}}">{{$row['day_number'].".".$row['month_name'].".".$row['year_name']}}</option>
                    @endforeach
                </select>
                <br>
                <select class="form-select" name="user_id" required>
                    <option value="">--Xodim--</option>
                    @foreach($users as $row)
                        <option value="{{$row['id']}}">{{ $row['kindgarden'][0]['kingar_name'].', '.$row['name']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button> -->
                <button type="submit" class="btn btn-success">Saqlash</button>
            </div>
        </form>
        </div>
    </div>
</div>
<div class="py-4 px-4">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">Яратиш</button>
    <hr>
    <table class="table table-light py-4 px-4">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Title</th>
                <th scope="col">Shaxs</th>
                <th scope="col">Sana</th>
                <th style="width: 40px;">Svod</th>
            </tr>
        </thead>
        <tbody>
            @php
                $bool = []
            @endphp
            @foreach($res as $row)
                <tr>
                    <td>{{ $row->gid }}</td>
                    <td><a href="/storage/intakinglargebase/{{ $row->gid }}">{{ $row->title }}</a></td>
                    <td>{{ $row->name }}</td>
                    <td>{{ $row->day_id }}</td>
                    <td><a href="#">PDF</a></td>
                </tr>
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
                
                div.append("<tr><td>"+tr+"-кун</td><td><input type='hidden' name='onemenu["+tr+"][1]' value="+onemenuid+"><input type='hidden' name='onemenu["+tr+"][2]' value="+onemenuid+">"+onemenutext+"</td><td>"+(bb ? "+":"-")+"</td><td><input type='hidden' name='onemenu["+tr+"][3]' value="+twomenuid+">"+twomenutext+"</td></tr>");
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