<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (isset($title)  && $title)
        <title>{{ $company['fullname']   . ' - ' . $title }}</title>
    @else
        <title>{{ $company['fullname']  }}</title>
    @endif

    
    
</head>
<body id="body" style="background-color: #fff; display:none">
	<style>
		html * {
			font-family: "微軟正黑體", "Lato", "Helvetica Neue", Helvetica, Arial, sans-serif;
		}

		
		.home-title a:hover{
			border-bottom: 0px;
		}
	</style>
    <div id="head">
		<top-nav :model="topMenus"></top-nav>
		<section class="hero is-primary">
            <div class="hero-body">
                <div class="container">
                    <div class="columns is-vcentered">
                        <div class="column">
                            <p class="title is-2">
								<span class="home-title">
								<a href="/"  >
                            		{{ $company['fullname'] }}
								</a>
								</span>
                            
                            </p>
                            <p class="subtitle">
                                啟發善念 終身學習 生活有智慧
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero-foot is-hidden-mobile">
				<main-nav  :areas="areas" :items="menus"></main-nav>
            </div>
        </section>
		<sub-nav :items="subMenus"></sub-nav>
    </div>
    
    <section class="section" style="padding-top:20px;min-height: 510px;">

		<div id="main" class="container">

			 @yield('content')

		</div>
	</section>

    
	<hr />
	<footer>
		<div class="container">
			<div class="content">
				<span style="font-size: 1.2em"> &copy; 2018 - {{ $company['fullname'] }}</span>
				&nbsp;&nbsp;&nbsp;
				<span>地址：{{ $company['address'] }}</span> <span style="padding-left: 2em">電話：{{ $company['tel'] }}</span>
				
			</div>
		</div>
		
	

	</footer>

    <script src="{{ asset('js/babel.min.js') }}" ></script>
    <script src="{{ asset('js/polyfill.js') }}" ></script>
	<script src="{{ asset('js/client.js') }}"></script>
	
		
	<script type="text/babel">
		new Vue({

			el: '#head',
			data() {
				return {
					topMenus:null,
					areas:[],
					menus:[],
					subMenus:[],
				}
			},
			computed:{
				
			},
			created() {
				// Bus.$on('menus',this.setMenus);
				Bus.$on('errors',this.showErrorMsg);
				Bus.$on('okmsg',this.showSuccessMsg);

			},
			beforeMount() {
				this.topMenus = {!! json_encode($topMenus) !!} ;

				@if (isset($menus))

					this.menus = {!! json_encode($menus) !!} ;
				
				@endif

				@if (isset($areas))

					this.areas = {!! json_encode($areas) !!} ;

				@endif

				@if (isset($subMenus))

				this.subMenus = {!! json_encode($subMenus) !!} ;

				@endif
        		
			},
			methods: {
				showErrorMsg(msg) {
					if(!msg) msg='系統暫時無回應,請稍候再試.';
					this.$notify.open({
							content: msg,
							type: 'danger',
							placement: 'top-center',
							duration: 1500,
						}) 
				},
				showSuccessMsg(msg) {
					if(!msg) msg='存檔成功.';
					this.$notify.open({
							content: msg,
							type: 'success',
							placement: 'top-center',
							duration: 1500,
						}) 
				},
				
				

			},


		})

	</script>

	@yield('scripts')   

	<script>
		function onPageLoaded() { 
			$('#body').show();
		}
		

	</script>
   
    
</body>
</html>
