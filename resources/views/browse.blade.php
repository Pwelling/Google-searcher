@extends('layouts.app')

@section('content')
<div id="">
	<select name="resultSelect" onchange="showResults(this.value,0);">
		<option value="">Please choose...</option>
		@foreach($results AS $result)
		<option value="{{$result->key}}" @if($key==$result->key) selected="selected" @endif>{{$result->key}} ({{$result->key_count}})</option>
		@endforeach
	</select>
</div>
{{$paginate}}
<div id="resultsContainer">
	@if($items !== false)
		<h2>Resultaten voor {{$key}}</h2>
		@foreach($items AS $item)
			<div class="item">
				<a href="http://www.google.nl{{ $item->url}}">{{$item->result}}</a>
			</div>
		@endforeach
	@endif
</div>
{{$paginate}}
@endsection

@section('footerscripts')
<script type="text/javascript">
	function showResults(val,page) {
		window.location = '/browse/' + val;
	}
</script>
@endsection
