<!DOCTYPE html>
<html lang="ja">
<head>
  <title><?php echo switch_title(); ?></title>
  <meta name="description" content="<?php echo switch_desc(); ?>">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="format-detection" content="telephone=no"/>
<!--  <link rel="shortcut icon" href="--><?php //echo get_template_directory_uri() . '/assets/images/favicon.ico' ?><!--">-->
  <?php wp_head(); ?>
</head>
<body data-barba="wrapper">
<header class="header">
  <div class="header_inner">
    <p class="header_logo">
      <a href="<?php echo esc_url(home_url('/')); ?>">logo</a>
    </p>
    <button class="header_button" type="button">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>
