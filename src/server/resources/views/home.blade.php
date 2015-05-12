@extends('app')

@section('content')
<div>
		<div class="container-fluid">
		<div class="col-md-12">
			<caravel-messages></caravel-messages>
		</div>
		<div class="col-md-4">
			<caravel-issues></caravel-issues>
		</div>

		<div class="col-md-4">
			<div class="panel panel-default">
			  <div class="panel-heading">Project Issues</div>
  				<div class="panel-body">
    				Specific project issues go here
  				</div>
			</div>
		</div>
	</div>
</div>
@endsection
