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
                color: #d32f2f;
                background-color: #ffebee;
                border: 1px solid #ffcdd2;
                border-radius: 4px;
                padding: 8px 12px;
                margin-top: 5px;
                font-size: 13px;
                display: block;
                animation: fadeIn 0.3s ease-in;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .alert {
                background-color: #fff3cd;
                border: 1px solid #ffeaa7;
                color: #856404;
                padding: 12px 16px;
                border-radius: 6px;
                margin-bottom: 15px;
                font-size: 14px;
                animation: slideIn 0.4s ease-out;
            }
            
            .alert-danger {
                background-color: #f8d7da;
                border: 1px solid #f5c6cb;
                color: #721c24;
            }
            
            .alert-success {
                background-color: #d4edda;
                border: 1px solid #c3e6cb;
                color: #155724;
            }
            
            @keyframes slideIn {
                from { opacity: 0; transform: translateX(-20px); }
                to { opacity: 1; transform: translateX(0); }
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
        
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-2"></div>
            <div class="col-lg-6 col-md-8 login-box">
                <div class="col-lg-12 login-title">
                    Tizimga kirish
                </div>

                <div class="col-lg-12 login-form">
                    <div class="col-lg-12 login-form">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="form-group">
                                <label class="form-control-label">Elektron pochta manzili</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="error">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">Parol</label>
                                <input type="password" class="form-control" name="password" required autocomplete="current-password">
                                @error('password')
                                    <span class="error">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-lg-12 loginbttm">
                                <!-- <div class="g-recaptcha" data-sitekey="6LfD7ScjAAAAACSAMyR8hhDpviT55YzQU9TRru9q"></div> -->
                                @if (session('error'))
                                    <div class="alert alert-danger">
                                        <i class="fa fa-exclamation-triangle" style="margin-right: 8px;"></i>
                                        {{ session('error') }}
                                    </div>
                                @endif
                                @if (session('status'))
                                    <div class="alert alert-success">
                                        <i class="fa fa-check-circle" style="margin-right: 8px;"></i>
                                        {{ session('status') }}
                                    </div>
                                @endif
                                @if (isset($messages))
                                    <div class="alert">{!! $messages !!}</div>
                                @endif
                                <br/>
                            </div>
                            
                            <div class="col-lg-12 login-btm">
                                <button type="submit" class="btn btn-outline-primary login-button">Kirish</button>
                            </div>
                            </div>
                            
                        </form>
                    </div>
                </div>
                <div class="col-lg-3 col-md-2"></div>
            </div>
        </div>
    </div>
    </body>
</html>