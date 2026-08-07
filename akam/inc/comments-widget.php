<?php
/**
 * Ajax comments widget helpers.
 *
 * @package WebMZ
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WEBMZ_COMMENT_WIDGET_VOTE_META' ) ) {
	define( 'WEBMZ_COMMENT_WIDGET_VOTE_META', '_webmz_comment_votes' );
}

/**
 * Check whether a post can use the comments widget.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function webmz_comments_widget_is_valid_post( $post_id ) {
	$post_id = absint( $post_id );

	if ( ! $post_id ) {
		return false;
	}

	$post_type = get_post_type( $post_id );
	$allowed   = array( 'post' );

	if ( defined( 'WEBMZ_TEACHER_POST_TYPE' ) ) {
		$allowed[] = WEBMZ_TEACHER_POST_TYPE;
	}

	return in_array( $post_type, $allowed, true );
}

/**
 * Get a stable voter key for the current visitor.
 *
 * @return string
 */
function webmz_comments_widget_get_voter_key() {
	$user_id = get_current_user_id();

	if ( $user_id ) {
		return 'user_' . absint( $user_id );
	}

	$ip = '';
	if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
	}

	$user_agent = '';
	if ( ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
		$user_agent = substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 190 );
	}

	$salt = defined( 'AUTH_SALT' ) ? AUTH_SALT : wp_salt( 'auth' );

	return 'guest_' . hash_hmac( 'sha256', $ip . '|' . $user_agent, $salt );
}

/**
 * Get all votes of a comment.
 *
 * @param int $comment_id Comment ID.
 * @return array<string,string>
 */
function webmz_comments_widget_get_votes( $comment_id ) {
	$votes = get_comment_meta( absint( $comment_id ), WEBMZ_COMMENT_WIDGET_VOTE_META, true );

	if ( ! is_array( $votes ) ) {
		return array();
	}

	$clean = array();
	foreach ( $votes as $key => $value ) {
		$key   = sanitize_key( (string) $key );
		$value = sanitize_key( (string) $value );
		if ( in_array( $value, array( 'like', 'dislike' ), true ) ) {
			$clean[ $key ] = $value;
		}
	}

	return $clean;
}

/**
 * Count comment votes.
 *
 * @param int $comment_id Comment ID.
 * @return array{like:int,dislike:int,user_vote:string}
 */
function webmz_comments_widget_get_vote_counts( $comment_id ) {
	$votes     = webmz_comments_widget_get_votes( $comment_id );
	$voter_key = webmz_comments_widget_get_voter_key();
	$likes     = 0;
	$dislikes  = 0;

	foreach ( $votes as $vote ) {
		if ( 'like' === $vote ) {
			$likes++;
		} elseif ( 'dislike' === $vote ) {
			$dislikes++;
		}
	}

	return array(
		'like'      => $likes,
		'dislike'   => $dislikes,
		'user_vote' => isset( $votes[ $voter_key ] ) ? $votes[ $voter_key ] : '',
	);
}

/**
 * Build a comment tree grouped by parent ID.
 *
 * @param array<int,WP_Comment> $comments Comments.
 * @return array<int,array<int,WP_Comment>>
 */
function webmz_comments_widget_build_tree( $comments ) {
	$tree = array();

	foreach ( $comments as $comment ) {
		$parent = absint( $comment->comment_parent );
		if ( ! isset( $tree[ $parent ] ) ) {
			$tree[ $parent ] = array();
		}
		$tree[ $parent ][] = $comment;
	}

	return $tree;
}

/**
 * Get approved comments for the widget.
 *
 * @param int $post_id Post ID.
 * @return array<int,WP_Comment>
 */
function webmz_comments_widget_get_comments( $post_id ) {
	$comments = get_comments(
		array(
			'post_id' => absint( $post_id ),
			'status'  => 'approve',
			'orderby' => 'comment_date_gmt',
			'order'   => 'ASC',
			'type'    => 'comment',
		)
	);

	return is_array( $comments ) ? $comments : array();
}

/**
 * Format a comment date in a short human-readable way.
 *
 * @param WP_Comment $comment Comment object.
 * @return string
 */
function webmz_comments_widget_comment_time( $comment ) {
	$timestamp = strtotime( $comment->comment_date_gmt . ' GMT' );

	if ( ! $timestamp ) {
		return get_comment_date( '', $comment );
	}

	return sprintf(
		/* translators: %s: human time difference. */
		esc_html__( '%s پیش', 'tadris' ),
		human_time_diff( $timestamp, current_time( 'timestamp', true ) )
	);
}

/**
 * Render one comment and its children.
 *
 * @param WP_Comment                    $comment Comment object.
 * @param array<int,array<int,WP_Comment>> $tree    Comment tree.
 * @param int                           $post_id Post ID.
 * @param int                           $depth   Depth.
 * @return string
 */
function webmz_comments_widget_render_comment( $comment, $tree, $post_id, $depth = 0 ) {
	$comment_id = absint( $comment->comment_ID );
	$author     = get_comment_author( $comment );
	$avatar     = get_avatar_url( $comment, array( 'size' => 96 ) );
	$counts     = webmz_comments_widget_get_vote_counts( $comment_id );
	$is_mine    = get_current_user_id() && absint( $comment->user_id ) === get_current_user_id();
	$is_author  = absint( $comment->user_id ) && absint( get_post_field( 'post_author', $post_id ) ) === absint( $comment->user_id );
	$classes    = array( 'webmz-comment-chat-item' );

	if ( $is_mine ) {
		$classes[] = 'is-mine';
	}
	if ( $is_author ) {
		$classes[] = 'is-post-author';
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
						<?php if ( $is_author ) : ?>
							<span class="webmz-comment-chat-badge"><?php esc_html_e( 'نویسنده', 'tadris' ); ?></span>
						<?php endif; ?>
					</div>
					<span class="webmz-comment-chat-date"><?php echo esc_html( webmz_comments_widget_comment_time( $comment ) ); ?></span>
				</div>

				<div class="webmz-comment-chat-text">
					<?php echo wp_kses_post( wpautop( get_comment_text( $comment ) ) ); ?>
				</div>
			</div>

			<div class="webmz-comment-chat-actions">
				<button type="button" class="webmz-comment-reply-button" data-comment-id="<?php echo esc_attr( $comment_id ); ?>" data-author="<?php echo esc_attr( $author ); ?>">
					<?php esc_html_e( 'پاسخ', 'tadris' ); ?>
				</button>

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
						echo webmz_comments_widget_render_comment( $child, $tree, $post_id, $depth + 1 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
 * Render the complete comment list.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function webmz_comments_widget_render_list( $post_id ) {
	$comments = webmz_comments_widget_get_comments( $post_id );
	$tree     = webmz_comments_widget_build_tree( $comments );

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
				echo webmz_comments_widget_render_comment( $comment, $tree, $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Get approved comment count.
 *
 * @param int $post_id Post ID.
 * @return int
 */
function webmz_comments_widget_count( $post_id ) {
	$count = get_comments(
		array(
			'post_id' => absint( $post_id ),
			'status'  => 'approve',
			'type'    => 'comment',
			'count'   => true,
		)
	);

	return absint( $count );
}

/**
 * Render the AJAX comments widget markup.
 *
 * @param int                 $post_id  Post ID.
 * @param array<string,mixed> $args     Display args.
 * @return string
 */
function webmz_render_comments_widget( $post_id, $args = array() ) {
	$post_id = absint( $post_id );

	if ( ! webmz_comments_widget_is_valid_post( $post_id ) ) {
		return '';
	}

	$args = wp_parse_args(
		$args,
		array(
			'widget_title'         => esc_html__( 'گفت‌وگو و دیدگاه‌ها', 'tadris' ),
			'show_count'           => true,
			'form_title'           => esc_html__( 'دیدگاه خود را بنویسید', 'tadris' ),
			'textarea_placeholder' => esc_html__( 'نظر خود را بنویسید...', 'tadris' ),
			'submit_text'          => esc_html__( 'ارسال دیدگاه', 'tadris' ),
			'wrapper_class'        => '',
		)
	);

	$count          = webmz_comments_widget_count( $post_id );
	$comments_html  = webmz_comments_widget_render_list( $post_id );
	$comments_open  = comments_open( $post_id );
	$must_login     = get_option( 'comment_registration' ) && ! is_user_logged_in();
	$require_fields = ! is_user_logged_in() && get_option( 'require_name_email' );
	$wrapper_class  = trim( 'webmz-comments-widget ' . (string) $args['wrapper_class'] );

	ob_start();
	?>
	<section class="<?php echo esc_attr( $wrapper_class ); ?>" data-webmz-comments-widget data-post-id="<?php echo esc_attr( $post_id ); ?>">
		<header class="webmz-comments-header">
			<div>
				<h3 class="webmz-comments-title"><?php echo esc_html( $args['widget_title'] ); ?></h3>
				<p class="webmz-comments-subtitle"><?php esc_html_e( 'دیدگاه‌ها به صورت گفت‌وگو نمایش داده می‌شوند.', 'tadris' ); ?></p>
			</div>

			<?php if ( ! empty( $args['show_count'] ) ) : ?>
				<span class="webmz-comments-count" data-webmz-comments-count><?php echo esc_html( number_format_i18n( $count ) ); ?></span>
			<?php endif; ?>
		</header>

		<div class="webmz-comments-list" data-webmz-comments-list>
			<?php echo $comments_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>

		<?php if ( $comments_open ) : ?>
			<div class="webmz-comments-form-card">
				<?php if ( $must_login ) : ?>
					<div class="webmz-comments-login-required">
						<?php
						echo wp_kses_post(
							sprintf(
								/* translators: %s: login link. */
								esc_html__( 'برای ثبت دیدگاه باید %s.', 'tadris' ),
								'<a href="' . esc_url( wp_login_url( get_permalink( $post_id ) ) ) . '">' . esc_html__( 'وارد حساب کاربری شوید', 'tadris' ) . '</a>'
							)
						);
						?>
					</div>
				<?php else : ?>
					<form class="webmz-comments-form" data-webmz-comments-form>
						<input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>">
						<input type="hidden" name="parent" value="0" data-webmz-comment-parent>

						<div class="webmz-comments-form-head">
							<strong><?php echo esc_html( $args['form_title'] ); ?></strong>
							<div class="webmz-comments-replying" data-webmz-replying hidden>
								<span></span>
								<button type="button" data-webmz-cancel-reply><?php esc_html_e( 'لغو پاسخ', 'tadris' ); ?></button>
							</div>
						</div>

						<?php if ( $require_fields ) : ?>
							<div class="webmz-comments-guest-fields">
								<label>
									<span><?php esc_html_e( 'نام شما', 'tadris' ); ?></span>
									<input type="text" name="author" autocomplete="name" required>
								</label>
								<label>
									<span><?php esc_html_e( 'ایمیل شما', 'tadris' ); ?></span>
									<input type="email" name="email" autocomplete="email" required>
								</label>
							</div>
						<?php endif; ?>

						<label class="webmz-comments-textarea-wrap">
							<span class="screen-reader-text"><?php esc_html_e( 'متن دیدگاه', 'tadris' ); ?></span>
							<textarea name="content" rows="5" placeholder="<?php echo esc_attr( $args['textarea_placeholder'] ); ?>" required></textarea>
						</label>

						<div class="webmz-comments-form-footer">
							<div class="webmz-comments-message" data-webmz-comments-message aria-live="polite"></div>
							<button type="submit" class="webmz-comments-submit">
								<span><?php echo esc_html( $args['submit_text'] ); ?></span>
							</button>
						</div>
					</form>
				<?php endif; ?>
			</div>
		<?php else : ?>
			<div class="webmz-comments-closed"><?php esc_html_e( 'دیدگاه‌ها برای این نوشته بسته شده‌اند.', 'tadris' ); ?></div>
		<?php endif; ?>
	</section>
	<?php
	return (string) ob_get_clean();
}

/**
 * Return json error for comment widget ajax.
 *
 * @param string $message Error message.
 * @param int    $status  HTTP status.
 * @return void
 */
function webmz_comments_widget_json_error( $message, $status = 400 ) {
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
function webmz_comments_widget_verify_nonce() {
	$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

	if ( ! wp_verify_nonce( $nonce, 'webmz_comments_widget' ) ) {
		webmz_comments_widget_json_error( esc_html__( 'درخواست نامعتبر است. صفحه را تازه‌سازی کنید.', 'tadris' ), 403 );
	}
}

/**
 * Ajax: load comments.
 *
 * @return void
 */
function webmz_comments_widget_ajax_load() {
	webmz_comments_widget_verify_nonce();

	$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

	if ( ! webmz_comments_widget_is_valid_post( $post_id ) ) {
		webmz_comments_widget_json_error( esc_html__( 'نوشته معتبر نیست.', 'tadris' ) );
	}

	wp_send_json_success(
		array(
			'html'  => webmz_comments_widget_render_list( $post_id ),
			'count' => webmz_comments_widget_count( $post_id ),
		)
	);
}
add_action( 'wp_ajax_webmz_comments_widget_load', 'webmz_comments_widget_ajax_load' );
add_action( 'wp_ajax_nopriv_webmz_comments_widget_load', 'webmz_comments_widget_ajax_load' );

/**
 * Ajax: submit a new comment/reply.
 *
 * @return void
 */
function webmz_comments_widget_ajax_submit() {
	webmz_comments_widget_verify_nonce();

	$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
	$parent  = isset( $_POST['parent'] ) ? absint( $_POST['parent'] ) : 0;
	$content = isset( $_POST['content'] ) ? trim( wp_kses_post( wp_unslash( $_POST['content'] ) ) ) : '';

	if ( ! webmz_comments_widget_is_valid_post( $post_id ) ) {
		webmz_comments_widget_json_error( esc_html__( 'نوشته معتبر نیست.', 'tadris' ) );
	}

	if ( ! comments_open( $post_id ) ) {
		webmz_comments_widget_json_error( esc_html__( 'دیدگاه‌ها برای این نوشته بسته شده‌اند.', 'tadris' ) );
	}

	if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) {
		webmz_comments_widget_json_error( esc_html__( 'برای ثبت دیدگاه ابتدا وارد حساب کاربری شوید.', 'tadris' ), 401 );
	}

	if ( '' === $content ) {
		webmz_comments_widget_json_error( esc_html__( 'متن دیدگاه را وارد کنید.', 'tadris' ) );
	}

	if ( $parent ) {
		$parent_comment = get_comment( $parent );
		if ( ! $parent_comment || absint( $parent_comment->comment_post_ID ) !== $post_id || '1' !== (string) $parent_comment->comment_approved ) {
			webmz_comments_widget_json_error( esc_html__( 'دیدگاه والد معتبر نیست.', 'tadris' ) );
		}
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
			webmz_comments_widget_json_error( esc_html__( 'نام و ایمیل معتبر را وارد کنید.', 'tadris' ) );
		}
	}

	$comment_data = array(
		'comment_post_ID'      => $post_id,
		'comment_author'       => $author,
		'comment_author_email' => $email,
		'comment_author_url'   => $url,
		'comment_content'      => $content,
		'comment_type'         => 'comment',
		'comment_parent'       => $parent,
		'user_id'              => $user_id,
	);

	$comment_id = wp_new_comment( wp_slash( $comment_data ), true );

	if ( is_wp_error( $comment_id ) ) {
		webmz_comments_widget_json_error( $comment_id->get_error_message() );
	}

	$comment  = get_comment( $comment_id );
	$approved = $comment && '1' === (string) $comment->comment_approved;

	wp_send_json_success(
		array(
			'html'     => webmz_comments_widget_render_list( $post_id ),
			'count'    => webmz_comments_widget_count( $post_id ),
			'approved' => $approved,
			'message'  => $approved ? esc_html__( 'دیدگاه شما ثبت شد.', 'tadris' ) : esc_html__( 'دیدگاه شما ثبت شد و پس از تأیید نمایش داده می‌شود.', 'tadris' ),
		)
	);
}
add_action( 'wp_ajax_webmz_comments_widget_submit', 'webmz_comments_widget_ajax_submit' );
add_action( 'wp_ajax_nopriv_webmz_comments_widget_submit', 'webmz_comments_widget_ajax_submit' );

/**
 * Ajax: like/dislike a comment.
 *
 * @return void
 */
function webmz_comments_widget_ajax_vote() {
	webmz_comments_widget_verify_nonce();

	$comment_id = isset( $_POST['comment_id'] ) ? absint( $_POST['comment_id'] ) : 0;
	$vote_type  = isset( $_POST['vote_type'] ) ? sanitize_key( wp_unslash( $_POST['vote_type'] ) ) : '';

	if ( ! in_array( $vote_type, array( 'like', 'dislike' ), true ) ) {
		webmz_comments_widget_json_error( esc_html__( 'نوع رأی معتبر نیست.', 'tadris' ) );
	}

	$comment = get_comment( $comment_id );
	if ( ! $comment || '1' !== (string) $comment->comment_approved ) {
		webmz_comments_widget_json_error( esc_html__( 'دیدگاه معتبر نیست.', 'tadris' ) );
	}

	$voter_key = webmz_comments_widget_get_voter_key();
	$votes     = webmz_comments_widget_get_votes( $comment_id );

	if ( isset( $votes[ $voter_key ] ) && $votes[ $voter_key ] === $vote_type ) {
		unset( $votes[ $voter_key ] );
	} else {
		$votes[ $voter_key ] = $vote_type;
	}

	update_comment_meta( $comment_id, WEBMZ_COMMENT_WIDGET_VOTE_META, $votes );

	wp_send_json_success( webmz_comments_widget_get_vote_counts( $comment_id ) );
}
add_action( 'wp_ajax_webmz_comments_widget_vote', 'webmz_comments_widget_ajax_vote' );
add_action( 'wp_ajax_nopriv_webmz_comments_widget_vote', 'webmz_comments_widget_ajax_vote' );
