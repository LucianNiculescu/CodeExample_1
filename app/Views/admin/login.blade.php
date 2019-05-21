@extends('admin.guest_master')

@section('guest-content')

@include('admin.background')

	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-11 col-md-10 col-lg-offset-1 col-md-offset-1 row-full-height">
				<div class="col-lg-7 col-md-6 hidden-sm hidden-xs padding-10 margin-right-30 dark-bg col-full-height">
					<div class="row login-marketing">
						<div class="hidden-md col-lg-4">
							<img src="/admin/templates/system/images/front.png">
						</div>
						<div class="col-md-12 col-lg-8">
							<h2>Cloud-managed WiFi</h2>
							<p class="white">MyAirangel enables easy internet access for your guests, real time customer engagement, a suite of marketing tools, customer analytics, and detailed network performance reports. In addition, the MyAirangel tools allow you to manage your entire wireless estate across multiple sites and hardware types, ensuring you have complete control of your network.</p>
							<p class="white">Please visit www.airangel.com to learn more about Airangel and our cloud-managed WiFi solutions, or login to manage your venue’s WiFi.</p>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-md-5 col-sm-6 col-xs-12 col-sm-offset-3 col-md-offset-0 padding-10 text-center dark-bg col-full-height">
					<div class="">
						<div class="panel-body">
							<div class="brand">
								<img src="/admin/templates/{{$template}}/img/logo.png">
								<h2 class="brand-title font-size-18 align-center">{{trans('admin.login')}}</h2>
							</div>

							<form action="/login" method="post" class="login validate-me">

								{{ csrf_field() }}

								<div class="form-group form-material floating">
									<input type="email" class="form-control" name="username" required aria-required="true" value="{{Input::old('username')}}" />
									<label class="floating-label">{{trans('admin.email')}}</label>
								</div>
								<div class="form-group form-material floating">
									<input type="password" class="form-control empty" name="password" required aria-required="true" value="{{Input::old('password')}}" />
									<label class="floating-label">{{trans('admin.password')}}</label>
								</div>



								<div class="form-group clearfix">
									<a class="pull-left margin-top-20 btn btn-dark white" href="/password/forgot">{{trans('admin.email-password-reset-form-title')}}</a>
									<button type="submit" class="btn btn-primary pull-right margin-top-20">{{trans('admin.login')}}</button>
								</div>

							</form>
							{{--<p>Still no account? Please go to <a href="register-v3.html">Sign up</a></p>--}}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection

