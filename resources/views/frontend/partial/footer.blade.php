<footer style="background: #0f172a; color: #94a3b8; padding-top: 80px; font-family: 'Inter', sans-serif;">
	<div class="grid-row">
		<div class="grid-col-row clear-fix" style="display: flex; flex-wrap: wrap; justify-content: space-between; margin-bottom: 50px;">
			
			<!-- Contact Us Section -->
			<section class="grid-col grid-col-4 footer-about" style="margin-bottom: 30px;">
				<h2 style="font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 600; color: #ffffff; margin-bottom: 25px; text-transform: uppercase; letter-spacing: 1px;">
					@lang('site.menu_contact_us')
				</h2>
				<address style="font-style: normal; line-height: 1.8;">
					@if(isset($siteInfo['phone']) && isset($siteInfo['email']) && isset($siteInfo['address']))
						<div style="display: flex; align-items: center; margin-bottom: 12px;">
							<i class="fa fa-phone" style="width: 20px; color: #3b82f6; margin-right: 10px;"></i>
							<a href="tel:{{$siteInfo['phone']}}" style="color: #94a3b8; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#94a3b8'">
								{{$siteInfo['phone']}}
							</a>
						</div>
						<div style="display: flex; align-items: center; margin-bottom: 12px;">
							<i class="fa fa-envelope" style="width: 20px; color: #3b82f6; margin-right: 10px;"></i>
							<a href="mailto:{{$siteInfo['email']}}" style="color: #94a3b8; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#94a3b8'">
								{{$siteInfo['email']}}
							</a>
						</div>
						<div style="display: flex; align-items: flex-start; margin-bottom: 12px;">
							<i class="fa fa-map-marker" style="width: 20px; color: #3b82f6; margin-right: 10px; margin-top: 5px;"></i>
							<a href="{{URL::route('site.contact_us_view')}}" style="color: #94a3b8; text-decoration: none; transition: color 0.3s; max-width: 200px;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#94a3b8'">
								{{$siteInfo['address']}}
							</a>
						</div>
					@else
						<p> @lang('site.empty_content') </p>
					@endif
				</address>
			</section>

			<!-- Latest Event Section -->
			<section class="grid-col grid-col-4 footer-latest" style="margin-bottom: 30px;">
				<h2 style="font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 600; color: #ffffff; margin-bottom: 25px; text-transform: uppercase; letter-spacing: 1px;">
					@lang('site.up_event')
				</h2>
				@if($event)
				<article style="display: flex; align-items: flex-start; gap: 15px;">
					<div style="width: 60px; height: 60px; background: rgba(59, 130, 246, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
						<i class="fa fa-calendar" style="font-size: 24px; color: #3b82f6;"></i>
					</div>
					<div>
						<h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; font-weight: 600; margin: 0 0 8px 0; line-height: 1.4;">
							<a href="{{URL::route('site.event_details',$event->slug)}}" style="color: #ffffff; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='#ffffff'">
								{{$event->title}}
							</a>
						</h3>
						<div class="course-date" style="font-size: 13px; color: #64748b; display: flex; gap: 10px;">
							<span><i class="fa fa-clock-o" style="margin-right: 4px;"></i>{{$event->event_time->format('h:i a')}}</span>
							<span><i class="fa fa-calendar-o" style="margin-right: 4px;"></i>{{$event->event_time->format('d.m.y')}}</span>
						</div>
					</div>
				</article>
				@else
					<p style="color: #64748b;">No upcoming events.</p>
				@endif
			</section>

			<!-- Quick Links Section -->
			<section class="grid-col grid-col-4 footer-links" style="margin-bottom: 30px;">
				<h2 style="font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 600; color: #ffffff; margin-bottom: 25px; text-transform: uppercase; letter-spacing: 1px;">
					@lang('site.help_links')
				</h2>
				<div style="display: flex; gap: 40px;">
					<ul style="list-style: none; padding: 0; margin: 0;">
						<li style="margin-bottom: 12px;"><a href="{{URL::route('site.faq_view')}}" style="color: #94a3b8; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#94a3b8'">@lang('site.menu_faq')</a></li>
						<li style="margin-bottom: 12px;"><a href="#" style="color: #94a3b8; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#94a3b8'">@lang('site.menu_admission')</a></li>
						<li style="margin-bottom: 12px;"><a href="{{route('report.marksheet_pub')}}" style="color: #94a3b8; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#94a3b8'">@lang('site.menu_result')</a></li>
					</ul>
					<ul style="list-style: none; padding: 0; margin: 0;">
						<li style="margin-bottom: 12px;"><a href="{{URL::route('site.timeline_view')}}" style="color: #94a3b8; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#94a3b8'">@lang('site.menu_timeline')</a></li>
						<li style="margin-bottom: 12px;"><a href="{{URL::route('site.gallery_view')}}" style="color: #94a3b8; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#94a3b8'">@lang('site.menu_gallery')</a></li>
						<li style="margin-bottom: 12px;"><a href="{{URL::route('site.contact_us_view')}}" style="color: #94a3b8; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#94a3b8'">@lang('site.menu_contact_us')</a></li>
					</ul>
				</div>
			</section>
		</div>
	</div>

	<!-- Footer Bottom -->
	<div class="footer-bottom" style="border-top: 1px solid rgba(255,255,255,0.05); padding: 25px 0;">
		<div class="grid-row clear-fix" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center;">
			
			<div class="copyright" style="color: #64748b; font-size: 14px;">
				&copy; {{ date('Y') }} {{$siteInfo['name']}}. @lang('site.copy_right')
			</div>

			<div class="footer-social" style="display: flex; gap: 15px;">
				<a target="_blank" href="@if($siteInfo['facebook']){{$siteInfo['facebook']}}@else #@endif" class="fa fa-facebook" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.05); border-radius: 50%; color: #94a3b8; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.background='#3b82f6'; this.style.color='#ffffff'" onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.color='#94a3b8'"></a>
				<a target="_blank" href="@if($siteInfo['instagram']){{$siteInfo['instagram']}}@else #@endif" class="fa fa-instagram" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.05); border-radius: 50%; color: #94a3b8; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.background='#db2777'; this.style.color='#ffffff'" onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.color='#94a3b8'"></a>
				<a target="_blank" href="@if($siteInfo['twitter']){{$siteInfo['twitter']}}@else #@endif" class="fa fa-twitter" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.05); border-radius: 50%; color: #94a3b8; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.background='#0ea5e9'; this.style.color='#ffffff'" onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.color='#94a3b8'"></a>
				<a target="_blank" href="@if($siteInfo['youtube']){{$siteInfo['youtube']}}@else #@endif" class="fa fa-youtube" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.05); border-radius: 50%; color: #94a3b8; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.background='#dc2626'; this.style.color='#ffffff'" onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.color='#94a3b8'"></a>
			</div>

			<div class="maintainedby" style="color: #64748b; font-size: 14px;">
				@lang('site.maintainer')
				<a href="{{$maintainer_url}}" style="color: #3b82f6; text-decoration: none; font-weight: 500; transition: color 0.3s;" onmouseover="this.style.color='#60a5fa'" onmouseout="this.style.color='#3b82f6'">
					{{$maintainer}}
				</a>
			</div>
			
		</div>
	</div>
</footer>