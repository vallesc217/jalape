<?php

defined( 'ABSPATH' ) || exit;

foreach ( $type->values as $value ) {
	if ( $value->hide ) {
		continue;
	}
	?>
    <div class="zaddon_text zaddon_option">
        <label class="zaddon-flex" for="zaddon_<?= $value->getID() ?>">
			<?php do_action( 'zaddon_before_option_title', $value, $name ); ?>
			<span class="zaddon_title"><?= $value->title ?></span> <?= $value->price ? '(' . apply_filters( 'zaddon_option_format_price', wc_price( $value->price ), $type, $value ) . ')' : "" ?>
			<?php
			if ( $value->tooltip_description && ! $value->hide_description && ! empty( $value->description ) ) {
				?>
                <span class="za-tooltip">
                    <button class="za-tooltip__control" type="button" aria-label="<?php echo $value->description ?>"></button>
                    <span class="za-tooltip__body"><?php echo $value->description ?></span>
                </span>
				<?php
			}
			?>
        </label>
        <input
            type="text"
			<?= isset( $value->required ) && $value->required ? "required" : "" ?>
            id="zaddon_<?= $value->getID() ?>"
            name="<?= $name ?>[value][<?= $value->getID() ?>]"
            data-price="<?= $value->price ?>"
            data-type="<?= $type->type ?>"
			<?php do_action( 'zaddon_input_property', $type ); ?>
        />
		<?php do_action( 'zaddon_after_option_input', $value, $name ); ?>
		<?= ! $value->hide_description && ! $value->tooltip_description && ! empty( $value->description ) ? '<p class="zaddon-option-description">' . $value->description . '</p>' : "" ?>
    </div>
<?php } ?>
