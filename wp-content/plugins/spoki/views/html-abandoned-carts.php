<?php
$has_wc = spoki_has_woocommerce();
$is_current_tab = $GLOBALS['current_tab'] == 'abandoned-carts';
$is_pro = isset($this->options['account_info']['plan']['slug']) && strpos($this->options['account_info']['plan']['slug'], 'pro') !== false;
$has_abandoned_carts = isset($this->options['abandoned_carts']['enable_tracking']) && $this->options['abandoned_carts']['enable_tracking'] == 1;
?>

<div <?php if (!$is_current_tab) echo 'style="display:none"' ?>>
    <h2 style="display: flex; align-items: center">
		<?php _e('Abandoned Carts', "spoki") ?>
        <a href="#TB_inline?&width=300&inlineId=abandoned-carts-info-dialog" class="thickbox button-info">ℹ</a>
    </h2>
    <p>
		<?php _e('<b>Send abandoned cart WhatsApp messages</b> to customers and reduce dropout rates.', "spoki") ?>
    </p>
    <img class="cover-image" src="<?php echo plugins_url() . '/' . SPOKI_PLUGIN_NAME . '/assets/images/abandoned-carts.png' ?>"/>

    <fieldset <?php if ($is_current_tab && !$has_wc) : ?>disabled<?php endif ?>>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="<?php echo SPOKI_OPTIONS ?>[abandoned_carts][enable_tracking]">
						<?php _e('Enable Tracking', "spoki") ?>
                    </label>
                </th>
                <td>
                    <input type="checkbox" id="enable_tracking"
                           name="<?php echo SPOKI_OPTIONS ?>[abandoned_carts][enable_tracking]"
                           value="1" <?php if (isset($this->options['abandoned_carts']['enable_tracking'])) echo checked(1, $this->options['abandoned_carts']['enable_tracking'], false) ?>>

                    <label for="enable_tracking"><?php _e('Start capturing abandoned carts', "spoki") ?></label>
                    <p class="description">
                        <b><?php _e("Note", "spoki") ?></b>: <?php _e('Cart will be considered abandoned if order is not completed in <b>15 minutes</b>', "spoki") ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="<?php echo SPOKI_OPTIONS ?>[abandoned_carts][notify_to_admin]">
						<?php _e('Notify recovery to admin', "spoki") ?>
                    </label>
                </th>
                <td>
                    <input type="checkbox" id="notify_to_admin"
                           name="<?php echo SPOKI_OPTIONS ?>[abandoned_carts][notify_to_admin]"
                           value="1" <?php if (isset($this->options['abandoned_carts']['notify_to_admin'])) echo checked(1, $this->options['abandoned_carts']['notify_to_admin'], false) ?>>

                    <label for="notify_to_admin">
						<?php
						/* translators: %1$s: Telephone */
						printf(__('Send a <b>cart recovered notification</b> to your WhatsApp Telephone <small>(%1$s)</small>', "spoki"), Spoki()->shop['telephone'])
						?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <div style="display: flex; align-items: center">
                        <label for="<?php echo SPOKI_OPTIONS ?>[abandoned_carts][trigger_webhook]" style="white-space: nowrap">
							<?php _e('Enable Webhook', "spoki") ?>
                        </label>
						<?php if (!$is_pro): ?>
                            <a href="<?php print(Spoki()->get_pro_plan_link()) ?>" target="_blank" style="text-decoration: none">
                                <div class="spoki-badge bg-spoki-secondary"><?php _e('Spoki PRO', "spoki") ?></div>
                            </a>
						<?php endif ?>
                    </div>
                </th>
                <td>
                    <input type="checkbox" id="trigger_webhook" disabled
                           name="<?php echo SPOKI_OPTIONS ?>[abandoned_carts][trigger_webhook]"
                           value="1" <?php if (!$is_pro && isset($this->options['abandoned_carts']['trigger_webhook'])) echo checked(1, $this->options['abandoned_carts']['trigger_webhook'], false) ?>>

                    <label for="trigger_webhook">
						<?php _e(' Allows you to trigger webhook automatically upon cart abandonment and recovery', "spoki") ?>
                    </label>
                </td>
            </tr>
        </table>
		<?php if ($has_wc) : submit_button(null, 'primary', 'submit-templates'); else : ?>
            <p>
				<?php _e("Install and activate the <strong>WooCommerce</strong> plugin to enable the Spoki features for WooCommerce.", "spoki") ?>
            </p>
		<?php endif ?>
    </fieldset>
</div>
