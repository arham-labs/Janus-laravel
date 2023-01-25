<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">




</head>

<body class="antialiased">
    <div class="container">
        <div class="new_form">
            @if (Session::has('message'))
                Very WLl
            @endif
            <div class="row">

                <div class="col-md-12">
                    @if (isset($message))
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
                            {{ $message }}
                        </div>
                    @else
                        This link is expired.
                    @endif
                </div>
            </div>
        </div>

    </div>

</body>

</html>
