<template>
<div id="direct-message-box" @focus="clearTitleNotification()" v-if="boxActive">
  <div class="fixed top-0 z-40 inset-x-0 mx-auto sm:max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mt-16 md:mt-24 px-4">
    <div class="bg-white text-lg rounded shadow-lg">
      <div class="bg-white text-2xl text-gray-600 px-8 py-4 rounded-t border-b border-gray-100 flex items-center justify-between">
        <div></div>
        <div>
          Mensajes
        </div>
        <div @click="hideMessageBox()" class="self-end cursor-pointer">
          <font-awesome-icon :icon="faTimes"
            class="text-sm">
          </font-awesome-icon>
        </div>
      </div>
      <div>
        <div class="flex items-center overflow-x-auto p-2 relative shadow-md z-10">
          <baseButton @click="setCurrentComponent('directory')" btnType="primary">Nuevo Mensaje</baseButton>
        </div>
      </div>
      
      <div class="container mx-auto flex flex-wrap">
        <!-- contact list -->
        <aside class="w-full md:w-1/4 flex flex-col border-r shadow relative">
          <div class="flex-col overflow-hidden overflow-y-auto h-128" id="contact-list">
                <div @click="selectUserMessage(convo.receiver, index)"
                  v-for="(convo, index) in convos"
                  class="flex flex-row relative border-b text-gray-800 p-2 cursor-pointer hover:bg-gray-100"
                  :class="[(convo.receiver.id === selectedUser.id) ? 'bg-gray-200' : '']"
                  :key="convo.id"
                >
                
                   <img class="w-10 h-10 rounded-full border-2 text-white shadow"
                    :title="convo.receiver.name"
                    :src="generateUrl(convo.receiver.avatar)"
                    :class="[(convo.id === selectedUser.id) ? 'border-indigo-500' : 'border-white', convo.unreadMessages ? 'jelly border-indigo-500' : '']"
                    >
                 <div :class="[convo.online ? 'bg-indigo-500' : 'bg-gray-500']" :title="[convo.online ? 'online' : 'offline']" class="absolute w-4 h-4 rounded-full border-2 border-white left-1 bottom-1"></div>

                    <div class="flex flex-col w-4/5">
                      <p class="mt-2 ml-3 text-base w-100 overflow-hidden whitespace-no-wrap"
                      :class="[convo.unreadMessages ? 'font-bold' : '']"
                      > {{ convo.receiver.name }}
                        <span v-if="convo.unreadMessages"> ({{ convo.unreadMessages }}) </span>
                      </p>
                      <p class="ml-3 text-sm w-100 overflow-hidden whitespace-no-wrap opacity-50">{{ convo.body }} </p>
                    </div>
                 </div>
          </div>
        </aside>

        <section class="w-full md:w-3/4 flex flex-col h-60-vh">
          <div class="flex-grow overflow-y-auto">
              <div id="message-box"  v-if="selectedUser.id" class="w-full min-h-full bg-blue-100">
                <div v-if="messages.length < 1" class="w-full h-full">
                  <div v-if="!loading" class="flex flex-col items-center justify-center">
                    <div class="text-gray-600 text-lg text-center py-16">
                      No hay mensajes aun! Dile "Hola" a {{ selectedUser.name }}...
                    </div>
                    <img src="/image/dm.svg" alt="direct message" class="w-96">
                  </div>
                </div>
                <div v-if="currentPage < lastPage">
                  <a class="cursor-pointer flex flex-col items-center justify-center hover:text-indigo-600 hover:bg-white px-4 py-2" @click="loadPrevMessage">Cargar mensajes anteriores</a>
                </div>
                <message v-for="(message, index) in messages" :key="message.id" :message="message" :user="authUser" :index="parseInt(index)" @deleted="deleteMessage" @edit="editMessage" :last="messages.length === (index + 1)" :direct="true"></message>
              </div>
              <div v-else class="flex flex-col items-center justify-center">
                <div class="text-gray-600 text-lg text-center py-16">
                  Elije una conversación.
                </div>
                <img src="/image/select.svg" alt="direct message" class="w-64">
              </div>
          </div>
          <!-- message box -->
          <div class="relative text-center p-4 border-t">
            <div v-if="isDisabled" class="absolute left-0 top-0 rounded-b w-full h-full bg-gray-800 opacity-25"></div>
            <div class="flex justify-between items-center">
              <textarea
                class="static textarea resize-none rounded w-full px-4 pt-2 text-gray-800 bg-white"
                id="send-direct-message"
                :style="{height: messageTextareaHeight}"
                ref="messageTextarea"
                :placeholder="'write your message here' | localize"
                rows=1
                v-model="message"
                :disabled="isDisabled"
                @keydown.enter.prevent="sendMessage($event)"></textarea>
              <div @click="sendMessage" class="bg-indigo-500 rounded-full h-10 w-10 flex justify-center items-center cursor-pointer">
                <font-awesome-icon :icon="faPaperPlane"
                  class="items-center text-white text-sm mr-1">
                </font-awesome-icon>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>

  <div @click="hideMessageBox()" class="h-screen w-screen fixed inset-0 bg-gray-900 opacity-25 z-30"></div>
</div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import message from './message'
import { faPaperPlane, faTimes } from '@fortawesome/free-solid-svg-icons'

export default {
  components: {message},

  data: () => ({
    authUser: user,
    isDisabled: true,
    message: '',
    messages: [],
    nextPageUrl: null,
    currentPage: 0,
    lastPage: 0,
    messageBoxShown: false,
    messageTextareaHeight: 'auto',
    title: '',
    unreadMessage: 0,
    users: [],
    selectedUser: {},
    editing: {},
    faPaperPlane,
    faTimes,
    convos:[],
  }),

  created () {
    this.title = document.title
    this.listen()
    document.addEventListener('visibilitychange', this.clearTitleNotification)

    this.getConvos();
    EventBus.$on('openChatMsg', this.handleOpenChat);
    
  },

  updated () {
    if (this.messageBoxShown) {
      this.scrollToBottom()
    }
  },

  beforeDestroy () {
    document.removeEventListener('visibilitychange', this.clearTitleNotification)
  },

  watch: {
    message (newVal) {
      // increase the height of textarea based on text present there
      this.messageTextareaHeight = newVal ? `${this.$refs.messageTextarea.scrollHeight}px` : 'auto'
    }
  },

  computed: {
    ...mapState({
      currentComponent: state => state.dropdown.currentComponent
    }),
    boxActive: function () {
      var active = this.currentComponent === 'direct-message-box'
      if (!active) {
        this.selectedUser = {};
      }
      return active;
    }
  },

  methods: {
    ...mapActions([
      'closeComponent',
      'showNotification',
      'toggleLoading',
      'setCurrentComponent'
    ]),
    getConvos() {
      axios.get('/direct-messages/convos')
      .then((response) => {
        console.log('resp.convos',response.data.convos);
        this.setConvos(response.data.convos);
        this.getUnread();
      })
      .catch((error) => {
        console.log(error)
      })
    },
    getUnread() {
      axios.get('/unread-direct-messages/users')
      .then((response) => {
        this.users = response.data.users
        this.users.forEach(user => {
          if(user.unread_messages_for_auth_user_count) {
            this.getConvoById(user.id).unreadMessages = user.unread_messages_for_auth_user_count; 
          }
        });
        
      })
      .catch((error) => {
        console.log(error)
      })
    },
    setConvos(convos) {
      //TODO: Move this logic to the model repository
      let senders = this.objectToArray(convos[0]);
      let senderIds = senders.map(a => a.receiver_id);
      let uniqueReceivers = [];
      
      this.objectToArray(convos[1]).forEach(receiever => {
        if(!senderIds.includes(receiever.sender_id)) {
          receiever.receiver = receiever.user;
          
          uniqueReceivers.push(receiever);
        } else {
          var index = this.getConvoIndex(receiever.sender_id,senders) 
          if (new Date (senders[index].created_at) < new Date (receiever.created_at)) {
            senders[index].body = receiever.body;
            senders[index].created_at = receiever.created_at
          }
        }
      });
      this.convos = [
        ...senders,
        ...uniqueReceivers
      ];
      this.sortConvos()
    },
    objectToArray (obj) {
        let properties = Object.keys(obj),
            finalArr = [];
        for (let i of properties) {
            obj[i].online = null;
            finalArr.push(obj[i]);
        };
        return finalArr;
    },
    getConvoIndex(id,arr) {
      return arr ? arr.findIndex(x => x.receiver.id === id) : this.convos.findIndex(x => x.receiver.id === id);
    },
    getConvoById(userId) {
      return this.convos[this.getConvoIndex(userId)] || {};
    },
    bumpConvo(receiverId, lastMessage, unreadCount) {
      var index = this.getConvoIndex(receiverId);
      var convoUp = this.convos[index];

      if(convoUp) {
        convoUp.body = lastMessage ? lastMessage : convoUp.body;
        convoUp.unreadMessages = unreadCount ? convoUp.unreadMessages += unreadCount : convoUp.unreadMessages;
        this.convos.splice(0, 0, ...this.convos.splice(index, 1));
      } else {
        this.convos.unshift({
          body: lastMessage || '',
          receiver: this.users[this.users.findIndex(x => x.id === receiverId)],
          unreadMessages: unreadCount || 0,
          online: unreadCount ? true : false
        })
      }   
      if (document.getElementById("contact-list")) {
        var contactsContainer = this.$el.querySelector('#contact-list')
        contactsContainer = contactsContainer.firstElementChild
        contactsContainer.scrollIntoView();
      }
    },
    sortConvos() {
      this.convos.sort((a, b) => {return new Date(b.created_at) - new Date(a.created_at);})
    },
    scrollToBottom () {
      this.$nextTick(() => {
        if (document.getElementById("message-box")) {
          var messagesContainer = this.$el.querySelector('#message-box')
          messagesContainer = messagesContainer.lastElementChild
          messagesContainer.scrollIntoView()
        }
      })
    },
    scrollToNthChild (nthChild) {
      this.$nextTick(() => {
        if (document.getElementById("message-box")) {
          var messagesContainer = this.$el.querySelector('#message-box')
          messagesContainer = messagesContainer.children[nthChild]
          messagesContainer.scrollIntoView();
        }
      })
    },
    hideMessageBox () {
      this.closeComponent()
    },
    sendMessage (e) {
      if (!this.selectedUser.id) {
        return false
      }
      if (e.shiftKey) {
        this.message = this.message + '\n'
      } else if (this.message.length > 0) {
        var msg = this.message
        this.message = ''
        if(this.editing.hasOwnProperty('messageIndex')) {
          this.sendEditedMessage(msg)
        } else {
          this.sendNewMessage(msg)
        }
      }
    },
    sendEditedMessage (msg) {
      axios.put('/direct-messages/' + this.editing.message.id, {
        body: msg,
        receiver_id: this.selectedUser.id
      })
        .then((response) => {
          if (response.data.status === 'success') {
            response.data.message.user = user
            this.messages.splice(this.editing.messageIndex, 1, response.data.message)
            this.editing = {}
          }
        })
        .catch((error) => {
          this.showNotification({type: error.response.data.status, message: error.response.data.message})
        })
    },
    sendNewMessage (msg) {
      axios.post('/direct-messages', {
        body: msg,
        receiver_id: this.selectedUser.id
      })
        .then((response) => {
          if (response.data.status === 'success') {
            response.data.message.user = user
            this.messages.push(response.data.message)
            this.scrollToBottom()
            this.bumpConvo(this.selectedUser.id, msg);
          }
        })
        .catch((error) => {
          this.showNotification({type: error.response.data.status, message: error.response.data.message})
        })
    },
    selectUserMessage (user, index) {
      this.toggleLoading(true)
      this.selectedUser = user
      this.isDisabled = false
      axios.get('/direct-messages', {
        params: {
          receiver_id: user.id
        }
      })
        .then((response) => {
          this.messages = response.data.messages.data.reverse()
          this.nextPageUrl = response.data.messages.next_page_url
          this.currentPage = response.data.messages.current_page
          this.lastPage = response.data.messages.last_page
          this.scrollToBottom()
          this.toggleLoading(false)
        })
        .catch((error) => {
          this.toggleLoading(false)
          console.log(error)
        })
      axios.put('/unread-direct-messages/' + user.id)
        .then((response) => {
          if(this.convos[index]) {
            this.convos[index].unreadMessages = 0
          }
          this.toggleLoading(false)
        })
        .catch((error) => {
          this.toggleLoading(false)
          console.log(error)
        })
    },
    paginationMessage (user) {
      this.toggleLoading(true)
      this.isDisabled = false
      axios.get(this.nextPageUrl, {
        params: {
          receiver_id: user.id
        }
      })
        .then((response) => {
          this.concatAllMessages(response)
          this.nextPageUrl = response.data.messages.next_page_url
          this.currentPage = response.data.messages.current_page
          this.toggleLoading(false)
        })
        .catch((error) => {
          this.toggleLoading(false)
          console.log(error)
        })
    },
    loadPrevMessage (){
      this.paginationMessage(this.selectedUser)
    },
    concatAllMessages (response){
      let currentMessageArray = JSON.parse(JSON.stringify(response.data.messages.data.reverse()))
      let previousMessagesArray = JSON.parse(JSON.stringify(this.messages))
      
      let i = 0
      const result = [];

      currentMessageArray.forEach(arrayObject => result[i++] = arrayObject)

      previousMessagesArray.forEach(arrayObject => result[i++] = arrayObject)
      
      this.messages = result
      i = 0
  
      let length = Object.keys(response.data.messages.data).length
      this.scrollToNthChild(length)

      
    },
    deleteMessage (index) {
      this.messages.splice(index, 1)
    },
    editMessage (index) {
      document.getElementById('send-direct-message').focus()
      this.editing = {
        message: this.messages[index],
        messageIndex: index,
      }
      this.message =  this.messages[index].body
    },
    listen () {
      Echo.join('global')
        .here(users => {
          this.convos.forEach(convo => {
            convo.online = users.includes(convo.receiver.id);
          })
        })
        .joining((userId) => {
          this.getConvoById(userId).online = true;
        })
        .leaving((userId) => {
          this.getConvoById(userId).online = false;       
        })
      Echo.private('User.' + this.authUser.id)
        .listen('DirectMessageCreated', event => {
          event.message.user = event.user
          if (!this.boxActive) {
            EventBus.$emit('new-direct-message');
          } 
          if (this.selectedUser.id === event.user.id) {
            axios.put('/unread-direct-messages/' + event.user.id)
              .catch((error) => {
                console.log(error)
              })
              this.messages.push(event.message)
              this.bumpConvo(event.user.id,event.message.body);
              this.scrollToBottom();
          } else if (this.selectedUser.id !== event.user.id) {
            this.bumpConvo(event.user.id,event.message.body, 1);
          }
          if (document.hidden) {
            this.unreadMessage += 1
            document.title = '(' + this.unreadMessage + ') ' + this.title
          }
          
        })
    },
    clearTitleNotification () {
      if (!document.hidden) {
        document.title = this.title
        this.unreadMessage = 0
      }
    },
    handleOpenChat(user) {
      this.selectUserMessage(user, this.getConvoIndex(user.id));
    }
    
  }
}
</script>

<style>
.jelly {
  animation: jelly 2s infinite;
  will-change: transform;
}

@keyframes jelly {
  0%,
  20% {
    transform: scale(0.9, 1.1);
  }
  40% {
    transform: scale(1.1, 0.9);
  }
  60% {
    transform: scale(0.95, 1.05);
  }
  80% {
    transform: scale(1, 1);
  }
  100% {
    transform: scale(1, 1);
  }
}
</style>
