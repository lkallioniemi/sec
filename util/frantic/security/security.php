<?php
namespace Frantic;
class FranticSecurity {
    function __construct() {
        add_action( 'pre_get_posts', array($this,'server_info_json'), 100);
    }
    function server_info_json($query ){
       global $wp;
        if ( !is_admin() && $query->is_main_query() ) {
            if ($wp->request == sha1(get_site_url()) ){
                $response = array();
                $response['basic'] = array(
                    "name" => get_bloginfo('name'),
                    "description" => get_bloginfo('description'),
                    "charset" => get_bloginfo('charset'),
                    "version" => get_bloginfo('version'),
                    "language" => get_bloginfo('language'),
                );
                $response['environment'] = array(
                    "document_root" => $_ENV['DOCUMENT_ROOT']
                );
                $response['PHP'] = array(
                    'version' => phpversion(),
                    'extensions' => get_loaded_extensions()
                );
                $response['comments'] = comments_open();
                    
                if ( ! function_exists( 'get_plugins' ) ) {
                    require_once ABSPATH . 'wp-admin/includes/plugin.php';
                }
                $response['plugins'] = get_plugins();
                $themes = array();
                foreach(wp_get_themes() as $wptheme){
                    $theme = array();
                    $theme['Name'] = $wptheme->get('Name');
                    $theme['ThemeURI'] = $wptheme->get('ThemeURI');
                    $theme['Description'] = $wptheme->get('Description');
                    $theme['Author'] = $wptheme->get('Author');
                    $theme['AuthorURI'] = $wptheme->get('AuthorURI');
                    $theme['Version'] = $wptheme->get('Version');
                    $theme['Template'] = $wptheme->get('Template');
                    $theme['Status'] = $wptheme->get('Status');
                    $theme['Tags'] = $wptheme->get('Tags');
                    $theme['TextDomain'] = $wptheme->get('TextDomain');
                    $theme['DomainPath'] = $wptheme->get('DomainPath');
                    array_push($themes, $theme);
                }
                $response['themes'] = $themes;
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }
        }
   }
}