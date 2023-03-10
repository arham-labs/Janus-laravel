<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

    <style>
        body {
            background-image: url('images/emailer-bg.png');
            height: 100vh;
            width: 100%;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .textfield-outlined {
            position: relative;
            margin-bottom: 20px;
            padding-top: 6px;
            font-size: 16px;
            line-height: 1.5;
            letter-spacing: 0.4px;
        }

        .textfield-outlined>input,
        .textfield-outlined>textarea {
            box-sizing: border-box;
            width: 100%;
            padding: 9px 13px 9px;
            font-size: 14px;
            line-height: inherit;
            color: #272e36;
            border-style: solid;
            border-width: 1px;
            /* border-color: transparent #999999 #999999; */
            border: 1px solid #999999;
            border-radius: 4px;
            -webkit-text-fill-color: currentColor;
            background-color: transparent;
            caret-color: #3d6f9e;
            transition: border 0.2s, box-shadow 0.2s;
        }

        .textfield-outlined>input:not(:focus):-moz-placeholder-shown,
        .textfield-outlined>textarea:not(:focus):-moz-placeholder-shown {
            border-top-color: #999999;
        }

        .textfield-outlined>input:not(:focus):-ms-input-placeholder,
        .textfield-outlined>textarea:not(:focus):-ms-input-placeholder {
            border-top-color: #999999;
        }

        .textfield-outlined>input:not(:focus):placeholder-shown,
        .textfield-outlined>textarea:not(:focus):placeholder-shown {
            border-top-color: #999999;
        }

        .textfield-outlined>input+label,
        .textfield-outlined>textarea+label {
            display: flex;
            width: 100%;
            max-height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            font-size: 12px;
            line-height: 15px;
            color: #999999;
            cursor: text;
            transition: color 0.2s, font-size 0.2s, line-height 0.2s;
        }

        .textfield-outlined>input:not(:focus):-moz-placeholder-shown+label,
        .textfield-outlined>textarea:not(:focus):-moz-placeholder-shown+label {
            font-size: 14px;
            line-height: 54px;
        }

        .textfield-outlined>input:not(:focus):-ms-input-placeholder+label,
        .textfield-outlined>textarea:not(:focus):-ms-input-placeholder+label {
            font-size: 14px;
            line-height: 54px;
        }

        .textfield-outlined>input:not(:focus):placeholder-shown+label,
        .textfield-outlined>textarea:not(:focus):placeholder-shown+label {
            font-size: 14px;
            line-height: 54px;
        }

        .textfield-outlined>input+label::before,
        .textfield-outlined>input+label::after,
        .textfield-outlined>textarea+label::before,
        .textfield-outlined>textarea+label::after {
            content: "";
            display: block;
            box-sizing: border-box;
            height: 8px;
            min-width: 10px;
            margin-top: 6px;
            border-top: solid 1px #999999;
            pointer-events: none;
            box-shadow: inset 0 1px transparent;
            transition: border 0.2s, box-shadow 0.2s;
        }

        .textfield-outlined>input+label::before,
        .textfield-outlined>textarea+label::before {
            margin-right: 4px;
            border-left: solid 1px transparent;
            border-radius: 4px 0;
        }

        .textfield-outlined>input+label::after,
        .textfield-outlined>textarea+label::after {
            flex-grow: 1;
            margin-left: 4px;
            border-right: solid 1px transparent;
            border-radius: 0 4px;
        }

        .textfield-outlined>input:not(:focus):-moz-placeholder-shown+label::before,
        .textfield-outlined>input:not(:focus):-moz-placeholder-shown+label::after,
        .textfield-outlined>textarea:not(:focus):-moz-placeholder-shown+label::before,
        .textfield-outlined>textarea:not(:focus):-moz-placeholder-shown+label::after {
            border-top-color: transparent;
        }

        .textfield-outlined>input:not(:focus):-ms-input-placeholder+label::before,
        .textfield-outlined>input:not(:focus):-ms-input-placeholder+label::after,
        .textfield-outlined>textarea:not(:focus):-ms-input-placeholder+label::before,
        .textfield-outlined>textarea:not(:focus):-ms-input-placeholder+label::after {
            border-top-color: transparent;
        }

        .textfield-outlined>input:not(:focus):placeholder-shown+label::before,
        .textfield-outlined>input:not(:focus):placeholder-shown+label::after,
        .textfield-outlined>textarea:not(:focus):placeholder-shown+label::before,
        .textfield-outlined>textarea:not(:focus):placeholder-shown+label::after {
            border-top-color: transparent;
        }

        .textfield-outlined~label:hover>input,
        .textfield-outlined~label:hover>textarea {
            border-color: transparent #272e36 #272e36;
        }

        .textfield-outlined label:hover>input+label::before,
        .textfield-outlined label:hover>input+label::after,
        .textfield-outlined label:hover>textarea+label::before,
        .textfield-outlined label:hover>textarea+label::after {
            border-top-color: #272e36;
        }

        .textfield-outlined label:hover>input:not(:focus):-moz-placeholder-shown,
        .textfield-outlined label:hover>textarea:not(:focus):-moz-placeholder-shown {
            border-color: #272e36;
        }

        .textfield-outlined label:hover>input:not(:focus):-ms-input-placeholder,
        .textfield-outlined label:hover>textarea:not(:focus):-ms-input-placeholder {
            border-color: #272e36;
        }

        .textfield-outlined label:hover>input:not(:focus):placeholder-shown,
        .textfield-outlined label:hover>textarea:not(:focus):placeholder-shown {
            border-color: #272e36;
        }

        .textfield-outlined>input:focus,
        .textfield-outlined>textarea:focus {
            border-color: transparent #3d6f9e #3d6f9e;
            outline: none;
        }

        .textfield-outlined>input:focus+label,
        .textfield-outlined>textarea:focus+label {
            color: #3d6f9e;
        }

        .textfield-outlined>input:focus+label::before,
        .textfield-outlined>input:focus+label::after,
        .textfield-outlined>textarea:focus+label::before,
        .textfield-outlined>textarea:focus+label::after {
            border-top-color: #3d6f9e !important;
        }

        .textfield-outlined>input:disabled,
        .textfield-outlined>input:disabled+label,
        .textfield-outlined>textarea:disabled,
        .textfield-outlined>textarea:disabled+label {
            color: rgba(189, 192, 197, 0.5);
            border-color: transparent rgba(189, 192, 197, 0.5) rgba(189, 192, 197, 0.5) !important;
            pointer-events: none;
        }

        .textfield-outlined>input:disabled+label::before,
        .textfield-outlined>input:disabled+label::after,
        .textfield-outlined>textarea:disabled+label::before,
        .textfield-outlined>textarea:disabled+label::after {
            border-top-color: rgba(189, 192, 197, 0.5) !important;
        }

        .textfield-outlined>input:disabled:-moz-placeholder-shown,
        .textfield-outlined>input:disabled:-moz-placeholder-shown+label,
        .textfield-outlined>textarea:disabled:-moz-placeholder-shown,
        .textfield-outlined>textarea:disabled:-moz-placeholder-shown+label {
            border-top-color: rgba(189, 192, 197, 0.5) !important;
        }

        .textfield-outlined>input:disabled:-ms-input-placeholder,
        .textfield-outlined>input:disabled:-ms-input-placeholder+label,
        .textfield-outlined>textarea:disabled:-ms-input-placeholder,
        .textfield-outlined>textarea:disabled:-ms-input-placeholder+label {
            border-top-color: rgba(189, 192, 197, 0.5) !important;
        }

        .textfield-outlined>input:disabled:placeholder-shown,
        .textfield-outlined>input:disabled:placeholder-shown+label,
        .textfield-outlined>textarea:disabled:placeholder-shown,
        .textfield-outlined>textarea:disabled:placeholder-shown+label {
            border-top-color: rgba(189, 192, 197, 0.5) !important;
        }

        .textfield-outlined>input:disabled:-moz-placeholder-shown+label::before,
        .textfield-outlined>input:disabled:-moz-placeholder-shown+label::after,
        .textfield-outlined>textarea:disabled:-moz-placeholder-shown+label::before,
        .textfield-outlined>textarea:disabled:-moz-placeholder-shown+label::after {
            border-top-color: transparent !important;
        }

        .textfield-outlined>input:disabled:-ms-input-placeholder+label::before,
        .textfield-outlined>input:disabled:-ms-input-placeholder+label::after,
        .textfield-outlined>textarea:disabled:-ms-input-placeholder+label::before,
        .textfield-outlined>textarea:disabled:-ms-input-placeholder+label::after {
            border-top-color: transparent !important;
        }

        .textfield-outlined>input:disabled:placeholder-shown+label::before,
        .textfield-outlined>input:disabled:placeholder-shown+label::after,
        .textfield-outlined>textarea:disabled:placeholder-shown+label::before,
        .textfield-outlined>textarea:disabled:placeholder-shown+label::after {
            border-top-color: transparent !important;
        }

        @media not all and (-webkit-min-device-pixel-ratio: 0),
        not all and (min-resolution: 0.001dpcm) {
            @supports (-webkit-appearance: none) {

                .textfield-outlined>input,
                .textfield-outlined>input+label,
                .textfield-outlined>input+label::before,
                .textfield-outlined>input+label::after,
                .textfield-outlined>textarea,
                .textfield-outlined>textarea+label,
                .textfield-outlined>textarea+label::before,
                .textfield-outlined>textarea+label::after {
                    transition-duration: 0.1s;
                }
            }
        }

        @media only screen and (max-device-width: 600px) {
            .new_form {
                width: 100% !important;
                margin: 0 auto;
                /* display: flex; */
                /* justify-content: center;
            align-items: center; */
                height: 100vh;
            }

            form {
                border: 1px solid #CCCCCC;
                padding: 10px !important;
                background: #fff;
            }

        }





        .new_form {
            width: 400px;
            margin: 0 auto;
            /* display: flex; */
            /* justify-content: center;
            align-items: center; */
            height: 100vh;
        }

        form {
            border: 1px solid #CCCCCC;
            padding: 32px;
            background: #fff;
        }

        .pass {
            font-style: normal;
            font-weight: 600;
            color: #333333;
            font-size: 16px;
            text-align: center;
        }

        .new_form img {
            margin: 30px auto;
            display: block;
        }

        .new_form form a {
            color: #fff;
            font-size: 15px;
            text-decoration: none;
        }

        .is-invalid,
        .invalid-feedback {
            color: red
        }
    </style>



</head>

<body class="antialiased">
    <div class="container">

        <div class="new_form" style="margin-top:40px">
            @if (session('statusSuccess'))
                <div class=""
                    style="color: green;
                font-size: 20px;
                display: flex;
                justify-content: center;
                padding: 1px;"
                    role="alert">
                    <div
                        style="width: 28px;
                    height: 28px;
                    border-radius: 50%;
                    background: green;
                    display: flex;
                    padding: 6px;
                    margin: 0 4px;">
                        <span class="glyphicon glyphicon-ok" aria-hidden="true"
                            style="
                            color: white;
                            font-size: 15px;
                            font-weight: 100;
                        "></span>
                    </div>
                    {{ session('statusSuccess') }}
                </div>
            @else
                @if (isset($isToken) && isset($token))
                    @if (session('error'))
                        <div class="alert alert-message" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ url('/reset/password-change') }}" style="border-radius: 12px;">
                        @csrf
                        <input type="hidden" name="user_token" value="{{ $token }}">
                        <input type="hidden" name="user_type" value="{{ $type }}">
                        <p class="pass">Reset Password</p>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="textfield-outlined">
                                    {{-- <input id="input-one" type="password" placeholder=" "> --}}
                                    <input id="password" type="password"
                                        class=" @error('password') is-invalid @enderror" name="password"
                                        autocomplete="new-password" placeholder=" ">
                                    <label for="password">Enter password</label>

                                    @error('password')
                                        <span class="invalid-feedback alert-message" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="textfield-outlined">
                                    <input id="password-confirm" type="password"
                                        class="@error('password_confirmation') is-invalid @enderror"
                                        name="password_confirmation" autocomplete="new-password" placeholder=" ">
                                    <label for="password-confirm">Confirm password</label>

                                    @error('password_confirmation')
                                        <span class="invalid-feedback alert-message" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit"
                                    style="background: #ED5327;color: #fff;
                            text-decoration: none;
                            padding: 10px 12px;
                            font-size: 15px;
                            width:100%;
                            border-radius: 50px;text-align: center;align-items: center;padding: 12px 24px;border: transparent;">
                                    Done
                                </button>
                            </div>
                        </div>

                    </form>
                @else
                    <div class="alert alert-message" role="alert">

                        <div class=""
                            style="color: red;
                    font-size: 20px;
                    display: flex;
                    justify-content: center;
                    padding: 1px;"
                            role="alert">
                            <div
                                style="width: 28px;
                        height: 28px;
                        border-radius: 50%;
                        background: red;
                        display: flex;
                        padding: 6px;
                        margin: 0 4px;">
                                <span class="glyphicon glyphicon-remove" aria-hidden="true"
                                    style="
                                color: white;
                                font-size: 15px;
                                font-weight: 100;
                            "></span>
                            </div>
                            Link Expired
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

</body>

</html>
