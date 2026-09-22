@extends('layouts.master')
@section('page-header')
<!--Page header-->
<div class="page-header">
	<div class="page-leftheader">
		<h4 class="page-title mb-0">Hi! Welcome Back</h4>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="{{url('/' . $page='#')}}"><i class="fe fe-home mr-2 fs-14"></i>Home</a></li>
			<li class="breadcrumb-item active" aria-current="page"><a href="{{url('/' . $page='#')}}">Dashboard</a></li>
		</ol>
	</div>
</div>
<!--End Page header-->
@endsection
@section('content')

<!-- Module Statistics Sections -->
@if(!empty($modules))
	@foreach($modules as $key => $category)
		<div class="mb-4">
			<h5 class="mb-3 font-weight-bold">{{ $category['title'] }}</h5>
			<div class="row">
				@foreach($category['items'] as $index => $item)
					@php
						$colors = ['bg-primary', 'bg-success', 'bg-danger', 'bg-warning', 'bg-info', 'bg-secondary', 'bg-dark'];
						$color = $colors[$index % count($colors)];
					@endphp
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<a href="{{ route($item['route'], ['username' => $siteSlug ?? auth()->user()->username]) }}" class="text-decoration-none">
							<div class="card overflow-hidden dash1-card border-0 transition-all hover-shadow">
								<div class="card-body">
									<div class="d-flex justify-content-between align-items-start">
										<div class="flex-grow-1">
											<p class="mb-1 text-muted">{{ $item['name'] }}</p>
											<h2 class="mb-1 number-font">{{ $item['count'] }}</h2>
										</div>
										<div class="module-icon {{ $color }} rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; color: white;">
											<i class="fe {{ $item['icon'] }} fs-18"></i>
										</div>
									</div>
									<small class="fs-12 text-muted d-block mt-2">Total Records</small>
								</div>
							</div>
						</a>
					</div>
				@endforeach
			</div>
		</div>
	@endforeach
@else
	<div class="alert alert-info" role="alert">
		<i class="fe fe-info mr-2"></i>
		No modules available. Please contact administrator.
	</div>
@endif

@endsection

@section('js')
<script>
	// Add smooth hover effects
	document.addEventListener('DOMContentLoaded', function() {
		const cards = document.querySelectorAll('.dash1-card');
		cards.forEach(card => {
			card.addEventListener('mouseenter', function() {
				this.style.transform = 'translateY(-5px)';
				this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
			});
			card.addEventListener('mouseleave', function() {
				this.style.transform = 'translateY(0)';
				this.style.boxShadow = 'none';
			});
		});
	});
</script>
@endsection