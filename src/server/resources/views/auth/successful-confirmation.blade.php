@extends('app')

@section('content')
<div>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-6 col-md-offset-3 text-center">
					<h1>Activation</h1>
					@if($success)
						<div class="alert alert-success mmt" role="alert">
							Account activated successfully
						</div>
					@else
						<div class="alert alert-danger mmt" role="alert">
							Can't confirm that account. Please make sure you entered the right url.
						</div>
					@endif
					<a class="btn btn-primary" href="/auth/login">login</a>
				</div>
	</div>
</div>
@endsection