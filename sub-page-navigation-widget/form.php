<p>
  <?php Echo $this->t('Title:'); ?>
  <input type="text" name="<?php  Echo $this->get_field_name('title') ?>" value="<?php Echo $this->get_option('title') ?>" />
</p>

<p>
  <input type="checkbox" value="yes" name="<?php Echo $this->get_field_name('hide_widget_title') ?>"<?php Checked( $this->get_option('hide_widget_title'), 'yes' ); ?>/>
  <?php echo $this->t('Hide the widget title.') ?>
</p>

<p>
  <input type="checkbox" value="yes" name="<?php Echo $this->get_field_name('replace_widget_title') ?>"<?php Checked( $this->get_option('replace_widget_title'), 'yes' ); ?>/>
  <?php echo $this->t('Replace the widget title with the title of the parent page if possible.') ?>
</p>

<p>
  <?php _e( 'Sort by:' ); ?>
  <select name="<?php Echo $this->get_field_name('sortby'); ?>">
    <option value="menu_order" <?php selected( $this->get_option('sortby'), 'menu_order' ); ?> ><?php _e('Page order'); ?></option>
    <option value="post_title" <?php selected( $this->get_option('sortby'), 'post_title' ); ?> ><?php _e('Page title'); ?></option>
    <option value="ID" <?php selected( $this->get_option('sortby'), 'ID' ); ?> ><?php _e( 'Page ID' ); ?></option>
  </select>
</p>

<p>
  <?php _e( 'Exclude:' ); ?>
  <input type="text" value="<?php echo $this->get_option('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" /><br />
  <small><?php _e( 'Page IDs, separated by commas.' ); ?></small>
</p>

<p>
  <input type="checkbox" value="yes" name="<?php Echo $this->get_field_name('do_not_show_on_top_leves_without_subs') ?>"<?php checked( $this->get_option('do_not_show_on_top_leves_without_subs'), 'yes' ); ?>/>
  <?php Echo $this->t('Do not show this widget on top level pages if there are no sub pages.') ?>
</p>

<p>
  <input type="checkbox" value="yes" name="<?php Echo $this->get_field_name('hide_upward_link') ?>"<?php checked( $this->get_option('hide_upward_link'), 'yes' ); ?>/>
  <?php Echo $this->t('Hide link to parant page.') ?>
</p>
