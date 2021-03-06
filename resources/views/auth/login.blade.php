<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <base href="../../../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="./images/favicon.png">
    <!-- Page Title  -->
    <title>Login | DashLite Admin Template</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="./assets/css/dashlite.css?ver=2.4.0">
    <link id="skin-default" rel="stylesheet" href="./assets/css/theme.css?ver=2.4.0">
</head>

<body class="nk-body npc-general pg-auth">
    <div class="nk-app-root">
        <div class="nk-main ">
            <div class="nk-wrap nk-wrap-nosidebar">
                <div class="nk-content ">
                    <!--<div class="d-flex justify-content-center align-items-center" style="min-height: 100vh; min-width: 100%">-->
                    <!--    <div>-->
                    <!--        <h1>MAINTENANCE</h1>-->
                    <!--    <h1 id="demo"></h1>-->
                    <!--    </div>-->
                    <!--</div>-->
                    <div class="nk-split nk-split-page nk-split-md">
                        <div class="nk-split-content nk-block-area nk-block-area-column nk-auth-container bg-primary">
                            <div class="absolute-top-right d-lg-none p-3 p-sm-5">
                                <a href="#" class="toggle btn-white btn btn-icon btn-light" data-target="athPromo"><em class="icon ni ni-info"></em></a>
                            </div>
                            <div class="nk-block nk-block-middle nk-auth-body">
                                <div class="card bg-lighter">
                                    <div class="card-inner">
                                        <h3 class="text-center">Login</h3>
                                    </div>
                                    <form action="{{ route('login') }}" method="post">
                                        @csrf
                                        <div class="card-body">
                                            @if(session('errors'))
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                Something it's wrong:
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">??</span>
                                                </button>
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            @endif
                                            @if (Session::has('success'))
                                            <div class="alert alert-success">
                                                {{ Session::get('success') }}
                                            </div>
                                            @endif
                                            @if (Session::has('error'))
                                            <div class="alert alert-danger">
                                                {{ Session::get('error') }}
                                            </div>
                                            @endif
                                            <div class="form-group">
                                                <label for=""><strong>Username</strong></label>
                                                <input type="text" name="username" class="form-control" placeholder="Username">
                                            </div>
                                            <div class="form-group">
                                                <label for=""><strong>Password</strong></label>
                                                <input type="password" name="password" class="form-control" placeholder="Password">
                                            </div>
                                        </div>
                                        <div class="card-inner text-right">
                                            <button type="submit" class="btn btn-primary">Masuk</button>
                                             <p class="text-center">Belum punya akun? <a href="{{ route('register') }}">Register</a> sekarang!</p> 
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="nk-split-content nk-split-stretch bg-lighter d-flex toggle-break-lg toggle-slide toggle-slide-right" data-content="athPromo" data-toggle-screen="lg" data-toggle-overlay="true">
                            <div class="w-100 w-max-500px p-4 p-sm-4 m-4">
                                <div class="nk-feature nk-feature-center">
                                    <div class="nk-feature-content py-4 p-sm-5">
                                    </div>
                                    <div class="nk-feature-img">
                                        <img src="{{url('/images/vapehitz-logo2.jpeg')}}" srcset="./images/slides/promo-a2x.png 2x" alt="">
                                    </div>
                                </div>
                                <div class="nk-block bg-lighter text-center mt-5">
                                    &copy; 2021<b class="text-info"> VAPE</b><b class="text-dark">HITZ</b> | All Right Reserved.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="./assets/js/bundle.js?ver=2.4.0"></script>
    <script src="./assets/js/scripts.js?ver=2.4.0"></script>
    <script>
// Set the date we're counting down to
var countDownDate = new Date("Nov 20, 2021 12:00:00").getTime();

// Update the count down every 1 second
var x = setInterval(function() {

  // Get today's date and time
  var now = new Date().getTime();

  // Find the distance between now and the count down date
  var distance = countDownDate - now;

  // Time calculations for days, hours, minutes and seconds
  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  var seconds = Math.floor((distance % (1000 * 60)) / 1000);

  // Display the result in the element with id="demo"
  document.getElementById("demo").innerHTML = days + "d " + hours + "h "
  + minutes + "m " + seconds + "s ";

  // If the count down is finished, write some text
  if (distance < 0) {
    clearInterval(x);
    document.getElementById("demo").innerHTML = "EXPIRED";
  }
}, 1000);
</script>
</body>

</html>