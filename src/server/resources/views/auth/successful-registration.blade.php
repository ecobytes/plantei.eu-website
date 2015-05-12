@extends('app')

@section('content')
<div>
		<div class="container-fluid">
			<div class="row">
				@if(!$willSendMail)
					<div class="col-md-6 col-md-offset-3 text-center">
						<div class="alert alert-danger mmt" role="alert">
							Email was not sent since email is not configured correctly.<br/> This might be the desired behaviour during development.<br/> Configuration is stored at "src/.env".  
						</div>
					</div>
				@endif
				<div class="col-md-6 col-md-offset-3 text-center">
					<h1>Registration Successful</h1>
					<div class="alert alert-warning mmt" role="alert">
						Please check your e-mail to activate your account
					</div>
					<a class="btn btn-primary" href="/">home</a>
				</div>
	</div>
</div>
@endsection