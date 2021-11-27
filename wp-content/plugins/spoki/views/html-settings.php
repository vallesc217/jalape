<?php
$has_wc = spoki_has_woocommerce();
$is_current_tab = $GLOBALS['current_tab'] == 'settings';
$has_valid_secret = isset($this->options['secret_status']['code']) && $this->options['secret_status']['code'] == 200;
$has_key = (isset($this->options['secret']) && trim($this->options['secret']) != '') || (isset($this->options['delivery_url']) && trim($this->options['delivery_url']) != '');
$has_spoki_keys = (isset($this->options['secret']) && trim($this->options['secret']) != '') && (isset($this->options['delivery_url']) && trim($this->options['delivery_url']) != '');

if ($is_current_tab) {
	Spoki()->check_secret_status();

	if ($has_wc && $has_spoki_keys && !isset($this->options['account_info']['plan']['name'])) {
		Spoki()->fetch_account_info();
	}
}

if ($has_wc && $has_key && !$has_valid_secret) { ?>
    <div class="notice notice-error">
        <p>
            <b><?php _e('Your Spoki keys are not valid!', "spoki"); ?></b>
            <a href="?page=<?php echo urlencode(SPOKI_PLUGIN_NAME) ?>&tab=settings">
				<?php _e('Settings', "spoki") ?>
            </a>
        </p>
    </div>
<?php } ?>

<div <?php if (!$is_current_tab) echo 'style="display:none"' ?>>
    <h2><?php _e('Account info', "spoki") ?></h2>
    <p><?php _e('This account info will be used to let WhatsApp buttons work.', "spoki") ?></p>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="<?php echo SPOKI_OPTIONS ?>[telephone]">
					<?php _e('Your WhatsApp Telephone', "spoki") ?> *
                </label>
            </th>
            <td>
                <div class="spoki-phone-container">
                    <input type="text" class="regular-text spoki-phone-prefix"
                           name="<?php echo SPOKI_OPTIONS ?>[prefix]"
						   <?php if ($is_current_tab) echo 'required' ?>
                           value="<?php if (isset($this->options['prefix']) && trim($this->options['prefix']) != '') echo $this->options['prefix'] ?>"
                           placeholder="+39"
                    />
                    <input type="tel" class="regular-text"
                           name="<?php echo SPOKI_OPTIONS ?>[telephone]"
                           <?php if ($is_current_tab) echo 'required' ?>
                           placeholder="3331234567"
                           value="<?php if (isset($this->options['telephone']) && trim($this->options['telephone']) != '') echo $this->options['telephone'] ?>"
                    />
                </div>
                <p class="description">
                    <b><?php _e("Note", "spoki") ?></b>: <?php _e("Your customers will send messages to this WhatsApp number", "spoki") ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="<?php echo SPOKI_OPTIONS ?>[shop_name]">
					<?php _e('Shop Name', "spoki") ?> *
                </label>
            </th>
            <td>
                <input type="text" class="regular-text" <?php if ($is_current_tab) echo 'required' ?>
                       name="<?php echo SPOKI_OPTIONS ?>[shop_name]"
                       placeholder="<?php echo Spoki()->shop['name'] ?>"
                       value="<?php if (isset($this->options['shop_name']) && trim($this->options['shop_name']) != '') echo $this->options['shop_name'] ?>"
                />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="<?php echo SPOKI_OPTIONS ?>[email]">
					<?php _e('Your best email', "spoki") ?> *
                </label>
            </th>
            <td>
                <input type="email" class="regular-text" <?php if ($is_current_tab) echo 'required' ?>
                       name="<?php echo SPOKI_OPTIONS ?>[email]"
                       placeholder="<?php echo Spoki()->shop['email'] ?>"
                       value="<?php if (isset($this->options['email']) && trim($this->options['email']) != '') echo $this->options['email'] ?>"
                />
            </td>
        </tr>
		<?php if ($is_current_tab): ?>
            <tr>
                <th>
                    <label for="single_product_button_position">
                        <b><?php _e('Language', "spoki") ?></b>
                    </label>
                </th>
                <td>
					<?php
					$language = Spoki()->shop['language'];
					$is_spanish = spoki_starts_with($language, 'es');
					$is_italian = spoki_starts_with($language, 'it');
					$is_portoguese = spoki_starts_with($language, 'pt');
					$is_french = spoki_starts_with($language, 'fr');
					$is_english = !$is_italian && !$is_spanish && !$is_portoguese && !$is_french;
					?>
                    <select name="<?php echo SPOKI_OPTIONS ?>[language]" id="language" class="regular-text">
                        <option value="en-EN" <?php echo $is_english ? 'selected' : '' ?>><?php echo __('English', "spoki") ?></option>
                        <option value="it-IT" <?php echo $is_italian ? 'selected' : '' ?>><?php echo __('Italian', "spoki") ?></option>
                        <option value="es-ES" <?php echo $is_spanish ? 'selected' : '' ?>><?php echo __('Spanish', "spoki") ?></option>
                        <option value="pt-PT" <?php echo $is_portoguese ? 'selected' : '' ?>><?php echo __('Portuguese', "spoki") ?></option>
                        <option value="fr-FR" <?php echo $is_french ? 'selected' : '' ?>><?php echo __('French', "spoki") ?></option>
                    </select>
                    <p class="description">
						<?php _e('Your language is not in list?', "spoki") ?> <a target="_blank" href="<?php echo SPOKI_SUGGEST_TEMPLATES_URL ?>"><?php _e('Suggest messages', "spoki") ?></a>
                    </p>
                    <p class="description">
                        <b><?php _e("Note", "spoki") ?></b>: <?php _e('Choose the language of the WooCommerce messages to send to customers', "spoki"); ?>
                    </p>
                </td>
            </tr>
		<?php endif; ?>
        <tr>
            <th scope="row">
                <label for="<?php echo SPOKI_OPTIONS ?>[contact_link]">
					<?php _e('Contact link', "spoki") ?>
                </label>
            </th>
            <td>
                <input type="text" class="regular-text"
                       name="<?php echo SPOKI_OPTIONS ?>[contact_link]"
                       placeholder="<?php _e('Reply to the WhatsApp telephone', 'spoki') ?>"
                       value="<?php if (isset($this->options['contact_link']) && trim($this->options['contact_link']) != '') echo $this->options['contact_link'] ?>"
                />
                <p class="description">
                    <b><?php _e("Note", "spoki") ?></b>: <?php _e("Enter the link where your customers can contact you after receiving the notification message.<br/>This link will come at the end of each WooCommerce notification message.<br/>If you leave empty the customers will reply to your WhatsApp telephone.", "spoki") ?>
                    <small>(<?php echo Spoki()->shop['telephone'] ?>)</small>
                </p>
            </td>
        </tr>
    </table>
    <!--    <script src="--><?php //echo plugins_url() . '/' . SPOKI_PLUGIN_NAME . '/assets/js/prefixes.js' ?><!--"></script>-->
    <!--    <script>-->
    <!--        Array.from(document.getElementsByClassName('spoki-phone-prefixes')).forEach(el => el.append(...prefixes.map(p => {-->
    <!--            var option = document.createElement('option');-->
    <!--            option.value = p.code;-->
    <!--            option.text = `${p.code} (${p.name})`;-->
    <!--            return option;-->
    <!--        })))-->
    <!--    </script>-->
    <br/>
    <hr/>
    <h2><?php _e('Spoki keys', "spoki") ?></h2>
    <p class="description"><?php _e('Enter here the keys we emailed you to send automatic notifications with WooCommerce.', "spoki") ?></p>
    <table class="form-table">
        <tr>
            <th scope="row"><?php _e('Current Spoki Plan', "spoki") ?></th>
            <td>
                <p>
					<?php
					if ($has_spoki_keys && isset($this->options['account_info']['plan']['name'])) {
						echo "<b>" . $this->options['account_info']['plan']['name'] . "</b> ";
						echo "<a href='" . $this->options['account_info']['upgrade_url'] . "' target='_blank'><button type='button' class='button button-primary bg-spoki' style='border: none;'>" . __('Change plan', "spoki") . "</button></a>";
					} else {
						_e('There is no associated Spoki plan.<br/>If you already have a Spoki account insert your Spoki keys below, if not', "spoki");
						echo " <a href='" . Spoki()->get_plan_link(false) . "' target='_blank'>" . __('enable Spoki Free', "spoki") . "</a>.";
					}
					?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Spoki Delivery URL', "spoki") ?></th>
            <td>
                <input type="text" class="regular-text" name="<?php echo SPOKI_OPTIONS ?>[delivery_url]" value="<?php if (isset($this->options['delivery_url']) && trim($this->options['delivery_url']) != '') echo $this->options['delivery_url'] ?>">
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Spoki Secret', "spoki") ?></th>
            <td>
                <input type="text" class="regular-text" name="<?php echo SPOKI_OPTIONS ?>[secret]" value="<?php if (isset($this->options['secret']) && trim($this->options['secret']) != '') echo $this->options['secret'] ?>">
				<?php if ($has_valid_secret):
					echo "<p class='text-success'>" . __('Valid keys!', "spoki") . "</p>";
				else:
					echo "<p class='text-danger'>" . __('Invalid keys', "spoki") . "</p>";
				endif; ?>
            </td>
        </tr>
    </table>
    <br/>
    <hr/>
    <h2><?php _e('Billing data', "spoki") ?></h2>
    <p><?php _e('If you have a Spoki subscription plan we will send you an invoice with these billing data.', "spoki") ?></p>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="<?php echo SPOKI_OPTIONS ?>[billing_data][vat_name]">
					<?php _e('VAT Name', "spoki") ?>
                </label>
            </th>
            <td>
                <input type="text" class="regular-text"
                       name="<?php echo SPOKI_OPTIONS ?>[billing_data][vat_name]"
                       value="<?php if (isset($this->options['billing_data']['vat_name']) && trim($this->options['billing_data']['vat_name']) != '') echo $this->options['billing_data']['vat_name'] ?>"
                />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="<?php echo SPOKI_OPTIONS ?>[billing_data][route]">
					<?php _e('Address', "spoki") ?>
                </label>
            </th>
            <td>
                <input type="text" class="regular-text"
                       name="<?php echo SPOKI_OPTIONS ?>[billing_data][route]"
                       value="<?php if (isset($this->options['billing_data']['route']) && trim($this->options['billing_data']['route']) != '') echo $this->options['billing_data']['route'] ?>"
                />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="<?php echo SPOKI_OPTIONS ?>[billing_data][zip_code]">
					<?php _e('Zip Code', "spoki") ?>
                </label>
            </th>
            <td>
                <input type="text" class="regular-text"
                       name="<?php echo SPOKI_OPTIONS ?>[billing_data][zip_code]"
                       value="<?php if (isset($this->options['billing_data']['zip_code']) && trim($this->options['billing_data']['zip_code']) != '') echo $this->options['billing_data']['zip_code'] ?>"
                />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="<?php echo SPOKI_OPTIONS ?>[billing_data][city]">
					<?php _e('City', "spoki") ?>
                </label>
            </th>
            <td>
                <input type="text" class="regular-text"
                       name="<?php echo SPOKI_OPTIONS ?>[billing_data][city]"
                       value="<?php if (isset($this->options['billing_data']['city']) && trim($this->options['billing_data']['city']) != '') echo $this->options['billing_data']['city'] ?>"
                />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="<?php echo SPOKI_OPTIONS ?>[billing_data][province]">
					<?php _e('Province', "spoki") ?>
                </label>
            </th>
            <td>
                <input type="text" class="regular-text"
                       name="<?php echo SPOKI_OPTIONS ?>[billing_data][province]"
                       value="<?php if (isset($this->options['billing_data']['province']) && trim($this->options['billing_data']['province']) != '') echo $this->options['billing_data']['province'] ?>"
                />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="<?php echo SPOKI_OPTIONS ?>[billing_data][country]">
					<?php _e('Country', "spoki") ?>
                </label>
            </th>
            <td>
                <input type="text" class="regular-text"
                       name="<?php echo SPOKI_OPTIONS ?>[billing_data][country]"
                       value="<?php if (isset($this->options['billing_data']['country']) && trim($this->options['billing_data']['country']) != '') echo $this->options['billing_data']['country'] ?>"
                />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="<?php echo SPOKI_OPTIONS ?>[billing_data][vat_number]">
					<?php _e('VAT Number', "spoki") ?>
                </label>
            </th>
            <td>
                <input type="text" class="regular-text"
                       name="<?php echo SPOKI_OPTIONS ?>[billing_data][vat_number]"
                       value="<?php if (isset($this->options['billing_data']['vat_number']) && trim($this->options['billing_data']['vat_number']) != '') echo $this->options['billing_data']['vat_number'] ?>"
                />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="<?php echo SPOKI_OPTIONS ?>[billing_data][c_f]">
					<?php _e('Fiscal Code', "spoki") ?>
                </label>
            </th>
            <td>
                <input type="text" class="regular-text"
                       name="<?php echo SPOKI_OPTIONS ?>[billing_data][c_f]"
                       value="<?php if (isset($this->options['billing_data']['c_f']) && trim($this->options['billing_data']['c_f']) != '') echo $this->options['billing_data']['c_f'] ?>"
                />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="<?php echo SPOKI_OPTIONS ?>[billing_data][pec]">
					<?php _e('PEC', "spoki") ?>
                </label>
            </th>
            <td>
                <input type="email" class="regular-text"
                       name="<?php echo SPOKI_OPTIONS ?>[billing_data][pec]"
                       value="<?php if (isset($this->options['billing_data']['pec']) && trim($this->options['billing_data']['pec']) != '') echo $this->options['billing_data']['pec'] ?>"
                />
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="<?php echo SPOKI_OPTIONS ?>[billing_data][pec]">
					<?php _e('SID', "spoki") ?>
                </label>
            </th>
            <td>
                <input type="text" class="regular-text"
                       name="<?php echo SPOKI_OPTIONS ?>[billing_data][sid]"
                       value="<?php if (isset($this->options['billing_data']['sid']) && trim($this->options['billing_data']['sid']) != '') echo $this->options['billing_data']['sid'] ?>"
                />
            </td>
        </tr>
    </table>

    <br/><br/><br/>
    <div>
        <input type="checkbox" id="terms_check" <?php if ($is_current_tab) echo 'required' ?>
               name="<?php echo SPOKI_OPTIONS ?>[terms_check]"
               value="1" <?php if (isset($this->options['terms_check'])) echo checked(1, $this->options['terms_check'], false) ?>>
        <label for="terms_check">
			<?php _e("I accept", "spoki") ?>
            <a href="https://app.spoki.it/static/terms_conditions_spoki_flex.pdf" target="_blank"><?php _e('general terms and conditions', "spoki") ?></a>
			<?php _e("for using the Spoki service and the", "spoki") ?>
            <a href="https://app.spoki.it/static/privacy.pdf" target="_blank"><?php _e('privacy policy', "spoki") ?></a>.
        </label>
    </div>

	<?php if ($is_current_tab) : ?>
        <input type="hidden" name="<?php echo SPOKI_OPTIONS ?>[is_settings]" value="1">
	<?php
	endif;
	submit_button(); ?>
</div>
