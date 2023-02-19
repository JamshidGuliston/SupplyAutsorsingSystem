<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Admin Panel Log in</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet"> 
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <style>
            body {
                background: #eafbfc;
                font-family: 'Roboto', sans-serif;
            }

            .login-box {
                margin-top: 75px;
                height: auto;
                background: white;
                text-align: center;
                box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
            }

            .login-title {
                margin-top: 15px;
                text-align: center;
                font-size: 30px;
                letter-spacing: 2px;
                margin-top: 15px;
                font-weight: bold;
                color: #216cc1;
            }

            .login-form {
                margin-top: 25px;
                text-align: left;
            }

            input[type=email] {
                background-color: white;
                border: none;
                border-bottom: 2px solid #a2a2a2;
                border-top: 0px;
                border-radius: 0px;
                font-weight: bold;
                outline: 0;
                margin-bottom: 20px;
                padding-left: 0px;
                color: black;
            }

            input[type=password] {
                background-color: white;
                border: none;
                border-bottom: 2px solid #a2a2a2;
                border-top: 0px;
                border-radius: 0px;
                font-weight: bold;
                outline: 0;
                padding-left: 0px;
                margin-bottom: 20px;
                color: black;
            }

            .form-group {
                margin-bottom: 40px;
                outline: 0px;
            }

            .form-control:focus {
                border-color: inherit;
                -webkit-box-shadow: none;
                box-shadow: none;
                border-bottom: 2px solid #a2a2a2;
                outline: 0;
                
                color: black;
            }

            input:focus {
                outline: none;
                box-shadow: 0 0 0;
            }

            label {
                margin-bottom: 0px;
            }

            .form-control-label {
                font-size: 16px;
                color: #216cc1;
                font-weight: bold;
                letter-spacing: 1px;
            }

            .btn-outline-primary {
                border-color: #efefef;
                background-color: #efefef;
                color: #216cc1;
                border-radius: 0px;
                font-weight: bold;
                letter-spacing: 1px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
            }

            .btn-outline-primary:hover {
                background-color: #efefef;
                right: 0px;
                color: #000;
            }

            .login-btm {
                padding-top: 30px;
                float: left;
            }

            .login-button {
                width: 100%;
                text-align: center;
                margin-bottom: 25px;
                cursor: pointer;
            }

            .error{
                color: red;
            }

            .login-text {
                text-align: left;
                padding-left: 0px;
                color: black;
            }

            .loginbttm {
                padding: 0px;
            }
        </style>
    </head>
    <body>
<!-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> -->
</body>
</html>
