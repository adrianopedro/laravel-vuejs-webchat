@extends('layouts.master')

@section('content')

<div id="chat">
	<div v-for="chatbox in chatboxes" v-bind:id="'chat_'+chatbox.id" class="chat chat--message-arrow chat--skin-light" v-if="!hidden['chat_'+chatbox.id]" v-on:click="received(chatbox.id)">
		<div class="chat__head">
			<div class="chat__head-title"><span v-for="user in chatbox.usersunique" v-html="user.initials"></div>
			<div class="chat__head-tools">
				<a href="#" v-on:click="minimize(chatbox.id)"><i class="fas fa-window-minimize"></i></a>
				<a href="#" v-on:click="hide(chatbox.id)"><i class="fas fa-window-close"></i></a>					
			</div>
		</div>
		<div class="chat__messages" v-if="!minimized['chat_'+chatbox.id]">
			<div v-for="message in chatbox.messagelist">					
				<div class="chat__wrapper">
					<div class="chat__message" v-bind:class="{ 'chat__message--in' : message.inout == 'in', 'chat__message--out' : message.inout == 'out' }">
						<div class="chat__message-body">
							<div class="chat__message-arrow"></div>
							<div class="chat__message-content">
								<div class="chat__message-username" v-html='message.user.name'></div>
								<div class="chat__message-text" v-html="message.message"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="chat__datetime" v-html="message.created_at"></div>
			</div>
			<div class="ps__rail-x" style="left: 0px; bottom: 0px;">
				<div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
			</div>
			<div class="ps__rail-y" style="top: 0px; height: 306px; right: 4px;">
				<div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 95px;"></div>
			</div>
		</div>
		<div class="chat__seperator" v-if="!minimized['chat_'+chatbox.id]"></div>
		<div class="chat__form" v-if="!minimized['chat_'+chatbox.id]">
			<div class="chat__form-controls">
				<input type="text" v-model="chatinput[chatbox.id]" placeholder="Type here..." class="chat__form-input" v-on:keyup.enter="sendMessage(chatbox.id)">
			</div>
			<div class="chat__form-tools">
				<a href="" class="chat__form-attachment">
					<i class="la la-paperclip"></i>
				</a>
			</div>
		</div>
	</div>
</div>

@endsection

@section('css')
	<link src="{{ asset("/vendor/chat/css/vchat.css") }}" rel="stylesheet" type="text/css" />
@endsection

@section('scripts')
	<script src="{{ asset("/vendor/chat/js/vchat.js") }}" type="text/javascript"></script>
@endsection


