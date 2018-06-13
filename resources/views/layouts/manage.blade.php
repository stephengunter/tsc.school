<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (isset($title))
        <title>{{ '慈大社推課程管理系統' . ' - ' . $title }}</title>
    @else
        <title>慈大社推課程管理系統</title>
    @endif

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/fontawesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/summernote/summernote.css') }}" rel="stylesheet">
    <link href="{{ asset('css/manage.css') }}" rel="stylesheet">
    
    <style>
		html * {
			font-family: "微軟正黑體", "Lato", "Helvetica Neue", Helvetica, Arial, sans-serif;
		}
	</style>
</head>
<body>
    
    <div>
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a href="/manage" class="navbar-brand" style="font-size:1.6em">慈大社推課程管理系統</a>
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        @isset($menus)
                            @foreach ($menus as $item)
                            {
                                <li style="font-size:1.2em" class="{{ $item['active'] ? "active" : "" }}">
                                    <a href="{{ $item['path'] }}">{{ $item['text'] }} </a>
                                </li>
                            }
                            @endforeach
                        @endisset
                        
                       
                    </ul>
                    @auth
                    <form action="/manage/logout" method="post" id="logoutForm" class="navbar-right">
                        @csrf    
                        <ul class="nav navbar-nav navbar-right">
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                {{ Auth::user()->profile->fullname }} 
                                </a>
                                <ul class="dropdown-menu" style="font-size: 15px;">
                                    <li>
                                        <a href="#" onclick="event.preventDefault();document.getElementById('logoutForm').submit();">
                                            登出
                                        </a>
                                    </li>
                                    <li><a href="/manage/change-password">變更密碼</a></li>
                                
                                </ul>
                            </li>
                        
                        </ul>
                    </form>    
                    @endauth
                        
                </div>
            </div>
        </nav>
    </div>
    <div id="main" class="container body-content" style="margin-top:1em; min-height: 960px;">

        @yield('content')

    </div>
    <hr />
    <footer class="container">
        <p>&copy; 2018 - {{ config('app.company.east.fullname') }}</p>

        <div id="footer" ></div>
        
    </footer>

    <script src="{{ asset('js/jquery.min.js') }}" ></script>
	<script src="{{ asset('js/tether.min.js') }}" ></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/summernote/summernote.min.js') }}" ></script>

    <script src="{{ asset('js/babel.min.js') }}" ></script>
    <script src="{{ asset('js/polyfill.js') }}" ></script>

   
    
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')   
   
</body>
</html>
