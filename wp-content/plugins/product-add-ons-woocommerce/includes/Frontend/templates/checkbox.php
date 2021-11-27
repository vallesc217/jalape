<?php

defined( 'ABSPATH' ) || exit;

foreach ( $type->values as $value ) {
	if ( $value->hide ) {
		continue;
	}
	?>
    <div class="zaddon_checkbox zaddon_option">
        <label>
            <input
                type="checkbox"
				<?= $value->checked ? "checked" : "" ?>
                name="<?= $name ?>[value][]"
                value="<?= $value->getID() ?>"
                data-price="<?= $value->price ?>"
                data-type="<?= $type->type ?>"
				<?php do_action( 'zaddon_input_property', $type ); ?>
            />
			<?php do_action( 'zaddon_before_option_title', $value, $name ); ?>
            <span class="zaddon-radio-title">
			    <span class="zaddon_title"><?= $value->title ?></span>
				<?= $value->price ? '(' . apply_filters( 'zaddon_option_format_price', wc_price( $value->price ), $type, $value ) . ')' : "" ?>
				<?php
				if ( ! $value->hide_description && ! empty( $value->description ) ) {
					if ( $value->tooltip_description ) {
						?>
                        <span class="za-tooltip">
                                <button class="za-tooltip__control" type="button" aria-label="<?php echo $value->description ?>"></button>
                                <span class="za-tooltip__body"><?php echo $value->description ?></span>
                            </span>
						<?php
					} else {
						?>
                        <p class="zaddon-option-description"><?php echo $value->description ?></p>
						<?php
					}
				}
				?>
            </span>
        </label>
		<?php do_action( 'zaddon_after_option_input', $value, $name ); ?>
    </div>
<?php } ?>
