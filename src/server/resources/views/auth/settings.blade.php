@extends('app')

@section('content')
<div>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-6 col-md-offset-3 text-center">
					<div class="panel panel-default">
					  <div class="panel-heading">Your Settings</div>
  					<div class="panel-body">
    					<form class="form-horizontal" role="form" method="POST" action="/auth/Settings">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">

								<div class="form-group">
									<label class="col-md-4 control-label">Password</label>
									<div class="col-md-6">
										<input type="password" class="form-control" name="password">
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label">Confirm Password</label>
									<div class="col-md-6">
										<input type="password" class="form-control" name="password_confirmation">
									</div>
								</div>

								<div class="form-group">
									<div class="col-md-6 col-md-offset-4">
										<button type="submit" class="btn btn-success">
											Save
										</button>
									</div>
								</div>
							</form>
  					</div>
					</div>
				</div>
	</div>
</div>
@endsection