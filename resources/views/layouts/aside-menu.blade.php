<aside class="app-sidebar ps ps--active-y">
	<div class="app-sidebar__logo">
		<a class="header-brand" href="{{url('/' . $page='index')}}">
			<img src="{{URL::asset('assets/images/brand/logo.png')}}" class="header-brand-img desktop-lgo" alt="Admintro logo">
			<img src="{{URL::asset('assets/images/brand/logo1.png')}}" class="header-brand-img dark-logo" alt="Admintro logo">
			<img src="{{URL::asset('assets/images/brand/favicon.png')}}" class="header-brand-img mobile-logo" alt="Admintro logo">
			<img src="{{URL::asset('assets/images/brand/favicon1.png')}}" class="header-brand-img darkmobile-logo" alt="Admintro logo">
		</a>
	</div>
	<div class="app-sidebar__user">
		<div class="dropdown user-pro-body text-center">
			<div class="user-pic">
				<img src="{{URL::asset('assets/images/users/2.jpg')}}" alt="user-img" class="avatar-xl rounded-circle mb-1">
			</div>
			<div class="user-info">
				<h5 class=" mb-1">{{Str::title(auth()->user()->name)}} <i class="ion-checkmark-circled  text-success fs-12"></i></h5>
				<span class="text-muted app-sidebar__user-name text-sm">{{Str::title(auth()->user()->role)}}</span>
			</div>
		</div>
	</div>
	<ul class="side-menu app-sidebar3">
		<li class="side-item side-item-category mt-4">Main</li>
		<li class="slide">
			<a class="side-menu__item"  href="{{url('/dashboard')}}">
			<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"/></svg>
			<span class="side-menu__label">Dashboard</span></a>
		</li>
		@canany(['view.role', 'view.permission'])
		<li class="slide">
			<a class="side-menu__item" data-toggle="slide" href="#">
			<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M16.66 4.52l2.83 2.83-2.83 2.83-2.83-2.83 2.83-2.83M9 5v4H5V5h4m10 10v4h-4v-4h4M9 15v4H5v-4h4m7.66-13.31L11 7.34 16.66 13l5.66-5.66-5.66-5.65zM11 3H3v8h8V3zm10 10h-8v8h8v-8zm-10 0H3v8h8v-8z"></path></svg>
			<span class="side-menu__label">Authorization</span><i class="angle fa fa-angle-right"></i></a>
			<ul class="slide-menu ">
				@can('view.role')
					<li><a href="{{url('/role')}}" class="slide-item">Roles</a></li>
				@endcan
				@can('view.permission')
				<li><a href="{{url('/permission')}}" class="slide-item">Permissions</a></li>
				@endcan
			</ul>
		</li>
		@endcan
		
		<li class="side-item side-item-category mt-4">
			Customer
		</li>
		@php
			$userType = auth()->user()->type;
		@endphp
		
		{{-- @if($userType == 0) Super Admin --}}
		@can('view.admin')
			<li class="slide">
				<a class="side-menu__item" href="{{ route('admins.index', ['type' => 1]) }}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"/></svg>
					<span class="side-menu__label">Admins</span>
				</a>
			</li>
		@endcan
		@can('view.public_vendor')
			<li class="slide">
				<a class="side-menu__item" href="{{ route('admins.index', ['type' => 2]) }}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"/></svg>
					<span class="side-menu__label">Public Vendors</span>
				</a>
			</li>
		@endcan
		{{-- @endif --}}

		{{-- @if($userType == 0 || $userType == 1) Super Admin & Admin can see this --}}
		@can('view.private_vendor')
			<li class="slide">
				<a class="side-menu__item" href="{{ route('admins.index', ['type' => 3]) }}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"/></svg>
					<span class="side-menu__label">Private Vendors</span>
				</a>
			</li>
		@endcan
		{{-- @endif --}}
		
		@canany(['view.game', 'view.wheel'])
			<li class="side-item side-item-category mt-4">
				Fortune Wheel
			</li>
			@can('view.game')
				<li class="slide">
					<a class="side-menu__item"  href="{{url('/game')}}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"/></svg>
					<span class="side-menu__label">Games</span></a>
				</li>
			@endcan
			@can('view.wheel')
				<li class="slide">
					<a class="side-menu__item"  href="{{url('/wheel')}}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"/></svg>
					<span class="side-menu__label">Wheels</span></a>
				</li>
			@endcan
		@endcan
		
		@canany(['view.store_view'])
			<li class="side-item side-item-category mt-4">
				Configuration
			</li>
			@can('view.store_view')
				<li class="slide">
					<a class="side-menu__item"  href="{{url('/store_view')}}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"/></svg>
					<span class="side-menu__label">Store View</span></a>
				</li>
			@endcan
			@can('view.category')
				<li class="slide">
					<a class="side-menu__item"  href="{{url('/category')}}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"/></svg>
					<span class="side-menu__label">Category</span></a>
				</li>
			@endcan
			@can('view.page')
				<li class="slide">
					<a class="side-menu__item"  href="{{url('/page')}}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"/></svg>
					<span class="side-menu__label">Page</span></a>
				</li>
			@endcan
	
		@endcan
		
	{{-- 	<li class="slide">
			<a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#')}}">
			<svg  class="side-menu__icon"  xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 5.99L19.53 19H4.47L12 5.99M12 2L1 21h22L12 2zm1 14h-2v2h2v-2zm0-6h-2v4h2v-4z"/></svg>
			<span class="side-menu__label">Error Pages</span><i class="angle fa fa-angle-right"></i></a>
			<ul class="slide-menu">
				<li><a href="{{url('/' . $page='400')}}" class="slide-item"> 400</a></li>
				<li><a href="{{url('/' . $page='401')}}" class="slide-item"> 401</a></li>
				<li><a href="{{url('/' . $page='403')}}" class="slide-item"> 403</a></li>
				<li><a href="{{url('/' . $page='404')}}" class="slide-item"> 404</a></li>
				<li><a href="{{url('/' . $page='500')}}" class="slide-item"> 500</a></li>
				<li><a href="{{url('/' . $page='503')}}" class="slide-item"> 503</a></li>
			</ul>
		</li>
	</ul> --}}
</aside>
<!--aside closed-->