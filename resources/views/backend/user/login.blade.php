@extends('backend.layouts.front_master')

@section('pageTitle') Login @endsection
@section('bodyCssClass') login-page @endsection
@section('pageContent')

<div class="login-wrapper">
    <div class="login-left-panel">
        <div class="login-left-content">
            <div class="login-illustration">
                <h1>Welcome Back</h1>
                <p>Sign in to continue to your dashboard and manage everything in one place.</p>
                <svg width="250" height="200" viewBox="0 0 250 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="125" cy="100" r="80" fill="rgba(255,255,255,0.1)" />
                    <rect x="75" y="70" width="100" height="60" rx="8" fill="#ffffff" opacity="0.9" />
                    <circle cx="100" cy="100" r="10" fill="#3b82f6" />
                    <rect x="120" y="95" width="40" height="4" rx="2" fill="#cbd5e1" />
                    <rect x="120" y="105" width="20" height="4" rx="2" fill="#cbd5e1" />
                </svg>
            </div>
        </div>
    </div>
    
    <div class="login-right-panel">
        <div class="login-box">
            @if (Session::has('success') || Session::has('error') || Session::has('warning'))
                <div class="alert @if (Session::has('success')) alert-success @elseif(Session::has('error')) alert-danger @else alert-warning @endif alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    @if (Session::has('success'))
                        <h5><i class="icon fa fa-check" aria-hidden="true"></i> {{ Session::get('success') }}</h5>
                    @elseif(Session::has('error'))
                        <h5><i class="icon fa fa-ban" aria-hidden="true"></i> {{ Session::get('error') }}</h5>
                    @else
                        <h5><i class="icon fa fa-warning" aria-hidden="true"></i> {{ Session::get('warning') }}</h5>
                    @endif
                </div>
            @endif

            <div class="login-box-body">
                <div class="login-header">
                    <h2>Sign In</h2>
                    <p class="login-box-msg text-muted">Use your username and password to Login</p>
                </div>
                
                <form novalidate id="loginForm" action="{{ URL::Route('login') }}" method="post">
                    @csrf
                    <div class="form-group has-feedback @error('username') has-error @enderror">
                        <label for="usernameInput">Username</label>
                        <div class="input-group-wrapper">
                            <input autofocus type="text" id="usernameInput" class="form-control" name="username" value="{{ old('username') }}" placeholder="Enter your username" required minlength="5" maxlength="255">
                        </div>
                        @if($errors->has('username'))
                            <span class="text-danger-msg">{{ $errors->first('username') }}</span>
                        @endif
                    </div>

                    <div class="form-group has-feedback @error('password') has-error @enderror">
                        <label for="passwordInput">Password</label>
                        <div class="input-group-wrapper">
                            <input type="password" id="passwordInput" class="form-control" name="password" placeholder="Enter your password" required minlength="6" maxlength="255">
                        </div>
                        @if($errors->has('password'))
                            <span class="text-danger-msg">{{ $errors->first('password') }}</span>
                        @endif
                    </div>

                    <div class="login-actions">
                        <div class="checkbox icheck">
                            <label>
                                <input name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }}>
                                <span>Remember Me</span>
                            </label>
                        </div>
                        <a href="{{ URL::Route('forgot') }}" class="forgot-link">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" class="btn btn-lg btn-block btn-flat login-button">SIGN IN</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('extraScript')
    <script type="text/javascript">
        $(document).ready(function () {
            Login.init();
        });
    </script>
@endsection