{{-- @if(Session::has('message'))
<div class="alert alert-success">{{Session::get('message')}}</div>
@endif
thanks --}}
{{-- @if(Session::has('error'))
  <div>
    {{  Session::get('error')}}
    
</div>
@else
<div>
    {{  Session::get('error')}}
    
</div>
@endif --}}
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags must come first in the head; any other head content must come after these tags -->

	<title>Success {{ session('success')['code'] }}</title>

	<!-- Google font -->
	<link href="https://fonts.googleapis.com/css?family=Nunito:400,700" rel="stylesheet">

  <style>
    :root {
        --base-url: {{ json_encode(asset('')) }};
    }
  </style>

	<!-- Custom stlylesheet -->
	<link type="text/css" rel="stylesheet" href="{{asset('css/error/jazz_error.css?v='.time())}}" />

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

</head>

<body>

	<div id="notfound">
		<div class="notfound">
			<div class="notfound-404" style="background-image: url({{asset('images/success/jazz_cash_success.png')}})"></div>
			<h1>{{ session('success')['code'] }}</h1>
			<h2>{{ session('success')['text'] }}</h2>
			<p>Sorry but the page you are looking for does not exist, have been removed. name changed or is temporarily unavailable</p>
			<a style="cursor: pointer;" onclick="history.back()">Return Back</a>
		</div>
	</div>

</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>
