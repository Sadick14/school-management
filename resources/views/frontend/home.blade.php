@extends('frontend.layouts.master')

@section('pageTitle') @lang('site.menu_home') @endsection

@section('pageContent')
	
	<!-- Modern SaaS Hero Section -->
	<section class="modern-hero-section" style="padding: 100px 0; background: linear-gradient(135deg, #f0f2ff 0%, #dbe2ff 100%); text-align: center;">
		<div class="grid-row">
			<h1 style="font-size: 52px; font-weight: 700; color: #0f172a; margin-bottom: 20px;">Manage Your Institute with Ease</h1>
			<p style="font-size: 20px; color: #475569; max-width: 600px; margin: 0 auto 40px auto; line-height: 1.6;">
				Experience a premium, glassmorphic dashboard designed to simplify administration, empower teachers, and engage students.
			</p>
			<a href="{{ route('login') }}" class="cws-button border-radius" style="font-size: 16px; padding: 16px 40px;">
				Get Started
				<i class="fa fa-angle-double-right"></i>
			</a>
		</div>
	</section>

	<hr class="divider-color">
	
	<!-- content -->
	<div id="home" class="page-content padding-none">
		<section class="fullwidth-background padding-section">
			<div class="grid-row clear-fix">
				<h2 class="center-text" style="margin-bottom: 40px;">@lang('site.about_us')</h2>
				@if($aboutContent)
					<div class="grid-col-row">
						<div class="grid-col grid-col-6">
							<h3 style="font-size: 28px; margin-bottom: 20px;">@lang('site.why_we')</h3>
							<p style="font-size: 16px; line-height: 1.8; color: #475569;">{{ $aboutContent->why_content }}</p>
							
							<!-- accordions -->
							<div class="accordions" style="margin-top: 30px;">
							@if($aboutContent->key_point_1_title)
									<div class="content-title active">{{$aboutContent->key_point_1_title}}</div>
									<div class="content">{!! $aboutContent->key_point_1_content !!}</div>
							@endif
							@if($aboutContent->key_point_2_title)
									<div class="content-title">{{$aboutContent->key_point_2_title}}</div>
									<div class="content">{!! $aboutContent->key_point_2_content !!}</div>
							@endif
							@if($aboutContent->key_point_3_title)
									<div class="content-title">{{$aboutContent->key_point_3_title}}</div>
									<div class="content">{!! $aboutContent->key_point_3_content !!}</div>
							@endif
							</div>
							<!--/accordions -->
						</div>
						<div class="grid-col grid-col-6">
							@if($aboutImages)
								<div class="owl-carousel full-width-slider" style="border-radius: 24px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1);">
									@foreach($aboutImages as $slider)
										<div class="gallery-item picture">
											<img src="{{asset('storage/about/'.$slider->image)}}" alt>
										</div>
									@endforeach
								</div>
							@else
								<div class="alert alert-warning">
									<span>@lang('site.empty_content')</span>
								</div>
							@endif
						</div>
					</div>
				@else
					<div class="alert alert-warning">
						<span>@lang('site.empty_content')</span>
					</div>
				@endif
			</div>
		</section>

		<hr class="divider-color" />

		<!-- Service Grid (Mimicking 'You Need to Hire' cards) -->
		<section class="fullwidth-background padding-section" style="background: #f8fafc;">
			<div class="grid-row clear-fix text-center">
				<h2 class="center-text" style="margin-bottom: 20px;">@lang('site.service')</h2>
				@if($ourService)
					<p style="color: #64748b; margin-bottom: 50px;">{{$ourService->meta_value}}</p>
				@endif
				
				<div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px;">
					<div class="counter-block" style="flex: 1 1 200px; max-width: 250px;">
						<div class="service-icon" style="background: #eef2ff; color: #3b82f6;"><i class="flaticon-graduate"></i></div>
						<div class="author-info">Education</div>
					</div>
					<div class="counter-block" style="flex: 1 1 200px; max-width: 250px;">
						<div class="service-icon" style="background: #fffbeb; color: #d97706;"><i class="flaticon-medical"></i></div>
						<div class="author-info">Medical Facility</div>
					</div>
					<div class="counter-block" style="flex: 1 1 200px; max-width: 250px;">
						<div class="service-icon" style="background: #fdf2f8; color: #db2777;"><i class="flaticon-restaurant"></i></div>
						<div class="author-info">Canteen</div>
					</div>
					<div class="counter-block" style="flex: 1 1 200px; max-width: 250px;">
						<div class="service-icon" style="background: #f0fdfa; color: #0d9488;"><i class="sms-icon-bus"></i></div>
						<div class="author-info">Transport</div>
					</div>
					<div class="counter-block" style="flex: 1 1 200px; max-width: 250px;">
						<div class="service-icon" style="background: #faf5ff; color: #9333ea;"><i class="flaticon-book1"></i></div>
						<div class="author-info">Library</div>
					</div>
				</div>
			</div>
		</section>
		
		<hr class="divider-color" />

		<!-- Modern Statistics Section -->
		<section class="padding-section" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
			<div class="grid-row">
				<div class="grid-col-row clear-fix statistic">
					@if($statistic)
					<div class="grid-col grid-col-3 alt">
						<div class="counter-block" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
							<i class="flaticon-multiple" style="color: #60a5fa !important;"></i>
							<div class="counter" data-count="{{$statistic->student}}" style="color: white !important;">0</div>
							<div class="counter-name" style="color: #bfdbfe !important;">@lang('site.stat_students')</div>
						</div>
					</div>
					<div class="grid-col grid-col-3 alt">
						<div class="counter-block" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
							<i class="sms-icon-group" style="color: #60a5fa !important;"></i>
							<div class="counter" data-count="{{$statistic->teacher}}" style="color: white !important;">0</div>
							<div class="counter-name" style="color: #bfdbfe !important;">@lang('site.stat_teachers')</div>
						</div>
					</div>
					<div class="grid-col grid-col-3 alt">
						<div class="counter-block" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
							<i class="flaticon-college" style="color: #60a5fa !important;"></i>
							<div class="counter" data-count="{{$statistic->graduate}}" style="color: white !important;">0</div>
							<div class="counter-name" style="color: #bfdbfe !important;">@lang('site.stat_college')</div>
						</div>
					</div>
					<div class="grid-col grid-col-3 alt">
						<div class="counter-block" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
							<i class="flaticon-book1 last" style="color: #60a5fa !important;"></i>
							<div class="counter" data-count="{{$statistic->books}}" style="color: white !important;">0</div>
							<div class="counter-name" style="color: #bfdbfe !important;">@lang('site.stat_books')</div>
						</div>
					</div>
					@endif
				</div>
			</div>
		</section>

		<hr class="divider-color" />
		
		<!-- Testimonials -->
		<section class="fullwidth-background testimonial padding-section">
			<div class="grid-row">
				<h2 class="center-text">@lang('site.testimonials')</h2>
				<div class="owl-carousel testimonials-carousel">
					@foreach($testimonials as $test)
					<div class="gallery-item" style="text-align: center;">
						<div class="quote-avatar-author clear-fix" style="display: flex; flex-direction: column; align-items: center;">
							<img src="@if($test->photo ){{ asset('storage/testimonials')}}/{{ $test->photo }} @else {{ asset('images/avatar.jpg')}} @endif" alt="" style="margin-bottom: 15px;">
							<div class="author-info">{{$test->writer}}</div>
						</div>
						<p style="color: #64748b; font-style: italic; margin-top: 15px;">"{{$test->comments}}"</p>
					</div>
					@endforeach
				</div>
			</div>
		</section>

		<hr class="divider-color" />
		
		<!-- Subscribe / CTA Section -->
		<div style="background: #f8fafc; padding: 80px 0;">
			<div class="grid-row center-text">
				<h2 style="font-size: 32px; font-weight: 700; margin-bottom: 10px;">@lang('site.get_in')</h2>
				<div class="divider-mini" style="margin: 0 auto 20px auto;"></div>
				<p style="font-size: 16px; color: #64748b; margin-bottom: 30px;">@lang('site.drop_email')</p>
				<form id="subscribeFrom" class="subscribe" action="{{URL::route('site.subscribe')}}" method="POST" enctype="multipart/form-data" style="max-width: 500px; margin: 0 auto;">
					@csrf
					<div style="display: flex; align-items: center; justify-content: center;">
						<input type="email" name="email" size="40" required placeholder="@lang('site.write_email')" aria-required="true" style="width: 100%; border-radius: 50px 0 0 50px !important;">
						<input type="submit" value="@lang('site.subscribe')" style="margin-left: -20px !important; border-radius: 50px !important; z-index: 2; position: relative;">
					</div>
				</form>
			</div>
		</div>
		
	</div>
	<!-- / content -->

@endsection
