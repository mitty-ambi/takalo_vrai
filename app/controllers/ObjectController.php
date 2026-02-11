<?php
namespace app\controllers;

use app\models\Objet;
use app\models\Image_objet;

class ObjectController
{
    public static function get_user_objects($id_user)
    {
        return Objet::get_objet_by_id_user($id_user);
    }

    public static function get_other_users_objects($id_user)
    {
        $objet = new Objet();
        return $objet->get_objets_autre_user($id_user);
    }

    public static function get_object_by_id($id_objet)
    {
        return Objet::get_by_id($id_objet);
    }

    public static function create_object($nom_objet, $id_categorie, $id_user, $description, $prix_estime, $date_acquisition = null)
    {
        // Convertir en entiers pour éviter les problèmes de type avec PDO
        $id_categorie = (int) $id_categorie;
        $id_user = (int) $id_user;
        $prix_estime = (float) $prix_estime;
        
        error_log('[DEBUG ObjectController] Creating object with: nom_objet=' . $nom_objet . ', id_categorie=' . $id_categorie . ', id_user=' . $id_user . ', prix_estime=' . $prix_estime);
        $objet = new Objet(null, $nom_objet, $id_categorie, $id_user, $date_acquisition, $description, $prix_estime);
        $id_objet = $objet->insert_base();
        error_log('[DEBUG ObjectController] insert_base returned: ' . var_export($id_objet, true));
        return $id_objet;
    }

    public static function update_object($id_objet, $nom_objet, $id_categorie, $description, $prix_estime)
    {
        // Convertir en entiers pour éviter les problèmes de type avec PDO
        $id_objet = (int) $id_objet;
        $id_categorie = (int) $id_categorie;
        $prix_estime = (float) $prix_estime;
        
        $objet = new Objet($id_objet, $nom_objet, $id_categorie, null, null, $description, $prix_estime);
        return $objet->update();
    }

    public static function delete_object($id_objet)
    {
        $objet = new Objet($id_objet);
        return $objet->delete();
    }

    public static function add_image_to_object($id_objet, $url_image)
    {
        $image = new Image_objet(null, $id_objet, $url_image);
        return $image->insert_base();
    }

    public static function get_object_images($id_objet)
    {
        $image = new Image_objet();
        return $image->get_image_by_objet($id_objet);
    }

    public static function get_all_objects()
    {
        return Objet::get_all();
    }
}
?>
