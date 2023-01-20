<!DOCTYPE html>

<html>

<head>

    <title>Arhamlabs Authentication Package Email verification Testing</title>

</head>

<body>
    <img style="height: auto" src="{{ $details['tokenUrl'] }}" alt="arhamlabs-logo" width="60">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Verify Your Email Address</div>
                    <div class="card-body">
                        @if (session('resent'))
                            <div class="alert alert-success" role="alert">
                                {{ __('A fresh verification link has been sent to your email address.') }}
                            </div>
                        @endif
                        <a href="{{ $details['tokenUrl'] }}">Click Here</a>.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <p>Thank you,</p>
    <p>Team Arhamlabs</p>


</body>

</html>
