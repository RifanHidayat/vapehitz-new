<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <base href="../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">
    <!-- Page Title  -->
    <title>@yield('title')</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="{{ asset('assets/css/dashlite.css?ver=2.4.0') }}">
    <link id="skin-default" rel="stylesheet" href="{{ asset('assets/css/theme.css?ver=2.4.0') }}">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/libs/fontawesome-icons.css') }}">
    @yield('head')

    @yield('pagestyle')
</head>

<body class="nk-body bg-lighter npc-general has-sidebar ">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main">
            @include('layouts.sidebar')
            <!-- wrap @s -->
            <div class="nk-wrap">
                <!-- main header @s -->
                @include('layouts.header')
                <!-- main header @e -->
                <!-- content @s -->
                <div class="nk-content" id="app">
                    <div class="container-fluid">
                        <div class="nk-content-inner">
                            <div class="nk-content-body">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content @e -->
                <!-- footer @s -->

                <!-- footer @e -->
            </div>
            <!-- wrap @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->
    <!-- JavaScript -->
    <script src="{{ asset('assets/js/bundle.js?ver=2.4.0') }}"></script>
    <script src="{{ asset('assets/js/scripts.js?ver=2.4.0') }}"></script>
    <script src="{{ asset('assets/js/charts/gd-default.js?ver=2.4.0') }}"></script>
    @yield('script')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
    @yield('pagescript')
</body>

</html>