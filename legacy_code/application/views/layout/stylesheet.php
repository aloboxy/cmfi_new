<!-- Modern Fonts: Inter -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Bootstrap & Icons -->
<link rel="stylesheet" href="<?php echo base_url('assets/vendor/bootstrap/css/bootstrap.css');?>">
<link rel="stylesheet" href="<?php echo base_url('assets/vendor/font-awesome/css/all.min.css');?>">

<!-- Jquery Datatables CSS -->
<link rel="stylesheet" href="<?php echo base_url('assets/vendor/datatables/media/css/dataTables.bootstrap.min.css');?>">

<!-- UI Components -->
<link rel="stylesheet" href="<?php echo base_url('assets/vendor/select2/css/select2.css');?>">
<link rel="stylesheet" href="<?php echo base_url('assets/vendor/select2-bootstrap-theme/select2-bootstrap.min.css');?>">
<link rel="stylesheet" href="<?php echo base_url('assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker3.min.css');?>">
<link rel="stylesheet" href="<?php echo base_url('assets/vendor/simple-line-icons/css/simple-line-icons.css');?>">
<link rel="stylesheet" href="<?php echo base_url('assets/vendor/magnific-popup/magnific-popup.css');?>">
<link rel="stylesheet" href="<?php echo base_url('assets/css/custom-style.css');?>">
<link rel="stylesheet" href="<?php echo base_url('assets/vendor/sweetalert/sweetalert-custom.css');?>">

<!-- Modern School Theme Skin -->
<link rel="stylesheet" href="<?php echo base_url('assets/css/skins/default.css');?>">

<?php if (isset($theme_config)): ?>
<style type="text/css">
  :root {
    <?php if (!empty($theme_config['primary_color'])): ?>
    --primary-light: <?php echo $theme_config['primary_color']; ?> !important;
    <?php endif; ?>
    <?php if (!empty($theme_config['sidebar_bg'])): ?>
    --sidebar-bg: <?php echo $theme_config['sidebar_bg']; ?> !important;
    <?php endif; ?>
    <?php if (!empty($theme_config['sidebar_text'])): ?>
    --sidebar-text: <?php echo $theme_config['sidebar_text']; ?> !important;
    <?php endif; ?>
    <?php if (!empty($theme_config['sidebar_icon'])): ?>
    --sidebar-icon: <?php echo $theme_config['sidebar_icon']; ?> !important;
    <?php endif; ?>
    <?php if (!empty($theme_config['sidebar_active'])): ?>
    --sidebar-active: <?php echo $theme_config['sidebar_active']; ?> !important;
    <?php endif; ?>
    <?php if (!empty($theme_config['text_dark'])): ?>
    --text-dark: <?php echo $theme_config['text_dark']; ?> !important;
    <?php endif; ?>
  }
</style>
<?php endif; ?>

<!-- Include Modern UI Overrides -->
<link rel="stylesheet" href="<?php echo base_url('assets/css/modern-ui-updates.css');?>">

<!-- jQuery -->
<script src="<?php echo base_url('assets/vendor/jquery/jquery.min.js');?>"></script>
<script src="<?php echo base_url('assets/vendor/jquery-ui/jquery-ui.min.js');?>"></script>
<script src="<?php echo base_url('assets/vendor/modernizr/modernizr.js');?>"></script>
