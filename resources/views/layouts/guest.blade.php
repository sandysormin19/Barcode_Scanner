<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>
    WMS Produksi - Login
</title>


<meta name="viewport" content="width=device-width, initial-scale=1">


<link rel="stylesheet"
href="{{ asset('vendor/adminlte/plugins/fontawesome-free/css/all.min.css') }}">


<link rel="stylesheet"
href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">


</head>


<body class="hold-transition login-page"
style="
background:
linear-gradient(
135deg,
rgba(0,123,255,.85),
rgba(0,40,100,.9)
),
url('/images/gudang.jpg');

background-size:cover;
background-position:center;
">

{{ $slot }}



<script src="{{ asset('vendor/adminlte/plugins/jquery/jquery.min.js') }}"></script>


<script src="{{ asset('vendor/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>


<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>


</body>

</html>
