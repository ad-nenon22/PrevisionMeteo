<?php

declare(strict_types=1);


/**
 * La fonction retourne la date du serveur dans sa langue si elle est en francais ou en anglais. Par défaut elle renvoie en francais
 * @param $lang est la langue par défaut
 */
function date_ex5(string $lang="fr"): string{
    $lang = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,2);
    $info = getdate();
    $date = $info['mday'];
    $day = $info ['wday'];
    $month = $info['mon'];
    $year = $info['year'];
    if ($lang=="fr") {
        $jour = array("Lundi", "Mardi", "Mercredi", "Jeudi","Vendredi", "Samedi","Dimanche");
        $mois = array("Janvier","Fevrier","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Otobre","Novembre","Decembre");
        $current_date = "$jour[$day] $date $mois[$month] $year";
        return $current_date;
    }
    if ($lang=="en") {
        $days=array("Monday","Tuesday","Wednesday","Thrusday","Friday","Saturday","Sunday");
        $months=array("January","February","March","April","May","June","July","August","September","October","Nomvember","December");
        $current_date = "$days[$day], $months[$month] $date, $year";
        return $current_date;
    }
    else {
        return "language not supported";
    }
}


/**
 * La fonction renvoie une chaîne de charactères indiquant la navigateur de l'utilisateur.
 */

function get_navigateur(): string {
    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
        $infos = $_SERVER['HTTP_USER_AGENT'];

        // Liste des navigateurs courants
        $navigateurs = ['Firefox', 'Chrome', 'Safari', 'Edge', 'Opera', 'MSIE', 'Trident'];

        foreach ($navigateurs as $nav) {
            if (stripos($infos, $nav) !== false) {
                return "Navigateur: $nav";
            }
        }

        return "Navigateur non identifié";
    }

    return "Information non disponible";
}



?>
