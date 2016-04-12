@extends('layouts.app')


@section('content')
@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif
<div id="">
	{!! Form::open(['route' => 'search']) !!}
		<table width="350" border="0" cellpadding="0" cellspacing="0" class="">
    		<tr>
    			<th>zoekterm</th>
    			<td>{!! Form::text('key') !!}</td>
    		</tr>
    	</table>
    	<br clear="all" />
    	<p>{!!  Form::submit('Zoek', array('class' => 'opslaan')) !!}</p>
	{!! Form::close() !!}
	</div>
@endsection