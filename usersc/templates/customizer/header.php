<?php
require_once($abs_us_root.$us_url_root.'users/includes/template/header1_must_include.php');
require_once($abs_us_root.$us_url_root.'usersc/templates/'.$settings->template.'/assets/fonts/glyphicons.php');

// IMPORTANTE: Headers para WhatsApp
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');


?>
<!-- META TAGS PARA THUMBNAILS WHATSAPP -->
<meta charset="UTF-8">
<meta property="og:title" content="<?php echo $titulo; ?> - Candeivid -">
<meta property="og:description" content="<?php echo $descripcion; ?>">
<meta property="og:image" content="<?php echo $imagen_url; ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:type" content="image/jpeg">
<meta property="og:url" content="<?php echo $url_actual; ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Candeivid">
<meta property="og:locale" content="es_ES">
<meta property="fb:app_id" content="1785029542388500">
<meta property="og:updated_time" content="<?php echo date('c'); ?>">
<meta name="twitter:site" content="@candeivid">
<meta name="twitter:creator" content="@rutascandeivid">
<meta property="fb:app_id" content="1785029542388500">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo $titulo; ?> - Candeivid">
<meta name="twitter:description" content="<?php echo $descripcion; ?>">
<meta name="twitter:image" content="<?php echo $imagen_url; ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?php echo $descripcion; ?>">

<!-- meta tags especificos para facebook -->
<meta charset="UTF-8">
<meta property="og:title" content="<?php echo $titulo; ?> - Candeivid">
<meta property="og:description" content="<?php echo $descripcion; ?>">
<meta property="og:image" content="<?php echo $imagen_url; ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:type" content="image/jpeg">
<meta property="og:url" content="<?php echo $url_actual; ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Candeivid">
<meta property="og:locale" content="es_ES">
<meta property="fb:app_id" content="TU_APP_ID_AQUI">
<meta property="og:updated_time" content="<?php echo date('c'); ?>">

<!-- Meta tags específicos para Facebook -->
<meta property="fb:admins" content="1335276754197042">
<meta property="og:rich_attachment" content="true">
<meta property="og:see_also" content="https://www.candeivid.com/pages/rutas.php">
<meta property="article:author" content="https://www.facebook.com/candeivid">
<meta property="article:publisher" content="https://www.facebook.com/candeivid">
<meta property="article:section" content="Deportes">
<meta property="article:tag" content="moto, rutas, GPS, aventura, turismo">

<!-- Meta tags para Instagram -->
<meta property="instagram:app_id" content="1785029542388500">
<meta name="instagram:card" content="summary_large_image">
<meta name="instagram:site" content="@candeivid">
<meta name="instagram:creator" content="@candeivid">

<!-- Twitter Card Meta Tags (mejorados) -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo $titulo; ?> - Candeivid">
<meta name="twitter:description" content="<?php echo $descripcion; ?>">
<meta name="twitter:image" content="<?php echo $imagen_url; ?>">
<meta name="twitter:image:alt" content="<?php echo $titulo; ?> - Ruta en moto">
<meta name="twitter:site" content="@candeivid">
<meta name="twitter:creator" content="@candeivid">
<meta name="twitter:domain" content="candeivid.com">

<!-- Meta tags adicionales para LinkedIn -->
<meta property="og:image:alt" content="<?php echo $titulo; ?> - Ruta en moto">
<meta name="linkedin:owner" content="Candeivid">

<!-- Meta tags para Pinterest -->
<meta name="pinterest:description" content="<?php echo $descripcion; ?>">
<meta name="pinterest:media" content="<?php echo $imagen_url; ?>">
<meta name="pinterest:url" content="<?php echo $url_actual; ?>">

<!-- Meta tags adicionales para mejor SEO social -->
<meta name="author" content="Candeivid">
<meta name="publisher" content="Candeivid">
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta name="theme-color" content="#1a5490">
<meta property="fb:app_id" content="TU_APP_ID_AQUI">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo $titulo; ?> - Candeivid">
<meta name="twitter:description" content="<?php echo $descripcion; ?>">
<meta name="twitter:image" content="<?php echo $imagen_url; ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?php echo $descripcion; ?>">
<title><?php echo $titulo; ?> - Candeivid</title>
<link rel="canonical" href="<?php echo $url_actual; ?>">


<link rel="stylesheet" href="<?=$us_url_root?>usersc/templates/<?=$settings->template?>/assets/fonts/glyphicons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<link href="<?=$us_url_root?>users/css/datatables.css" rel="stylesheet">
<link href="<?=$us_url_root?>users/css/menu.css" rel="stylesheet">
<script src="<?= $us_url_root?>users/js/menu.js"></script>
<link rel="stylesheet" href="<?=$us_url_root?>users/fonts/css/fontawesome.min.css">
<link rel="stylesheet" href="<?=$us_url_root?>users/fonts/css/brands.min.css">
<link rel="stylesheet" href="<?=$us_url_root?>users/fonts/css/solid.min.css">
<link rel="stylesheet" href="<?=$us_url_root?>users/fonts/css/v4-shims.min.css">

<!-- CUSTOM CSS -->
<link rel="stylesheet" href="<?=$us_url_root?>css/custom_nav.css">
<?php
require_once $abs_us_root . $us_url_root . "users/js/jquery.php";
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js" integrity="sha512-7Pi/otdlbbCR+LnW+F7PwFcSDJOuUJB3OxtEHbg4vSMvzvJjde4Po1v4BR9Gdc9aXNUNFVUY+SK51wWT8WF0Gg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

 <!-- Incluir TinyMCE desde CDN - El API KEY está sacado con la cuenta de Gmail rutascandeivid-->    
 <script src="https://cdn.tiny.cloud/1/nhlsx7jkin6voponazn6x5mjea8yt6w7zn7ir3dwvu33jr4w/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<!-- Esto es de las cookies de Usercentrics 
<script src="https://web.cmp.usercentrics.eu/modules/autoblocker.js"></script>
<script id="usercentrics-cmp" src="https://web.cmp.usercentrics.eu/ui/loader.js" data-settings-id="y9oJV2qzdICjpQ" async></script>
-->

<?php
//if the theme has never been loaded before, it needs to be initialized. We do this so we can distribute it without css files and customizations in place
if (!file_exists($abs_us_root.$us_url_root.'usersc/templates/'.$settings->template.'/assets/css/customizations.php')) {
  require_once $abs_us_root.$us_url_root.'usersc/templates/'.$settings->template.'/initialize.php';
  initializeCustomizerTheme();
}


//set a variable above init.php of $child_theme = filename to load a child theme instead of your core template
$child_loaded = false;
if(file_exists($abs_us_root.$us_url_root.'usersc/templates/'.$settings->template.'/assets/css/revision.php')){
  require_once($abs_us_root.$us_url_root.'usersc/templates/'.$settings->template.'/assets/css/revision.php');
}

//if the child_theme variable is set, we need to make sure that it is defined in the revision.php file and that the css file exists
if(isset($child_theme) && $child_theme != ''){
  //
  if(isset($child_themes) && is_array($child_themes) && isset($child_themes[$child_theme])) {
    $timestampedFile = $child_themes[$child_theme];
    if(file_exists($abs_us_root.$us_url_root.'usersc/templates/'.$settings->template.'/assets/child_themes/'.$timestampedFile)){
      echo '<link href="'.$us_url_root.'usersc/templates/'.$settings->template.'/assets/child_themes/'.$timestampedFile.'" rel="stylesheet">';
      $child_loaded = true;
    }
  }

} 
  // Fall back to standard theme
  if(!$child_loaded && file_exists($abs_us_root.$us_url_root.'usersc/templates/'.$settings->template.'/assets/css/revision.php')){
      if(isset($css_revision) && $css_revision != '' && file_exists($abs_us_root.$us_url_root.'usersc/templates/'.$settings->template.'/assets/css/'.$css_revision)){ 
      echo '<link href="'.$us_url_root.'usersc/templates/'.$settings->template.'/assets/css/'.$css_revision.'" rel="stylesheet">';
    }
  }
  
//if this file exists, it overrides everything before it
if(file_exists($abs_us_root.$us_url_root.'usersc/templates/'.$settings->template.'.css')){?>
  <link href="<?=$us_url_root?>usersc/templates/<?=$settings->template?>.css" rel="stylesheet">
<?php } ?>

</head>
<body class="d-flex flex-column min-vh-100">
<?php require_once($abs_us_root.$us_url_root.'users/includes/template/header3_must_include.php'); ?>
