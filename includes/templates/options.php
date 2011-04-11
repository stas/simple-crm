<?php if( $flash ) { ?>
    <div id="message" class="updated fade">
        <p><strong><?php echo $flash; ?></strong></p>
    </div>
<?php } ?>
<div id="icon-tools" class="icon32"><br /></div>
<div class="wrap">
    <h2><?php _e( 'Simple CRM','scrm' ); ?></h2>
    <div id="poststuff" class="metabox-holder">
        <div class="postbox">
            <h3 class="hndle" ><?php _e( 'Introduction','scrm' )?></h3>
            <div class="inside">
                <p>
                    <?php _e( 'Simple CRM lets you define custom fields to extend user profiles and it is also a framework for integration with all kind of CRM API webservices.','scrm' ); ?>
                </p>
                <form action="" method="post">
                    <?php wp_nonce_field( 'scrm', 'scrm_force_nonce' ); ?>
                    <p>
                        <input id="force_redirect" name="force_redirect" type="checkbox" <?php checked( $force_redirect, 1 ); ?>/>
                        <label for="force_redirect">
                            <strong><?php _e( 'Force users to update their information by redirecting to their profile screen.','scrm' )?></strong>
                        </label>
                    </p>
                    <p>
                        <input type="submit" class="button-primary" value="<?php _e( 'Save Changes' )?>"/>
                    </p>
                </form>
            </div>
        </div>
        
         <form action="" method="post">
            <?php wp_nonce_field( 'scrm', 'scrm_nonce' ); ?>
            <div class="postbox">
                <h3 class="hndle" ><?php _e( 'Add Field','scrm' )?></h3>
                <div class="inside">
                    <div class="scrm-field-form">
                        <p class="form-field">
                            <label for="field_title">
                                <strong><?php _e( 'Field Title','scrm' )?></strong>
                            </label>
                            <br />
                            <input id="field_title" name="field_title" type="text" value="<?php echo $field['title']; ?>"/>
                        </p>
                        <p class="form-field">
                            <label for="field_name">
                                <strong><?php _e( 'Field Name','scrm' )?></strong>
                            </label>
                            <br />
                            <input id="field_name" name="field_name" type="text" value="<?php echo $field['name']; ?>"/>
                        </p>
                        <p class="form-field">
                            <label for="field_type">
                                <strong><?php _e( 'Field Type','scrm' )?></strong>
                            </label>
                            <select id="field_type" name="field_type">
                                <option value="text" <?php selected( 'text', $field['type'] ); ?>><?php _e( 'Text Field','scrm' )?></option>
                                <option value="textarea" <?php selected( 'textarea', $field['type'] ); ?>><?php _e( 'Text Area','scrm' )?></option>
                            </select>
                        </p>
                    </div>
                    <p>
                        <input type="submit" class="button-primary" value="<?php _e( 'Save Changes' )?>"/>
                    </p>
                </div>
            </div>
        </form>
        
        <div class="postbox">
            <h3 class="hndle" ><?php _e( 'Current Fields','scrm' )?></h3>
            <div class="inside">
                <div class="scrm-fields">
                    <?php if( !empty( $fields ) ): ?>
                        <ol>
                            <?php foreach( $fields as $f ): ?>
                            <li>
                                <strong><?php echo $f['title']; ?></strong> &mdash;
                                <a href="<?php echo $edit_permalink; ?>&amp;edit=<?php echo $f['name']; ?>" class="button"><?php _e( 'Edit' )?></a>
                                <a href="<?php echo $delete_permalink; ?>&amp;del=<?php echo $f['name']; ?>" class="button"><?php _e( 'Remove' )?></a>
                            </li>
                            <?php endforeach; ?>
                        </ol>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php do_action( 'scrm_options_screen' ); ?>
        
    </div>
</div>
