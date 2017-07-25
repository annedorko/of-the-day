<?php
if ( $settings->current_tab() == 'facebook-app' ) {
  do_action( 'ofd_fb_javascript' );
}
?>
<div class="wrap settings-dictionary">
  <h1><?php _e( 'Of The Day', 'of-the-day' ); ?></h1>
  <h2 class="nav-tab-wrapper">
  <?php
  $active_class = ' nav-tab-active';
  $tabs = $settings->tabs();
  foreach ( $tabs as $tab => $info ) { ?>
    <a href="?options-general.php?&amp;page=of-the-day&amp;tab=<?php echo $tab; ?>" class="nav-tab<?php echo $settings->current_tab() == $tab ? $active_class : ''; ?>">
      <span class="dashicons dashicons-<?php echo $info['icon']; ?>"></span>
        <?php echo $info['label']; ?>
    </a>
    <?php } ?>
  </h2>
  <form action="options.php" method="post">
    <?php $settings_section = $settings->current_tab(); ?>
    <?php settings_fields( $settings_section ); ?>
    <?php do_settings_sections( $settings_section ); ?>
    <?php submit_button(); ?>
  </form>
</div>
