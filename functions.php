<?php
// remove_action
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head','rest_output_link_wp_head');
remove_action('wp_head','feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
remove_action('wp_head', 'wp_shortlink_wp_head', 10);
remove_action('admin_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');

// remove_filter
remove_filter('term_description', 'wpautop');
remove_filter('the_content_feed', 'wp_staticize_emoji');
remove_filter('comment_text_rss', 'wp_staticize_emoji');
remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

// add_filter
add_filter('tiny_mce_plugins', 'disable_emojis_tinymce');
add_filter('show_admin_bar', '__return_false');

// dns-prefetchの削除
add_filter('wp_resource_hints', function($hints, $relation_type) {
  if ($relation_type === 'dns-prefetch') return array_diff(wp_dependencies_unique_hosts(), $hints);
  return $hints;
}, 10, 2);

// 謎のインラインスタイルの削除
add_action('widgets_init', function () {
  global $wp_widget_factory;
  remove_action('wp_head', array($wp_widget_factory -> widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
});

// wp-embed.min.js削除
add_action('wp_footer', function () {
  wp_deregister_script('wp-embed');
});

// 管理画面のメニューのカスタマイズ
add_action('admin_menu', function() {
  global $menu, $submenu;
  $menu[14] = $menu[10];
  unset($menu[10]);
  $menu[5][0] = 'ニュース';
  $submenu['edit.php'][5][0] = '投稿';
});
add_action('admin_head', function() { ?>
  <style>.dashicons-admin-post:before { content: '\f119';}</style>
<?php });

// ログイン画面のカスタマイズ
add_action('login_head', function() {
  echo '<link rel="stylesheet" href="' . get_template_directory_uri() . '/assets/css/login.css" />';
});

// 画像のtitle class width heightを削除
function remove_img_attribute($html){
  $html = preg_replace('/(width|height)="\d*"\s/', '', $html);
  $html = preg_replace('/class=[\'"]([^\'"]+)[\'"]/i', '', $html);
  $html = preg_replace('/title=[\'"]([^\'"]+)[\'"]/i', '', $html);
  return $html;
}
add_filter('image_send_to_editor', 'remove_img_attribute', 10);
add_filter('post_thumbnail_html', 'remove_img_attribute', 10);
add_filter('get_image_tag', 'remove_img_attribute', 10);

// アイキャッチ画像の有効化
add_theme_support('post-thumbnails');

// ieの判定
function ie() {
  $ua = $_SERVER['HTTP_USER_AGENT'];
  return strstr($ua, 'Trident') || strstr($ua, 'MSIE');
}

/**
 * キャッシュ対策
 * ファイルのバージョン更新
 * @param $file
 * @return string
 */
function update_version($file) {
  return date_i18n('YmdHi', filemtime(get_template_directory() . $file));
}

// js & cssの読み込み
add_action('wp_enqueue_scripts', function() {
  if (!is_admin()) {
    if (ie()) {
      // IEのみ
      wp_enqueue_script('polyfill_barba', 'https://polyfill.io/v3/polyfill.min.js?features=default%2CArray.prototype.find%2CIntersectionObserver', array(), false, true);
    }
    wp_enqueue_style('style', get_stylesheet_uri(), array(), update_version('/style.css'), 'all');
    wp_enqueue_style('app_style', get_template_directory_uri() . '/dist/assets/stylesheets/app.css', array(), update_version('/dist/assets/stylesheets/app.css'), 'all');
    wp_deregister_script('jquery');
    wp_enqueue_script('stats', 'https://cdnjs.cloudflare.com/ajax/libs/stats.js/r16/Stats.min.js', array(), false, true);
    wp_enqueue_script('barba', 'https://unpkg.com/@barba/core', array(), false, true);
    wp_enqueue_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.10.4/gsap.min.js', array(), false, true);
    wp_enqueue_script('app_js', get_template_directory_uri() . '/dist/assets/scripts/app.js', array(), update_version('/dist/assets/scripts/app.js'), true);
  }
});

// jsにcrossoriginを追加
add_filter('script_loader_tag', function ($tag, $handle) {
  if ($handle === 'polyfill_barba') return str_replace('></script>', ' crossorigin="anonymous"></script>', $tag);
  return $tag;
}, 10, 2);

// タイトルタグの設定
function switch_title() {
  $title_name = get_option('blogname');
  $title_desc = '**********';
  $pipe = ' | ';
  if (is_front_page()):
    $title = $title_name . $pipe . $title_desc;
  elseif (is_single()):
    $title = strip_tags(get_the_title()) . $pipe . $title_name;
  elseif (is_category()):
    $category = get_queried_object();
    $category_name = $category -> name;
    $title = $category_name . $pipe . $title_name;
  elseif (is_tag()):
    $tag = get_queried_object();
    $tag_name = $tag -> name;
    $title = $tag_name . $pipe . $title_name;
  elseif (is_page('en')):
    $title = 'Heart Beat Plan Co., Ltd.';
  elseif (is_page()):
    $title = get_the_title() . $pipe . $title_name;
  elseif (is_search()):
    $title = '検索内容' . $pipe . $title_name;
  elseif (is_404()):
    $title = '404' . $pipe . $title_name;
  else:
    $title = $title_name . $pipe . $title_desc;
  endif;
  return $title;
}

// descriptionの設定
function switch_desc() {
  $desc = get_option('blogdescription');
  if (is_front_page()):
    return $desc;
  elseif (is_single()):
    if (!empty(get_the_excerpt())) {
      $desc = get_the_excerpt();
    } else {
      $desc = get_option('blogdescription');
    }
  else:
    return $desc;
  endif;
  return $desc;
}

// OGの設定
function add_ogp() {
  $type = 'website';
  $url = get_permalink();
  $img = content_url() . '/themes/**********/assets/images/ogp.jpg';
  $twitter_card = 'summary_large_image';
  $twitter_site = '';
  $title_name = get_option('blogname');
  $title_desc = '/**********';
  $pipe = ' | ';
  if (is_front_page()):
    $title = $title_name . $pipe . $title_desc;
  elseif (is_single()):
    $title = strip_tags(get_the_title()) . $pipe . $title_name;
    $img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full')[0];
  elseif (is_category()):
    $category = get_queried_object();
    $category_name = $category -> name;
    $title = $category_name . $pipe . $title_name;
  elseif (is_tag()):
    $tag = get_queried_object();
    $tag_name = $tag -> name;
    $title = $tag_name . $pipe . $title_name;
  elseif (is_page()):
    $title = get_the_title() . $pipe . $title_name;
  elseif (is_search()):
    $title = '検索内容' . $pipe . $title_name;
  elseif (is_404()):
    $title = '404' . $pipe . $title_name;
  else:
    $title = $title_name . $pipe . $title_desc;
  endif;
  if (is_front_page()):
    $desc = get_option('blogdescription');
  elseif (is_single()):
    if (!empty(get_the_excerpt())) {
      $desc = get_the_excerpt();
    } else {
      $desc = get_option('blogdescription');
    }
  else:
    $desc = get_option('blogdescription');
  endif; ?>
  <meta property="og:title" content="<?php echo esc_attr($title); ?>">
  <meta property="og:type" content="<?php echo $type; ?>"/>
  <meta property="og:url" content="<?php echo esc_url($url); ?>">
  <meta property="og:image" content="<?php echo esc_url($img); ?>">
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="600" />
  <meta property="og:description" content="<?php echo esc_attr($desc); ?>">
  <meta property="og:site_name" content="<?php echo esc_attr($title); ?>">
  <meta property="og:locale" content="ja_JP">
  <meta property="fb:app_id" content="">
  <meta property="fb:admins" content="">
  <meta name="twitter:card" content="<?php echo $twitter_card; ?>">
  <meta name="twitter:site" content="<?php echo $twitter_site; ?>">
  <?php
}
add_action('wp_head', 'add_ogp');

// ajaxの設定
function add_ajax() { ?>
  <script>
    var ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
  </script>
  <?php
}
add_action('wp_head', 'add_ajax', 1);
