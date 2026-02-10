<?php
    namespace app\controllers;
    use app\models\Messages;
    use app\models\MessageFille;
    
    class MessagesController {
        /**
         * Créer une nouvelle conversation
         * @param string $user_1
         * @param string $user_2
         * @return int|false 
         */
        public function creer_conversation($user_1, $user_2) {
            if(empty($user_1) || empty($user_2)) {
                return false;
            }
            
            $message = new Messages(null, $user_1, $user_2);
            return $message->creer();
        }

        /**
         * Obtenir une conversation entre deux utilisateurs
         * @param string $user_1
         * @param string $user_2
         * @return array|false
         */
        public function obtenir_conversation($user_1, $user_2) {
            $message = new Messages();
            return $message->obtenir_conversation($user_1, $user_2);
        }

        /**
         * Obtenir toutes les conversations d'un utilisateur
         * @param string $username
         * @return array
         */
        public function obtenir_mes_conversations($username) {
            $message = new Messages();
            return $message->obtenir_conversations($username);
        }

        /**
         * Envoyer un message
         * @param int $id_message 
         * @param string $user_from 
         * @param string $user_to
         * @param string $content
         * @return int|false
         */
        public function envoyer_message($id_message, $user_from, $user_to, $content) {
            if(empty($id_message) || empty($user_from) || empty($user_to) || empty($content)) {
                return false;
            }
            
            $messageFille = new MessageFille($id_message, $user_from, $user_to, $content);
            return $messageFille->envoyer();
        }

        /**
         * Obtenir tous les messages d'une conversation
         * @param int $id_message
         * @return array
         */
        public function obtenir_messages($id_message) {
            $messageFille = new MessageFille();
            return $messageFille->obtenir_messages($id_message);
        }

        /**
         * Obtenir les derniers messages d'une conversation
         * @param int $id_message
         * @param int $limite
         * @return array
         */
        public function obtenir_derniers_messages($id_message, $limite = 50) {
            $messageFille = new MessageFille();
            return $messageFille->obtenir_derniers_messages($id_message, $limite);
        }

        /**
         * Obtenir le nombre de messages non lus
         * @param string $user_to
         * @return int
         */
        public function obtenir_non_lus($user_to) {
            $messageFille = new MessageFille();
            return $messageFille->obtenir_non_lus($user_to);
        }

        /**
         * Supprimer une conversation
         * @param int $id
         * @return bool
         */
        public function supprimer_conversation($id) {
            $message = new Messages();
            return $message->supprimer($id);
        }

        /**
         * Obtenir TOUTES les conversations
         * @return array
         */
        public function obtenir_toutes_conversations() {
            $message = new Messages();
            return $message->obtenir_toutes();
        }

        /**
         * Supprimer tous les messages d'une conversation
         * @param int $id_message
         * @return bool
         */
        public function supprimer_messages($id_message) {
            $messageFille = new MessageFille();
            return $messageFille->supprimer($id_message);
        }
    }
?>
