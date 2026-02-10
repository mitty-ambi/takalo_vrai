// Messages Component Alpine.js - Gère tout via la base de données
function messagesComponent() {
    return {
        selectedConversation: null,
        searchQuery: '',
        newMessage: '',
        sidebarVisible: true,
        showEmojiPicker: false,
        conversations: [],
        allUsers: [],
        filteredConversations: [],
        filteredUsers: [],
        currentMessages: [],
        isTyping: false,
        emojis: ['😀', '😂', '😍', '🤔', '😢', '😡', '👍', '👎', '❤️', '🔥', '✨', '🎉'],

        init() {
            // Les utilisateurs sont déjà passés en PHP
            this.filterUsers();
            // Les conversations sont déjà passées en PHP
            this.filterConversations();
        },

        filterConversations() {
            const query = this.searchQuery.toLowerCase();
            this.filteredConversations = this.conversations.filter(conv => {
                const userName = (conv.user_1 === 'currentUser' ? conv.user_2 : conv.user_1).toLowerCase();
                return userName.includes(query);
            });
        },

        filterUsers() {
            const query = this.searchQuery.toLowerCase();
            this.filteredUsers = this.allUsers.filter(user =>
                user.username.toLowerCase().includes(query) ||
                user.email.toLowerCase().includes(query)
            );
        },

        hasConversation(username) {
            return this.conversations.some(conv => 
                conv.user_1 === username || conv.user_2 === username
            );
        },

        selectConversation(conversation) {
            this.selectedConversation = conversation;
            this.currentMessages = [];
            this.loadMessages(conversation.id);
            this.sidebarVisible = false;
        },

        openConversation(username) {
            const conversation = this.conversations.find(conv => 
                conv.user_1 === username || conv.user_2 === username
            );
            if(conversation) {
                this.selectConversation(conversation);
            }
        },

        async createConversation(username) {
            try {
                const response = await fetch('/api/conversations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        user_1: 'currentUser',
                        user_2: username
                    })
                });

                if(response.ok) {
                    const data = await response.json();
                    // Ajouter la conversation à la liste
                    const newConv = {
                        id: data.id,
                        user_1: 'currentUser',
                        user_2: username,
                        created_at: new Date().toISOString()
                    };
                    this.conversations.unshift(newConv);
                    this.filterConversations();
                    this.selectConversation(newConv);
                } else {
                    alert('Erreur lors de la création de la conversation');
                }
            } catch(error) {
                console.error('Erreur:', error);
                alert('Erreur de connexion');
            }
        },

        async loadMessages(conversationId) {
            try {
                const response = await fetch(`/api/conversations/${conversationId}/messages`);
                if(response.ok) {
                    this.currentMessages = await response.json();
                } else {
                    console.error('Erreur lors du chargement des messages');
                }
            } catch(error) {
                console.error('Erreur:', error);
            }
        },

        async sendMessage() {
            if(!this.newMessage.trim() || !this.selectedConversation) return;

            try {
                const response = await fetch('/api/messages', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_message: this.selectedConversation.id,
                        user_from: 'currentUser',
                        user_to: this.selectedConversation.user_2,
                        content: this.newMessage
                    })
                });

                if(response.ok) {
                    // Recharger les messages
                    await this.loadMessages(this.selectedConversation.id);
                    this.newMessage = '';
                    this.$nextTick(() => {
                        const messagesContainer = document.getElementById('chatMessages');
                        if(messagesContainer) {
                            messagesContainer.scrollTop = messagesContainer.scrollHeight;
                        }
                    });
                } else {
                    alert('Erreur lors de l\'envoi du message');
                }
            } catch(error) {
                console.error('Erreur:', error);
                alert('Erreur de connexion');
            }
        },

        handleTyping() {
            this.isTyping = true;
            setTimeout(() => {
                this.isTyping = false;
            }, 3000);
        },

        autoResize(event) {
            event.target.style.height = 'auto';
            event.target.style.height = Math.min(event.target.scrollHeight, 120) + 'px';
        },

        toggleEmojiPicker() {
            this.showEmojiPicker = !this.showEmojiPicker;
        },

        addEmoji(emoji) {
            this.newMessage += emoji;
            this.showEmojiPicker = false;
        },

        videoCall() {
            alert('Video call with ' + this.selectedConversation?.user_2);
        },

        voiceCall() {
            alert('Voice call with ' + this.selectedConversation?.user_2);
        },

        toggleSidebar() {
            this.sidebarVisible = !this.sidebarVisible;
        },

        toggleAttachment() {
            alert('Attach file');
        },

        markAllRead() {
            // À implémenter avec la BD
        },

        muteConversation() {
            alert('Conversation muted');
        },

        archiveConversation() {
            if(this.selectedConversation) {
                // À implémenter avec la BD
                alert('Conversation archived');
                this.conversations = this.conversations.filter(c => c.id !== this.selectedConversation.id);
                this.selectedConversation = null;
                this.filterConversations();
            }
        },

        deleteConversation() {
            if(confirm('Are you sure you want to delete this conversation?')) {
                if(this.selectedConversation) {
                    // À implémenter avec la BD
                    this.conversations = this.conversations.filter(c => c.id !== this.selectedConversation.id);
                    this.selectedConversation = null;
                    this.filterConversations();
                }
            }
        },
        newConversation() {
            this.sidebarVisible = true;
            this.searchQuery = '';
            this.filterConversations();
        }
    };
}
