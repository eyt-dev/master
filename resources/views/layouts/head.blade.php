<!-- Title -->
<title>Admitro - Admin Panel HTML template</title>

<!--Favicon -->
<link rel="icon" href="{{URL::asset('assets/images/brand/favicon.ico')}}" type="image/x-icon"/>

<!--Bootstrap css -->
<link href="{{URL::asset('assets/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">

<!-- Style css -->
<link href="{{URL::asset('assets/css/style.css')}}" rel="stylesheet"/>
<link href="{{URL::asset('assets/css/dark.css')}}" rel="stylesheet"/>
<link href="{{URL::asset('assets/css/skin-modes.css')}}" rel="stylesheet"/>

<!-- Animate css -->
<link href="{{URL::asset('assets/css/animated.css')}}" rel="stylesheet"/>

<!--Sidemenu css -->
<link href="{{URL::asset('assets/css/sidemenu.css')}}" rel="stylesheet">

<!-- P-scroll bar css-->
<link href="{{URL::asset('assets/plugins/p-scrollbar/p-scrollbar.css')}}" rel="stylesheet"/>

<!---Icons css-->
<link href="{{URL::asset('assets/css/icons.css')}}" rel="stylesheet"/>

<!-- Simplebar css -->
<link rel="stylesheet" href="{{URL::asset('assets/plugins/simplebar/css/simplebar.css')}}">

<!-- Color Skin css -->
<link id="theme" href="{{URL::asset('assets/colors/color1.css')}}" rel="stylesheet" type="text/css"/>

<link id="theme" href="{{asset('assets/css/select2.min.css')}}" rel="stylesheet" type="text/css"/>

<link id="theme" href="{{asset('assets/css/bootstrap-datepicker.min.css')}}" rel="stylesheet" type="text/css"/>
<meta name="csrf-token" content="{{ csrf_token() }}">

@yield('css')
