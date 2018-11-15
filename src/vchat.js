// import ChatBox from './components/ChatBox.vue'

// Define a new component called button-counter
// Vue.component('message', {
//   data: function () {
//     return {
//       count: 0
//     }
//   },
//   template: '<button v-on:click="count++">You clicked me {{ count }} times.</button>'
// })

var chat = new Vue({
  el: '#chat',
  components: {
    // ChatBox
  },
  data: {
    chatboxes			  : [],
    heartBeatTimeMin: 500,
    heartBeatTimeMax: 10000,
    heartBeatTimeInc: 100,
    heartBeatTime   : this.heartBeatTimeMin,
    chatinput       : [],
    minimized       : {},
    hidden          : {},
  },
  mounted () {
    this.start();
    if (localStorage.getItem('minimized')) {
      try {
        this.minimized = JSON.parse(localStorage.getItem('minimized'));
      } catch(e) {
        localStorage.removeItem('minimized');
      }
    }
    if (localStorage.getItem('hidden')) {
      try {
        this.hidden = JSON.parse(localStorage.getItem('hidden'));
      } catch(e) {
        localStorage.removeItem('hidden');
      }
    }
  },
  // watch: {
  //   minimized: function(val){
  //     console.log(val, this.minimized);
  //   }
  // },
  methods: {
    start: function() {
      setTimeout(function(){
        this.getData();
      }.bind(this), this.heartBeatTime);
    },
    getData: function(){
      axios.get('/chatHeartBeat',{
      })
      .then(response => {
        this.chatboxes  = response.data;         
        this.chatboxes.forEach(function(chatbox){   
          if(chatbox.newmessages > 0) {
            this.minimized["chat_"+chatbox.id+""] = false;
            this.hidden["chat_"+chatbox.id+""] = false;
            localStorage.removeItem('minimized');
            localStorage.removeItem('hidden');            
            setTimeout(function(){scrollToBottom(chatbox.id);},100);
            this.heartBeatTime = this.heartBeatTimeMin;
          } else {
            this.heartBeatTime = (this.heartBeatTime + this.heartBeatTimeInc) > this.heartBeatTimeMax ? this.heartBeatTimeMax : this.heartBeatTime + this.heartBeatTimeInc;
          }
        }.bind(this));
        this.start();
      })
      .catch(function(error){
        console.log(error);
      });
    },
    sendMessage: function(chatid){
      axios.post('/chatSend',{
          chatid  : chatid,
          message : this.chatinput[chatid],         
      })
      .then(response => {
        let index = findObjectByKey(this.chatboxes, 'id', chatid);
        this.chatboxes[index].messages.push(response.data);        
        this.chatinput[chatid] = '';
        setTimeout(function(){scrollToBottom(chatid);}.bind(this),100);
      })
      .catch(function(error){
        console.log(error);
      });
    },
    received: function(chatid){
      axios.post('/chatReceived',{
          chatid  : chatid,        
      })
      .then(response => {
        // 
      })
      .catch(function(error){
        console.log(error);
      });
    },
    openChatBox: function(){
      // 
    },
    minimize: function(chatid) {
      if("chat_"+chatid+"" in this.minimized){
        this.minimized["chat_"+chatid+""] = !this.minimized["chat_"+chatid+""];
      } else {
        Vue.set(this.minimized,"chat_"+chatid+"", true);        
      }
      const parsed = JSON.stringify(this.minimized);
      localStorage.setItem('minimized', parsed);
    },
    hide: function(chatid) {
      if("chat_"+chatid+"" in this.hidden){
        this.hidden["chat_"+chatid+""] = !this.hidden["chat_"+chatid+""];
      } else {
        Vue.set(this.hidden,"chat_"+chatid+"", true);        
      }
      const parsed = JSON.stringify(this.hidden);
      localStorage.setItem('hidden', parsed);
    },
    onSend: function(chatid) {
      this.sendMessage(chatid);
    },
  }
})

function scrollToBottom(chatid){
    var scrollHeight = document.querySelector('#chat_'+chatid+' .chat__messages').scrollHeight;
    document.querySelector('#chat_'+chatid+' .chat__messages').scrollTop = scrollHeight;
};

function findObjectByKey(array, key, value) {
    for (var i = 0; i < array.length; i++) {
        if (array[i][key] === value) {
            return i;
        }
    }
    return null;
}

// Vue.component('ChatBox', {
//   // ... options ...
// })