<header class="main-header">
    <!-- Logo -->
    <a href="{{ URL::route('user.dashboard') }}" class="logo" style="background: #ffffff; border-right: 1px solid #f1f5f9; border-bottom: none; display: flex; align-items: center; justify-content: flex-start; padding-left: 20px;">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini" style="color: #0f172a; font-weight: 700;">
            <div style="background: #3b82f6; color: white; width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                @if(isset($appSettings['institute_settings']['short_name']))
                    {{ substr($appSettings['institute_settings']['short_name'], 0, 1) }}
                @else
                    N
                @endif
            </div>
        </span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg" style="color: #0f172a; font-weight: 800; font-family: 'Outfit', sans-serif; font-size: 22px; display: flex; align-items: center; gap: 10px;">
            <div style="background: #3b82f6; color: white; width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                @if(isset($appSettings['institute_settings']['short_name']))
                    {{ substr($appSettings['institute_settings']['short_name'], 0, 1) }}
                @else
                    N
                @endif
            </div>
            @if(isset($appSettings['institute_settings']['short_name']))
                {{$appSettings['institute_settings']['short_name']}}
            @else
                Newlife CLA
            @endif
        </span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" style="background: #ffffff; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
        
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button" style="color: #64748b; padding: 15px; float: left;">
            <span class="sr-only">Toggle navigation</span>
            <i class="fa fa-bars" style="font-size: 18px;"></i>
        </a>

        <!-- Modern Search Bar (Hireism style) -->
        <div class="navbar-search hidden-xs" style="float: left; padding: 7px 15px;">
            <div style="position: relative; display: flex; align-items: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 50px; padding: 8px 20px; width: 320px; transition: all 0.3s;" onmouseover="this.style.borderColor='#cbd5e1'" onmouseout="this.style.borderColor='#e2e8f0'">
                    <input type="text" placeholder="Search something..." style="border: none; background: transparent; width: 100%; outline: none; font-size: 14px; color: #334155;">
                    <i class="fa fa-search" style="color: #94a3b8; margin-left: 10px; font-size: 14px;"></i>
                </div>
            </div>

        <div class="navbar-custom-menu" style="display: flex; align-items: center; padding-right: 20px;">
            <ul class="nav navbar-nav" style="display: flex; align-items: center; gap: 15px;">
                
                <!-- Settings Icon -->
                <li>
                    <a href="#" data-toggle="control-sidebar" style="color: #64748b; font-size: 18px; padding: 10px;">
                        <i class="fa fa-cog"></i>
                    </a>
                </li>

                <!-- Notifications -->
                <li class="dropdown messages-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color: #64748b; font-size: 18px; padding: 10px; position: relative;">
                        <i class="fa fa-bell-o"></i>
                        <span class="label" style="position: absolute; top: 8px; right: 8px; width: 8px; height: 8px; background: #f97316; border-radius: 50%; padding: 0;"></span> 
                    </a>
                    <ul class="dropdown-menu" style="border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 1px solid #f1f5f9;">
                        <li class="header notificaton_header" style="font-weight: 600; color: #334155;">You have 0 recent notifications</li>
                        <li>
                            <ul class="menu notification_top"></ul>
                        </li>
                        <li class="footer"><a href="{{route('user.notification_unread')}}" style="color: #3b82f6;">See All Notifications</a></li>
                    </ul>
                </li>                                                  
                
                @if($show_language)
                <li class="dropdown lang-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="padding: 10px;">
                        <img class="language-img" src="{{ asset('images/lang/'.$locale.'.png') }}" style="width: 20px; border-radius: 50%;">
                    </a>
                    <ul class="dropdown-menu" style="border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 1px solid #f1f5f9;">
                        <li class="header" style="font-weight: 600;"> Language</li>
                        @foreach($languages as $key => $lang)
                        <li class="language" id="bangla">
                            <a href="#">
                                <div class="pull-left">
                                    <img src="{{ asset('images/lang/'.$key.'.png') }}" style="width: 20px;">
                                </div>
                                <h4>
                                    {{$lang}} @if($locale == $key) <i class="glyphicon glyphicon-ok green pull-right"></i> @endif
                                </h4>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </li>
                @endif
                
                <!-- User Account -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="display: flex; align-items: center; gap: 12px; padding: 10px; border-left: 1px solid #f1f5f9; margin-left: 10px; padding-left: 20px;">
                        <div class="user-info-text hidden-xs" style="display: inline-flex; flex-direction: column; text-align: right;">
                            <span class="user-name" style="font-size: 14px; font-weight: 600; color: #0f172a; line-height: 1.2;">{{auth()->user()->name}}</span>
                            <span class="user-role" style="font-size: 11px; color: #94a3b8; font-weight: 500;">View profile</span>
                        </div>
                        <img src="{{ asset('images/avatar.jpg') }}" class="user-image img-circle" alt="User Image" style="width: 40px; height: 40px; border: 2px solid #e2e8f0; padding: 2px;">
                    </a>

                    <ul class="dropdown-menu" style="border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 1px solid #f1f5f9;">
                        <li class="user-body" style="padding: 15px;">
                            <div class="col-xs-6 text-center">
                                <a href="{{ URL::route('profile') }}" style="color: #64748b;">
                                    <div style="font-size: 18px; margin-bottom: 5px; color: #3b82f6;"><i class="fa fa-user"></i></div>
                                    Profile
                                </a>
                            </div>
                            <div class="col-xs-6 text-center password">
                                <a href="{{ URL::route('change_password') }}" style="color: #64748b;">
                                    <div style="font-size: 18px; margin-bottom: 5px; color: #3b82f6;"><i class="fa fa-lock"></i></div>
                                   Password
                                </a>
                            </div>
                        </li>
                        <li class="user-footer" style="background: #f8fafc; border-top: 1px solid #f1f5f9; border-radius: 0 0 12px 12px;">
                            <div class="col-xs-6 text-center">
                                <a href="{{ URL::route('logout') }}" style="color: #ef4444; font-weight: 600;">
                                    <div style="font-size: 18px; margin-bottom: 5px;"><i class="fa fa-power-off"></i></div>
                                    Log out
                                </a>
                            </div>
                            <div class="col-xs-6 text-center password">
                                <a href="{{ URL::route('lockscreen') }}" style="color: #64748b;">
                                    <div style="font-size: 18px; margin-bottom: 5px;"><i class="fa fa-eye-slash"></i></div>
                                    Lock Screen
                                </a>
                            </div>
                        </li>
                    </ul>
                </li>         
            </ul>
        </div>
    </nav>
</header>
<style>
/* Remove default hover backgrounds for a cleaner look */
.main-header .navbar .nav > li > a:hover, 
.main-header .navbar .nav > li > a:active, 
.main-header .navbar .nav > li > a:focus {
    background: transparent !important;
}
</style>