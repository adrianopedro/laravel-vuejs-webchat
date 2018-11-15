<div class="m-messenger m-messenger--message-arrow m-messenger--skin-light">
	<div class="m-messenger__messages m-scrollable m-scroller ps ps--active-y" style="height: 306px; overflow: hidden;">
		<div v-for="item in items">
			@{{ item.m }}
		</div>
		{{-- @foreach($messages as $message)
			@include("chat.message", $message)
			<div class="m-messenger__datetime">{{ $message->sent }}</div>
		@endforeach --}}
		
		<div class="ps__rail-x" style="left: 0px; bottom: 0px;">
			<div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
		</div>
		<div class="ps__rail-y" style="top: 0px; height: 306px; right: 4px;">
			<div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 95px;"></div>
		</div>
	</div>
	
	<div class="m-messenger__seperator"></div>

	<div class="m-messenger__form">
		<div class="m-messenger__form-controls">
			<input type="text" name="" placeholder="Type here..." class="m-messenger__form-input">
		</div>
		<div class="m-messenger__form-tools">
			<a href="" class="m-messenger__form-attachment">
				<i class="la la-paperclip"></i>
			</a>
		</div>
	</div>
</div>