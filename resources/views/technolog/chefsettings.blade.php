@extends('layouts.app')

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

    .password-section {
        border-top: 2px dashed #dee2e6;
        padding-top: 20px;
        margin-top: 20px;
    }

    .password-section h5 {
        color: #6c757d;
        margin-bottom: 15px;
    }

    .form-control.is-valid {
        border-color: #28a745;
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }
</style>
@endsection

@section('leftmenu')
@include('technolog.sidemenu'); 
@endsection

@section('content')
<div class="py-5 px-5">
    <h2>Chef sozlamalari: {{ $user->name }}</h2>
    
    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{route('updatechef')}}">
        @csrf
        <input type="hidden" name="userid" value="{{ $user->id }}" >
        
        <div class="form-group row">
            <label for="chefname" class="col-sm-2 col-form-label">Chef nomi: </label>
            <div class="col-sm-10">
                <input type="text" name="chefname" class="form-control" id="chefname" value="{{ $user->name }}" required>
            </div>
        </div>

        <div class="form-group row">
            <label for="chefemail" class="col-sm-2 col-form-label">Email: </label>
            <div class="col-sm-10">
                <input type="email" name="chefemail" class="form-control" id="chefemail" value="{{ $user->email }}" required>
            </div>
        </div>

        <div class="form-group row">
            <label for="phone" class="col-sm-2 col-form-label">Telefon raqam: </label>
            <div class="col-sm-10">
                <input type="tel" name="phone" class="form-control" id="phone" value="{{ $user->phone ?? '' }}" placeholder="+998 XX XXX XX XX">
            </div>
        </div>

        <div class="password-section">
            <h5><i class="fas fa-lock"></i> Parolni o'zgartirish</h5>
            
            <div class="form-group row">
                <label for="newpassword" class="col-sm-2 col-form-label">Yangi parol: </label>
                <div class="col-sm-10">
                    <input type="password" name="newpassword" class="form-control" id="newpassword" placeholder="Yangi parol kiriting (ixtiyoriy)">
                    <small class="form-text text-muted">Bo'sh qoldiring agar parolni o'zgartirmoqchi bo'lmasangiz</small>
                </div>
            </div>

            <div class="form-group row">
                <label for="confirmpassword" class="col-sm-2 col-form-label">Parolni tasdiqlang: </label>
                <div class="col-sm-10">
                    <input type="password" name="confirmpassword" class="form-control" id="confirmpassword" placeholder="Yangi parolni qayta kiriting">
                </div>
            </div>
        </div>

        <div class="form-group row">
            <label for="chefgardens" class="col-sm-2 col-form-label">Bog'cha</label>
            <div class="col-sm-10">
                <select id='chefGardenSelect' name="gardens[]" class="form-select" aria-label="Default select example">
                    @foreach($kindgardens as $garden)
                        <?php $selected = false; ?>
                        @foreach($user->kindgarden as $userGarden)
                            @if($garden->id == $userGarden->id)
                                <?php $selected = true; ?>
                            @endif
                        @endforeach
                        <option value='{{ $garden->id }}' {{ $selected ? 'selected' : '' }}>
                            {{ $garden->kingar_name }}
                        </option>
                    @endforeach
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
    <a href="/technolog/allchefs">Orqaga</a>
</div>
@endsection

@section('script')
<script>
    document.multiselect('#chefGardenSelect')
        .setCheckBoxClick("checkboxAll", function(target, args) {
            console.log("Checkbox 'Select All' was clicked and got value ", args.checked);
        })
        .setCheckBoxClick("1", function(target, args) {
            console.log("Checkbox for item with value '1' was clicked and got value ", args.checked);
        });

    function enableGardenSelect() {
        document.multiselect('#chefGardenSelect').setIsEnabled(true);
    }

    function disableGardenSelect() {
        document.multiselect('#chefGardenSelect').setIsEnabled(false);
    }

    // Parol validatsiyasi
    $('#newpassword, #confirmpassword').on('input', function() {
        var newPassword = $('#newpassword').val();
        var confirmPassword = $('#confirmpassword').val();
        var confirmField = $('#confirmpassword');
        
        if (newPassword.length > 0) {
            $('#confirmpassword').prop('required', true);
            if (confirmPassword.length > 0) {
                if (newPassword === confirmPassword) {
                    confirmField.removeClass('is-invalid').addClass('is-valid');
                } else {
                    confirmField.removeClass('is-valid').addClass('is-invalid');
                }
            } else {
                confirmField.removeClass('is-valid is-invalid');
            }
        } else {
            $('#confirmpassword').prop('required', false);
            confirmField.removeClass('is-valid is-invalid');
        }
    });

    // Form yuborilishidan oldin tekshirish
    $('form').on('submit', function(e) {
        var newPassword = $('#newpassword').val();
        var confirmPassword = $('#confirmpassword').val();
        
        if (newPassword.length > 0 && newPassword !== confirmPassword) {
            e.preventDefault();
            alert('Parollar mos kelmaydi!');
            return false;
        }
    });
</script>
@endsection
