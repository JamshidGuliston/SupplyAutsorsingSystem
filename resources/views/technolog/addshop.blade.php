@extends('layouts.app')

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection


@section('css')
<link href="/css/multiselect.css" rel="stylesheet"/>
<script src="/js/multiselect.min.js"></script>
<style>
    form {
        width: 85%;
        margin-top: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group .btn {
        width: 100%;
        background-color: #2f8d2f;
    }
</style>
@endsection

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection

@section('content')
<div class="py-5 px-5">
    <h2>Янги таъминотчи</h2>
    <form method="POST" action="{{route('createshop')}}">
        @csrf
        <div class="form-group row">
            <label for="staticEmail" class="col-sm-2 col-form-label">Номи: </label>
            <div class="col-sm-10">
                <input type="text" name="name" class="form-control" id="staticEmail" required>
            </div>
        </div>
        <div class="form-group row">
            <label for="bossname" class="col-sm-2 col-form-label">Rahbar ismi: </label>
            <div class="col-sm-10">
                <input type="text" name="bossname" class="form-control" id="bossname" value="{{ $shop->bossname ?? '' }}" placeholder="Ism Familiya">
            </div>
        </div>
        <div class="form-group row">
            <label for="phone" class="col-sm-2 col-form-label">Telefon: </label>
            <div class="col-sm-10">
                <input type="text" name="phone" class="form-control" id="phone" value="{{ $shop->phone ?? '' }}" placeholder="Telefon raqam">
            </div>
        </div>
        <div class="form-group row">
            <label for="staticEmail" class="col-sm-2 col-form-label">Faoliyat turi: </label>
            <div class="col-sm-10">
                <select id='select_type' name="type" class="form-select">
                    <option value="" selected>-----</option>
                    @foreach($types as $row)
                        <option value='{{ $row->id }}'>{{ $row->type_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row shtype">
            <label for="inputPassword" class="col-sm-2 col-form-label">Махсулотлар</label>
            <div class="col-sm-10">
                <select id='testSelect1' name="products[]" class="form-select" aria-label="Default select example" multiple>
                    @foreach($products as $row)
                    <option value='{{ $row->id }}'>{{ $row->product_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group row shtype">
            <label for="inputPassword" class="col-sm-2 col-form-label">Боғчалари</label>
            <div class="col-sm-10">
                <select id='testSelect2' name="gardens[]" class="form-select" aria-label="Default select example" multiple>
                    @foreach($gardens as $row)
                    <option value='{{ $row->id }}'>{{ $row->kingar_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="form-group row">
            <label for="inputPassword" class="col-sm-2 col-form-label">Ish faoliyati</label>
            <div class="col-sm-10">
                <select name="hide" class="form-select" required>
                    <option value="">-- Tanlang --</option>
                    <option value="1">🟢 Faol</option>
                    <option value="0">🔴 Faol emas</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="inputPassword" class="col-sm-2 col-form-label"></label>
            <div class="col-sm-10">
                <button type="submit" class="btn btn-success">Saqlash</button>
            </div>
        </div>
    </form>
    <a href="/technolog/shops">Orqaga</a>
</div>
@endsection


@section('script')
<script>
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

    $("#select_type").change(function(){
        if(this.value == 2){
            $(".shtype").hide();
        }else{
            $(".shtype").show();
        }
    });
</script>

@endsection