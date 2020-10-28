<?php

/**
 * Plugin Name: ColibriWP Fix Options
 *
Использовать:

Поместите этот файл в wp-content/plugins/каталог;
На панели инструментов Wordpress перейдите на страницу расширений;
Включите расширение «Параметры исправления ColibriWP».
Это устранит проблему сериализации.

Если ситуация не исправилась, запускаем с гет параметром ?colibriwp-try-fix-serialization=true


 */


function extendthemes_fix_serialized($string)
{
    // securities
    if (!preg_match('/^[aOs]:/', $string)) return $string;
    if (@unserialize($string) !== false) return $string;
    $string = preg_replace("%\n%", "", $string);
    // doublequote exploding
    $data = preg_replace('%";%', "µµµ", $string);
    $tab = explode("µµµ", $data);
    $new_data = '';
    foreach ($tab as $line) {
        $new_data .= preg_replace_callback('%\bs:(\d+):"(.*)%', 'fix_str_length', $line);
    }
    return $new_data;
}

add_action('init', function () {
    if (isset($_REQUEST['colibriwp-try-fix-serialization'])) {
        $x = get_option( 'extend_builder_theme', array());
        if (is_string($x)) {
            $x = preg_replace('#\r?\n#', "  ", $x);
            $x = extendthemes_fix_serialized($x);

            $y = unserialize($x);

            if(is_array($y)){
                update_option('extend_builder_theme', $y);
                var_dump($y);
            } else {
                wp_die( 'Au au' );
            }
            
        }
    }
});
