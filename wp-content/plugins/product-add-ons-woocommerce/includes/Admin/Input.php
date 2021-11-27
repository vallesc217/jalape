<?php

namespace ZAddons\Admin;

defined( 'ABSPATH' ) || exit;

class Input {
	public static function renderCheckbox( $args ) {
		$name  = $args['name'];
		$value = get_option( $name );
		?>
        <input type="checkbox" id="<?= esc_attr( $name ); ?>" name="<?= esc_attr( $name ); ?>" <?php if ( $value ) : echo 'checked'; endif; ?>/>
		<?php if ( isset( $args['label'] ) ) : ?>
            <label for="<?= esc_attr( $name ); ?>"><?= esc_html( $args['label'] ); ?></label>
		<?php endif;
	}

	public static function renderRadio( $args ) {
		$name  = $args['name'];
		$value = get_option( $name );

		$i = 1;
		foreach ( $args['options'] as $option ) {
			$checked = $value === $option['value'] || ( empty( $value ) && $i == 1 ) ? 'checked' : '';
			echo $i > 1 ? '<br>' : '';
			?>
            <label class="zaddon-radio">
                <input type="radio" name="<?= esc_attr( $name ); ?>" value="<?= esc_attr( $option['value'] ); ?>" <?= esc_html( $checked ); ?>/>
				<?= isset( $option['label'] ) ? '<span>' . esc_html( $option['label'] ) . '</span>' : ''; ?>
            </label>
			<?php
			$i ++;
		}
	}

	public static function renderText( $args ) {
		$type        = 'type="' . ( isset( $args['type'] ) ? esc_attr( $args['type'] ) : 'text' ) . '"';
		$name        = 'name="' . esc_attr( $args['name'] ) . '"';
		$value       = 'value="' . ( esc_attr( isset( $args['value'] ) ? $args['value'] : get_option( $args['name'] ) ) ) . '"';
		$placeholder = isset( $args['placeholder'] ) ? ( 'placeholder="' . esc_attr( $args['placeholder'] ) . '"' ) : '';
		$style       = isset( $args['style'] ) ? ( 'style="' . esc_attr( $args['style'] ) . '"' ) : '';
		$min         = isset( $args['min'] ) ? ( 'min="' . esc_attr( $args['min'] ) . '"' ) : '';
		$max         = isset( $args['max'] ) ? ( 'max="' . esc_attr( $args['max'] ) . '"' ) : '';
		?>
        <input <?= "{$type} {$name} {$value} {$placeholder} {$style} {$min} {$max}"; ?>/>
		<?php if ( isset( $args['description'] ) ) : ?>
            <label><?= esc_html( $args['description'] ); ?></label>
		<?php endif;
	}

	public static function renderSelect( $args ) {
		$value = get_option( $args['name'] );
		?>
        <select name="<?= esc_attr( $args['name'] ); ?>">
			<?php
			foreach ( $args['options'] as $option ) { ?>
                <option value="<?= esc_attr( $option['value'] ); ?>" <?php selected( $value, $option['value'] ); ?>>
					<?= esc_html( $option['label'] ); ?>
                </option>
			<?php } ?>
        </select>
		<?php
	}
}
