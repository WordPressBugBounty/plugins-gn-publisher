<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This controller is responsible for handling yandex turbo
 *
 * @since 1.5.27
 */
class GNPUB_Yandex_Turbo {

	public function __construct() {

		add_action( 'gnpub_render_yandex_turbo_compatibility', [ $this, 'render_yandex_turbo' ] );
		add_action( 'gnpub_render_compatibility_modals', [ $this, 'render_configure_modal' ] );

	}

	public function render_yandex_turbo() {

		$gnpub_options       = get_option( 'gnpub_new_options' );
		$gnpub_yandex_turbo  = isset( $gnpub_options['gnpub_yandex_turbo'] ) ? $gnpub_options['gnpub_yandex_turbo'] : false;
		$type                = $gnpub_yandex_turbo ? 'hidden' : 'checkbox';

		?>
		<tr>
			<th>
				<label for="gnpub_yandex_turbo" class="gnpub-hover-pointer">
					<?php echo esc_html__( 'Yandex Turbo', 'gn-publisher' ); ?>
				</label>
			</th>
			<td>

				<input 
					type="<?php echo esc_attr( $type ); ?>"
					name="gnpub_yandex_turbo"
					id="gnpub_yandex_turbo"
					class="gnpub_compatibility_options"
					<?php checked( $gnpub_yandex_turbo, true ); ?>
					value="1"
					data-id="gnpub_yandex_turbo"
				/>

				<label
					for="gnpub_yandex_turbo"
					id="gnpub_yandex_turbo_label"
					data-unchecked="<?php esc_attr_e( 'Enable yandex turbo compability in GN publisher feeds.', 'gn-publisher' ); ?>"
					data-checked="<?php esc_attr_e( '<b>Enabled</b>', 'gn-publisher' ); ?>"
				>
					<?php
					if ( ! $gnpub_yandex_turbo ) {
						echo esc_html__( 'Enable yandex turbo compability in GN publisher feeds.', 'gn-publisher' );
					} else {
						echo '<b>' . esc_html__( 'Enabled', 'gn-publisher' ) . '</b>';
					}
					?>
				</label>

				<a
					class="gnpub_config_button"
					id="gnpub_yandex_turbo_config"
					<?php if ( ! $gnpub_yandex_turbo ) { echo 'style="display:none;"'; } ?>
				>
					<?php echo esc_html__( 'Configure Settings ', 'gn-publisher' ); ?>
				</a>

				<a
					class="gnpub_yandex_turbo_link gnpub_compatibility_options_disable"
					id="gnpub_yandex_turbo_link"
					data-id="gnpub_yandex_turbo"
					<?php if ( ! $gnpub_yandex_turbo ) { echo 'style="display:none;"'; } ?>
				>
					<?php echo esc_html__( 'Disable', 'gn-publisher' ); ?>
				</a>

				<label>
				    <div class="gn-compat-note">
				        <?php
				        printf(
				            /* translators: %s: Yandex Turbo link */
				            __( 'Use this option when you need feeds for %s', 'gn-publisher' ),
				            '<a href="https://webmaster.yandex.com/" target="_blank">' . esc_html__( 'Yandex Turbo', 'gn-publisher' ) . '</a>'
				        );
				        ?>
				    </div>
				</label>

			</td>
		</tr>
		<?php
	}

	public function render_configure_modal() {

		$gnpub_options       = get_option( 'gnpub_new_options' );
		$permalinks_enabled  = ! empty( get_option( 'permalink_structure' ) );
		$gnpub_yandex_turbo  = isset( $gnpub_options['gnpub_yandex_turbo'] ) ? $gnpub_options['gnpub_yandex_turbo'] : false;
		$categories 		 = get_categories();

		$feed_url = esc_url(
			$permalinks_enabled
			? trailingslashit( home_url() ) . 'feed/gnturbo'
			: add_query_arg( 'feed', 'gnturbo', home_url() )
		);
		?>
		<div id="gnpub_modal_yandex_turbo" class="gnpub_comp_modal">
			
				<div class="gnpub_comp_modal-content">

					<span id="gnpub_yandex_turbo_close" class="gnpub_comp_close">&times;</span>

					<p><strong><?php echo esc_html__( 'Yandex Turbo RSS feeds :', 'gn-publisher' ); ?></strong></p>

					<ul>

						<li>
							<input
								type="text"
								class="gn-input"
								value="<?php echo esc_url( $feed_url ); ?>"
								id="gnpub-yandex-turbo-feed-0"
								size="60"
								readonly
							>

							<div class="gn-tooltip">
								<button
									class="gn-btn"
									onclick="gn_copy('gnpub-yandex-turbo-feed-0')"
									onmouseout="gn_out('gnpub-yandex-turbo-feed-0')"
								>
									<span
										class="gn-tooltiptext"
										id="gnpub-yandex-turbo-feed-0-tooltip"
									>
										<?php echo esc_html__( 'Copy URL', 'gn-publisher' ); ?>
									</span>

									<?php echo esc_html__( 'Copy', 'gn-publisher' ); ?>
								</button>
							</div>
						</li>

						<?php foreach ( $categories as $category ) :

							$gn_category_link = get_category_link( $category->term_id );

							$gn_category_link = str_replace( '/./', '/', $gn_category_link );

							$permalink_structure = get_option( 'permalink_structure' );

							if (
								defined( 'WPSEO_VERSION' ) &&
								is_callable( [ 'WPSEO_Options', 'get' ] ) &&
								WPSEO_Options::get( 'stripcategorybase' ) == true &&
								! empty( $permalink_structure )
							) {

								$permalink_prepend = '';

								if ( strlen( $permalink_structure ) > 3 ) {

									$permalink_array = explode( '/%', $permalink_structure );

									if ( $permalink_array && count( $permalink_array ) > 1 ) {

										$permalink_prepend = trailingslashit( $permalink_array[0] );
									}
								}

								$gn_category_link = str_replace( $permalink_prepend, '/', $gn_category_link );
							}

							$gn_category_link = $permalinks_enabled
								? trailingslashit( $gn_category_link ) . 'feed/gnturbo'
								: add_query_arg( 'feed', 'gnturbo', $gn_category_link );

							?>

							<li>

								<input
									type="text"
									class="gn-input"
									value="<?php echo esc_url( $gn_category_link ); ?>"
									id="<?php echo 'gnpub-yandex-turbo-feed-' . esc_attr( $category->term_id ); ?>"
									size="60"
									readonly
								>

								<div class="gn-tooltip">

									<button
										class="gn-btn"
										onclick="gn_copy('<?php echo 'gnpub-yandex-turbo-feed-' . esc_attr( $category->term_id ); ?>')"
										onmouseout="gn_out('<?php echo 'gnpub-yandex-turbo-feed-' . esc_attr( $category->term_id ); ?>')"
									>

										<span
											class="gn-tooltiptext"
											id="<?php echo 'gnpub-yandex-turbo-feed-' . esc_attr( $category->term_id ) . '-tooltip'; ?>"
										>
											<?php echo esc_html__( 'Copy URL', 'gn-publisher' ); ?>
										</span>

										<?php echo esc_html__( 'Copy', 'gn-publisher' ); ?>

									</button>

								</div>

							</li>

						<?php endforeach; ?>

					</ul>
				</div>

		</div>

		<?php

	}

}

new GNPUB_Yandex_Turbo();