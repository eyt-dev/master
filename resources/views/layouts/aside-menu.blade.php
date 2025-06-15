<aside class="app-sidebar ps ps--active-y">
	<div class="app-sidebar__logo">
		<a class="header-brand" href="{{url('/e')}}">
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
		{{-- <li class="slide">
			<a class="side-menu__item"  href="{{route('dashboard', ['site' => $siteSlug])}}">
			<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"/></svg>
			<span class="side-menu__label">Dashboard</span></a>
		</li>
		@canany(['view.setting'])
		<li class="slide">
			<a class="side-menu__item" href="{{route('setting.index', ['site' => $siteSlug])}}">
			<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M16.66 4.52l2.83 2.83-2.83 2.83-2.83-2.83 2.83-2.83M9 5v4H5V5h4m10 10v4h-4v-4h4M9 15v4H5v-4h4m7.66-13.31L11 7.34 16.66 13l5.66-5.66-5.66-5.65zM11 3H3v8h8V3zm10 10h-8v8h8v-8zm-10 0H3v8h8v-8z"></path></svg>
			<span class="side-menu__label">Settings</span></a>
		</li>
		@endcan
		@canany(['view.role', 'view.permission'])
		<li class="slide">
			<a class="side-menu__item" data-toggle="slide" href="#">
			<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M16.66 4.52l2.83 2.83-2.83 2.83-2.83-2.83 2.83-2.83M9 5v4H5V5h4m10 10v4h-4v-4h4M9 15v4H5v-4h4m7.66-13.31L11 7.34 16.66 13l5.66-5.66-5.66-5.65zM11 3H3v8h8V3zm10 10h-8v8h8v-8zm-10 0H3v8h8v-8z"></path></svg>
			<span class="side-menu__label">Authorization</span><i class="angle fa fa-angle-right"></i></a>
			<ul class="slide-menu ">
				@can('view.role')
					<li><a href="{{route('role.index', ['site' => $siteSlug])}}" class="slide-item">Roles</a></li>
				@endcan
				@can('view.permission')
				<li><a href="{{route('permission.index', ['site' => $siteSlug])}}" class="slide-item">Permissions</a></li>
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
		
		@can('view.admin')
			<li class="slide">
				<a class="side-menu__item" href="{{ route('admins.index', ['site' => $siteSlug, 'type' => 1]) }}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"/></svg>
					<span class="side-menu__label">Admins</span>
				</a>
			</li>
		@endcan
		@can('view.public_vendor')
			<li class="slide">
				<a class="side-menu__item" href="{{ route('admins.index', ['site' => $siteSlug, 'type' => 2]) }}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"/></svg>
					<span class="side-menu__label">Public Vendors</span>
				</a>
			</li>
		@endcan

		@can('view.private_vendor')
			<li class="slide">
				<a class="side-menu__item" href="{{ route('admins.index', ['site' => $siteSlug, 'type' => 3]) }}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"/></svg>
					<span class="side-menu__label">Private Vendors</span>
				</a>
			</li>
		@endcan
		
		@canany(['view.game', 'view.wheel'])
			<li class="side-item side-item-category mt-4">
				Fortune Wheel
			</li>
			@can('view.game')
				<li class="slide">
					<a class="side-menu__item"  href="{{route('game.index', ['site' => $siteSlug])}}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"/></svg>
					<span class="side-menu__label">Games</span></a>
				</li>
			@endcan
			@can('view.wheel')
				<li class="slide">
					<a class="side-menu__item"  href="{{route('wheel.index', ['site' => $siteSlug])}}">
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
					<a class="side-menu__item"  href="{{route('store_view.index', ['site' => $siteSlug])}}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"/></svg>
					<span class="side-menu__label">Store View</span></a>
				</li>
			@endcan
			@can('view.category')
				<li class="slide">
					<a class="side-menu__item"  href="{{route('category.index', ['site' => $siteSlug])}}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"/></svg>
					<span class="side-menu__label">Category</span></a>
				</li>
			@endcan
			@can('view.page')
				<li class="slide">
					<a class="side-menu__item"  href="{{route('page.index', ['site' => $siteSlug])}}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"/></svg>
					<span class="side-menu__label">Page</span></a>
				</li>
			@endcan
			@can('view.slide')
				<li class="slide">
					<a class="side-menu__item"  href="{{route('slide.index', ['site' => $siteSlug])}}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"/></svg>
					<span class="side-menu__label">Slide</span></a>
				</li>
			@endcan
			@can('view.testimonial')
				<li class="testimonial">
					<a class="side-menu__item"  href="{{route('testimonial.index', ['site' => $siteSlug])}}">
					<svg class="side-menu__icon" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"/></svg>
					<span class="side-menu__label">Testimonial</span></a>
				</li>
			@endcan
	
		@endcan --}}

</aside>
<!--aside closed-->