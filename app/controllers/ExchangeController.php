<?php
namespace app\controllers;

use app\models\Echange;
use app\models\EchangeFille;
use app\models\Objet;
class ExchangeController
{
    public static function create_exchange($id_user_1, $id_user_2)
    {
        $echange = new Echange(null, $id_user_1, $id_user_2, null, null, 'en attente');
        return $echange->create();
    }

    public static function get_exchange_by_id($id_echange)
    {
        return Echange::get_by_id($id_echange);
    }

    public static function get_user_exchanges($id_user)
    {
        return Echange::get_by_user($id_user);
    }

    public static function get_pending_received_exchanges($id_user)
    {
        return Echange::get_pending_received($id_user);
    }

    public static function get_pending_sent_exchanges($id_user)
    {
        return Echange::get_pending_sent($id_user);
    }

    public static function add_object_to_exchange($id_echange, $id_objet, $id_proprietaire, $quantite = 1)
    {
        $echange_fille = new EchangeFille(null, $id_echange, $id_objet, $quantite, $id_proprietaire);
        return $echange_fille->create();
    }

    public static function get_exchange_objects($id_echange)
    {
        return EchangeFille::get_by_echange($id_echange);
    }

    public static function accept_exchange($id_echange, $id_user_1, $id_user_2, $id_objet_1, $id_objet_2)
    {
        try {
            $echange = new Echange($id_echange);
            $result = $echange->update_status('accepte');
            Objet::switch_proprietaires_objets($id_user_1, $id_user_2, $id_objet_1, $id_objet_2);
            error_log('[INFO] accept_exchange: Exchange ' . $id_echange . ' status updated. Result: ' . ($result ? 'true' : 'false'));
            return $result;
        } catch (\Exception $e) {
            error_log('[ERROR] accept_exchange: ' . $e->getMessage());
            return false;
        }
    }

    public static function refuse_exchange($id_echange)
    {
        try {
            $echange = new Echange($id_echange);
            $echange->update_status('refuse');
            EchangeFille::delete_by_echange($id_echange);
            $result = $echange->delete();
            error_log('[INFO] refuse_exchange: Exchange ' . $id_echange . ' refused and deleted. Result: ' . ($result ? 'true' : 'false'));
            return $result;
        } catch (\Exception $e) {
            error_log('[ERROR] refuse_exchange: ' . $e->getMessage());
            return false;
        }
    }

    public static function cancel_exchange($id_echange)
    {
        try {
            $echange = new Echange($id_echange);
            EchangeFille::delete_by_echange($id_echange);
            $result = $echange->delete();
            error_log('[INFO] cancel_exchange: Exchange ' . $id_echange . ' cancelled and deleted. Result: ' . ($result ? 'true' : 'false'));
            return $result;
        } catch (\Exception $e) {
            error_log('[ERROR] cancel_exchange: ' . $e->getMessage());
            return false;
        }
    }
}
?>
