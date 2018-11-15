<div class="m-messenger__wrapper">

	<div class="m-messenger__message m-messenger__message--{{$in ? 'in' : 'out'}}">

		@if($in)
		<div class="m-messenger__message-pic">
			@if($pic)
			<img src="{{ $pic }}" alt="">
			@else
			<i class='fa fa-user'></i>
			@endif
		</div>
		@endif

		<div class="m-messenger__message-body">
			<div class="m-messenger__message-arrow"></div>
			<div class="m-messenger__message-content">
				@if($in)
				<div class="m-messenger__message-username">
					{{ $user->name }} {{ __('wrote') }}
				</div>
				@endif
				<div class="m-messenger__message-text">
					{{ $message }}
				</div>

			</div>
		</div>
	</div>

</div>