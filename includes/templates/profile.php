<?php if( empty($fields) ) return; ?>
<?php wp_nonce_field( 'scrm', 'scrm_nonce' ); ?>
<h3><?php _e( 'Other Details','scrm' )?></h3>
<table class="form-table">
    <tbody>
        <?php foreach ( $fields as $f ): ?>
        <tr>
            <th>
                <label for="<?php echo $f['name']; ?>"><?php echo $f['title']; ?></label>
            </th>
            <td>
                <?php if( $f['type'] == 'text' ): ?>
                <input 
                    class="regular-text" 
                    id="<?php echo $f['name']; ?>"
                    name="scrm[<?php echo $f['name']; ?>]"
                    type="<?php echo $f['type']; ?>" 
                    value="<?php echo $fields_data[$f['name']] ? $fields_data[$f['name']] : ''; ?>"
                />
                <?php endif; ?>
                
                <?php if( $f['type'] == 'textarea' ): ?>
                <textarea id="<?php echo $f['name']; ?>" name="scrm[<?php echo $f['name']; ?>]"
                ><?php echo $fields_data[$f['name']] ? $fields_data[$f['name']] : ''; ?></textarea>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
