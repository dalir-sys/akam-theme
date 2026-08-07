<?php
/**
 * Ajax WooCommerce product reviews widget helpers.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

/**
 * Check whether a product can use the reviews widget.
 *
 * @param int $product_id Product ID.
 * @return bool
 */
function webmz_wc_reviews_widget_is_valid_product( $product_id ) {
	$product_id = absint( $product_id );

	if ( ! $product_id || 'product' !== get_post_type( $product_id ) ) {
		return false;
	}

	if ( ! function_exists( 'wc_get_product' ) ) {
		return false;
	}

	$product = wc_get_product( $product_id );

	return $product instanceof WC_Product && $product->get_id() > 0;
}

/**
 * Whether star ratings are enabled for WooCommerce reviews.
 *
 * @return bool
 */
function webmz_wc_reviews_widget_ratings_enabled() {
	if ( function_exists( 'wc_review_ratings_enabled' ) ) {
		return wc_review_ratings_enabled();
	}

	return 'yes' === get_option( 'woocommerce_enable_review_rating', 'yes' );
}

/**
 * Whether a rating is required when submitting a review.
 *
 * @return bool
 */
function webmz_wc_reviews_widget_rating_required() {
	return 'yes' === get_option( 'woocommerce_review_rating_required', 'yes' );
}

/**
 * Whether only verified buyers can leave reviews.
 *
 * @return bool
 */
function webmz_wc_reviews_widget_verification_required() {
	return 'yes' === get_option( 'woocommerce_review_rating_verification_required', 'no' );
}

/**
 * Check if the current visitor can submit a review for a product.
 *
 * @param int    $product_id Product ID.
 * @param int    $user_id    User ID.
 * @param string $email      Commenter email.
 * @return bool
 */
function webmz_wc_reviews_widget_can_review( $product_id, $user_id = 0, $email = '' ) {
	$product_id = absint( $product_id );
	$user_id    = absint( $user_id );
	$email      = sanitize_email( $email );

	if ( ! webmz_wc_reviews_widget_is_valid_product( $product_id ) ) {
		return false;
	}

	if ( ! comments_open( $product_id ) ) {
		return false;
	}

	$product = wc_get_product( $product_id );
	if ( ! $product || ! $product->get_reviews_allowed() ) {
		return false;
	}

	if ( 'yes' !== get_option( 'woocommerce_enable_reviews', 'yes' ) ) {
		return false;
	}

	if ( webmz_wc_reviews_widget_verification_required() && function_exists( 'wc_customer_bought_product' ) ) {
		if ( ! wc_customer_bought_product( $email, $user_id, $product_id ) ) {
			return false;
		}
	}

	return true;
}

/**
 * Get approved product review comments.
 *
 * @param int $product_id Product ID.
 * @return array<int,WP_Comment>
 */
function webmz_wc_reviews_widget_get_comments( $product_id ) {
	$comments = get_comments(
		array(
			'post_id' => absint( $product_id ),
			'status'  => 'approve',
			'orderby' => 'comment_date_gmt',
			'order'   => 'ASC',
		)
	);

	if ( ! is_array( $comments ) ) {
		return array();
	}

	return array_values(
		array_filter(
			$comments,
			static function ( $comment ) {
				return $comment instanceof WP_Comment && in_array( $comment->comment_type, array( 'review', 'comment', '' ), true );
			}
		)
	);
}

/**
 * Get approved review count for a product.
 *
 * @param int $product_id Product ID.
 * @return int
 */
function webmz_wc_reviews_widget_count( $product_id ) {
	if ( function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( absint( $product_id ) );
		if ( $product instanceof WC_Product ) {
			return absint( $product->get_review_count() );
		}
	}

	$count = get_comments(
		array(
			'post_id' => absint( $product_id ),
			'status'  => 'approve',
			'type'    => 'review',
			'count'   => true,
		)
	);

	return absint( $count );
}

/**
 * Get average product rating.
 *
 * @param int $product_id Product ID.
 * @return float
 */
function webmz_wc_reviews_widget_average_rating( $product_id ) {
	if ( function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( absint( $product_id ) );
		if ( $product instanceof WC_Product ) {
			return (float) $product->get_average_rating();
		}
	}

	return 0.0;
}

/**
 * Render star rating markup.
 *
 * @param float $rating Rating value.
 * @param int   $max    Maximum stars.
 * @return string
 */
function webmz_wc_reviews_widget_render_stars( $rating, $max = 5 ) {
	$rating = max( 0, min( (float) $max, (float) $rating ) );
	$max    = max( 1, absint( $max ) );

	ob_start();
	?>
	<div class="webmz-wc-review-stars" aria-label="<?php echo esc_attr( sprintf( __( 'امتیاز %1$s از %2$s', 'tadris' ), $rating, $max ) ); ?>">
		<?php for ( $i = 1; $i <= $max; $i++ ) : ?>
			<span class="webmz-wc-review-star <?php echo $rating >= $i ? 'is-filled' : ''; ?>" aria-hidden="true">★</span>
		<?php endfor; ?>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Render one review comment and its children.
 *
 * @param WP_Comment                    $comment    Comment object.
 * @param array<int,array<int,WP_Comment>> $tree       Comment tree.
 * @param int                           $product_id Product ID.
 * @param int                           $depth      Depth.
 * @return string
 */
function webmz_wc_reviews_widget_render_comment( $comment, $tree, $product_id, $depth = 0 ) {
	$comment_id = absint( $comment->comment_ID );
	$author     = get_comment_author( $comment );
	$avatar     = get_avatar_url( $comment, array( 'size' => 96 ) );
	$counts     = function_exists( 'webmz_comments_widget_get_vote_counts' ) ? webmz_comments_widget_get_vote_counts( $comment_id ) : array( 'like' => 0, 'dislike' => 0, 'user_vote' => '' );
	$is_mine    = get_current_user_id() && absint( $comment->user_id ) === get_current_user_id();
	$is_review  = 'review' === $comment->comment_type;
	$rating     = $is_review ? absint( get_comment_meta( $comment_id, 'rating', true ) ) : 0;
	$classes    = array( 'webmz-comment-chat-item' );

	if ( $is_mine ) {
		$classes[] = 'is-mine';
	}
	if ( $is_review ) {
		$classes[] = 'is-review';
	}
	if ( $depth > 0 ) {
		$classes[] = 'is-reply';
	}

	ob_start();
	?>
	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-comment-id="<?php echo esc_attr( $comment_id ); ?>">
		<div class="webmz-comment-chat-avatar">
			<img src="<?php echo esc_url( $avatar ); ?>" alt="<?php echo esc_attr( $author ); ?>" loading="lazy" decoding="async">
		</div>

		<div class="webmz-comment-chat-main">
			<div class="webmz-comment-chat-bubble">
				<div class="webmz-comment-chat-head">
					<div class="webmz-comment-chat-author-wrap">
						<strong class="webmz-comment-chat-author"><?php echo esc_html( $author ); ?></strong>
						<?php if ( $is_review && $rating && webmz_wc_reviews_widget_ratings_enabled() ) : ?>
							<?php echo webmz_wc_reviews_widget_render_stars( $rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endif; ?>
					</div>
					<span class="webmz-comment-chat-date"><?php echo esc_html( function_exists( 'webmz_comments_widget_comment_time' ) ? webmz_comments_widget_comment_time( $comment ) : get_comment_date( '', $comment ) ); ?></span>
				</div>

				<div class="webmz-comment-chat-text">
					<?php echo wp_kses_post( wpautop( get_comment_text( $comment ) ) ); ?>
				</div>
			</div>

			<div class="webmz-comment-chat-actions">
				<?php if ( comments_open( $product_id ) ) : ?>
					<button type="button" class="webmz-comment-reply-button" data-comment-id="<?php echo esc_attr( $comment_id ); ?>" data-author="<?php echo esc_attr( $author ); ?>">
						<?php esc_html_e( 'پاسخ', 'tadris' ); ?>
					</button>
				<?php endif; ?>

				<button type="button" class="webmz-comment-vote-button <?php echo 'like' === $counts['user_vote'] ? 'is-active' : ''; ?>" data-comment-id="<?php echo esc_attr( $comment_id ); ?>" data-vote-type="like" aria-label="<?php esc_attr_e( 'لایک', 'tadris' ); ?>">
					<span aria-hidden="true">👍</span>
					<strong><?php echo esc_html( number_format_i18n( $counts['like'] ) ); ?></strong>
				</button>

				<button type="button" class="webmz-comment-vote-button <?php echo 'dislike' === $counts['user_vote'] ? 'is-active' : ''; ?>" data-comment-id="<?php echo esc_attr( $comment_id ); ?>" data-vote-type="dislike" aria-label="<?php esc_attr_e( 'دیسلایک', 'tadris' ); ?>">
					<span aria-hidden="true">👎</span>
					<strong><?php echo esc_html( number_format_i18n( $counts['dislike'] ) ); ?></strong>
				</button>
			</div>

			<?php if ( ! empty( $tree[ $comment_id ] ) ) : ?>
				<div class="webmz-comment-chat-replies">
					<?php
					foreach ( $tree[ $comment_id ] as $child ) {
						echo webmz_wc_reviews_widget_render_comment( $child, $tree, $product_id, $depth + 1 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Render the complete review list.
 *
 * @param int $product_id Product ID.
 * @return string
 */
function webmz_wc_reviews_widget_render_list( $product_id ) {
	$comments = webmz_wc_reviews_widget_get_comments( $product_id );
	$tree     = function_exists( 'webmz_comments_widget_build_tree' ) ? webmz_comments_widget_build_tree( $comments ) : array();

	ob_start();
	?>
	<div class="webmz-comments-list-inner">
		<?php if ( empty( $comments ) ) : ?>
			<div class="webmz-comments-empty">
				<span><?php esc_html_e( 'هنوز نظری ثبت نشده است. اولین نفر باشید.', 'tadris' ); ?></span>
			</div>
		<?php else : ?>
			<?php
			foreach ( $tree[0] ?? array() as $comment ) {
				if ( 'review' !== $comment->comment_type ) {
					continue;
				}
				echo webmz_wc_reviews_widget_render_comment( $comment, $tree, $product_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Return json error for WooCommerce reviews widget ajax.
 *
 * @param string $message Error message.
 * @param int    $status  HTTP status.
 * @return void
 */
function webmz_wc_reviews_widget_json_error( $message, $status = 400 ) {
	wp_send_json_error(
		array(
			'message' => $message,
		),
		$status
	);
}

/**
 * Verify ajax nonce.
 *
 * @return void
 */
function webmz_wc_reviews_widget_verify_nonce() {
	$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

	if ( ! wp_verify_nonce( $nonce, 'webmz_wc_reviews_widget' ) ) {
		webmz_wc_reviews_widget_json_error( esc_html__( 'درخواست نامعتبر است. صفحه را تازه‌سازی کنید.', 'tadris' ), 403 );
	}
}

/**
 * Clear WooCommerce product review transients.
 *
 * @param int $product_id Product ID.
 * @return void
 */
function webmz_wc_reviews_widget_clear_transients( $product_id ) {
	$product_id = absint( $product_id );

	if ( class_exists( 'WC_Comments' ) && method_exists( 'WC_Comments', 'clear_transients' ) ) {
		WC_Comments::clear_transients( $product_id );
		return;
	}

	delete_transient( 'wc_product_reviews_' . $product_id );
	delete_transient( 'wc_average_rating_' . $product_id );
	delete_transient( 'wc_rating_count_' . $product_id );
}

/**
 * Ajax: load reviews.
 *
 * @return void
 */
function webmz_wc_reviews_widget_ajax_load() {
	webmz_wc_reviews_widget_verify_nonce();

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;

	if ( ! webmz_wc_reviews_widget_is_valid_product( $product_id ) ) {
		webmz_wc_reviews_widget_json_error( esc_html__( 'محصول معتبر نیست.', 'tadris' ) );
	}

	wp_send_json_success(
		array(
			'html'            => webmz_wc_reviews_widget_render_list( $product_id ),
			'count'           => webmz_wc_reviews_widget_count( $product_id ),
			'average_rating'  => webmz_wc_reviews_widget_average_rating( $product_id ),
		)
	);
}
add_action( 'wp_ajax_webmz_wc_reviews_widget_load', 'webmz_wc_reviews_widget_ajax_load' );
add_action( 'wp_ajax_nopriv_webmz_wc_reviews_widget_load', 'webmz_wc_reviews_widget_ajax_load' );

/**
 * Ajax: submit a new review/reply.
 *
 * @return void
 */
function webmz_wc_reviews_widget_ajax_submit() {
	webmz_wc_reviews_widget_verify_nonce();

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$parent     = isset( $_POST['parent'] ) ? absint( $_POST['parent'] ) : 0;
	$content    = isset( $_POST['content'] ) ? trim( wp_kses_post( wp_unslash( $_POST['content'] ) ) ) : '';
	$rating     = isset( $_POST['rating'] ) ? absint( $_POST['rating'] ) : 0;

	if ( ! webmz_wc_reviews_widget_is_valid_product( $product_id ) ) {
		webmz_wc_reviews_widget_json_error( esc_html__( 'محصول معتبر نیست.', 'tadris' ) );
	}

	if ( '' === $content ) {
		webmz_wc_reviews_widget_json_error( esc_html__( 'متن نظر را وارد کنید.', 'tadris' ) );
	}

	if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) {
		webmz_wc_reviews_widget_json_error( esc_html__( 'برای ثبت نظر ابتدا وارد حساب کاربری شوید.', 'tadris' ), 401 );
	}

	$user_id = get_current_user_id();

	if ( $user_id ) {
		$user   = wp_get_current_user();
		$author = $user->display_name ? $user->display_name : $user->user_login;
		$email  = $user->user_email;
		$url    = $user->user_url;
	} else {
		$author = isset( $_POST['author'] ) ? sanitize_text_field( wp_unslash( $_POST['author'] ) ) : '';
		$email  = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$url    = '';

		if ( get_option( 'require_name_email' ) && ( '' === $author || '' === $email || ! is_email( $email ) ) ) {
			webmz_wc_reviews_widget_json_error( esc_html__( 'نام و ایمیل معتبر را وارد کنید.', 'tadris' ) );
		}
	}

	if ( $parent ) {
		$parent_comment = get_comment( $parent );
		if ( ! $parent_comment || absint( $parent_comment->comment_post_ID ) !== $product_id || '1' !== (string) $parent_comment->comment_approved ) {
			webmz_wc_reviews_widget_json_error( esc_html__( 'نظر والد معتبر نیست.', 'tadris' ) );
		}

		if ( ! comments_open( $product_id ) ) {
			webmz_wc_reviews_widget_json_error( esc_html__( 'ثبت پاسخ برای این محصول بسته شده است.', 'tadris' ) );
		}

		$comment_type = 'comment';
		$rating       = 0;
	} else {
		if ( ! webmz_wc_reviews_widget_can_review( $product_id, $user_id, $email ) ) {
			if ( webmz_wc_reviews_widget_verification_required() ) {
				webmz_wc_reviews_widget_json_error( esc_html__( 'فقط خریداران تأییدشده می‌توانند نظر ثبت کنند.', 'tadris' ) );
			}
			webmz_wc_reviews_widget_json_error( esc_html__( 'امکان ثبت نظر برای این محصول وجود ندارد.', 'tadris' ) );
		}

		if ( webmz_wc_reviews_widget_ratings_enabled() && webmz_wc_reviews_widget_rating_required() && ( $rating < 1 || $rating > 5 ) ) {
			webmz_wc_reviews_widget_json_error( esc_html__( 'لطفاً امتیاز خود را انتخاب کنید.', 'tadris' ) );
		}

		if ( ! webmz_wc_reviews_widget_ratings_enabled() ) {
			$rating = 0;
		} elseif ( $rating < 1 || $rating > 5 ) {
			$rating = 0;
		}

		$comment_type = 'review';
	}

	$comment_data = array(
		'comment_post_ID'      => $product_id,
		'comment_author'       => $author,
		'comment_author_email' => $email,
		'comment_author_url'   => $url,
		'comment_content'      => $content,
		'comment_type'         => $comment_type,
		'comment_parent'       => $parent,
		'user_id'              => $user_id,
	);

	$comment_id = wp_new_comment( wp_slash( $comment_data ), true );

	if ( is_wp_error( $comment_id ) ) {
		webmz_wc_reviews_widget_json_error( $comment_id->get_error_message() );
	}

	if ( 'review' === $comment_type && $rating > 0 ) {
		update_comment_meta( $comment_id, 'rating', $rating );
		webmz_wc_reviews_widget_clear_transients( $product_id );
	}

	$comment  = get_comment( $comment_id );
	$approved = $comment && '1' === (string) $comment->comment_approved;

	wp_send_json_success(
		array(
			'html'           => webmz_wc_reviews_widget_render_list( $product_id ),
			'count'          => webmz_wc_reviews_widget_count( $product_id ),
			'average_rating' => webmz_wc_reviews_widget_average_rating( $product_id ),
			'approved'       => $approved,
			'message'        => $approved ? esc_html__( 'نظر شما ثبت شد.', 'tadris' ) : esc_html__( 'نظر شما ثبت شد و پس از تأیید نمایش داده می‌شود.', 'tadris' ),
		)
	);
}
add_action( 'wp_ajax_webmz_wc_reviews_widget_submit', 'webmz_wc_reviews_widget_ajax_submit' );
add_action( 'wp_ajax_nopriv_webmz_wc_reviews_widget_submit', 'webmz_wc_reviews_widget_ajax_submit' );

/**
 * Ajax: like/dislike a review comment.
 *
 * @return void
 */
function webmz_wc_reviews_widget_ajax_vote() {
	webmz_wc_reviews_widget_verify_nonce();

	$comment_id = isset( $_POST['comment_id'] ) ? absint( $_POST['comment_id'] ) : 0;
	$vote_type  = isset( $_POST['vote_type'] ) ? sanitize_key( wp_unslash( $_POST['vote_type'] ) ) : '';

	if ( ! in_array( $vote_type, array( 'like', 'dislike' ), true ) ) {
		webmz_wc_reviews_widget_json_error( esc_html__( 'نوع رأی معتبر نیست.', 'tadris' ) );
	}

	$comment = get_comment( $comment_id );
	if ( ! $comment || '1' !== (string) $comment->comment_approved ) {
		webmz_wc_reviews_widget_json_error( esc_html__( 'نظر معتبر نیست.', 'tadris' ) );
	}

	if ( ! webmz_wc_reviews_widget_is_valid_product( $comment->comment_post_ID ) ) {
		webmz_wc_reviews_widget_json_error( esc_html__( 'نظر متعلق به محصول معتبر نیست.', 'tadris' ) );
	}

	if ( ! function_exists( 'webmz_comments_widget_get_voter_key' ) || ! function_exists( 'webmz_comments_widget_get_votes' ) || ! function_exists( 'webmz_comments_widget_get_vote_counts' ) ) {
		webmz_wc_reviews_widget_json_error( esc_html__( 'سیستم رأی‌گیری در دسترس نیست.', 'tadris' ) );
	}

	$voter_key = webmz_comments_widget_get_voter_key();
	$votes     = webmz_comments_widget_get_votes( $comment_id );

	if ( isset( $votes[ $voter_key ] ) && $votes[ $voter_key ] === $vote_type ) {
		unset( $votes[ $voter_key ] );
	} else {
		$votes[ $voter_key ] = $vote_type;
	}

	if ( defined( 'WEBMZ_COMMENT_WIDGET_VOTE_META' ) ) {
		update_comment_meta( $comment_id, WEBMZ_COMMENT_WIDGET_VOTE_META, $votes );
	}

	wp_send_json_success( webmz_comments_widget_get_vote_counts( $comment_id ) );
}
add_action( 'wp_ajax_webmz_wc_reviews_widget_vote', 'webmz_wc_reviews_widget_ajax_vote' );
add_action( 'wp_ajax_nopriv_webmz_wc_reviews_widget_vote', 'webmz_wc_reviews_widget_ajax_vote' );
