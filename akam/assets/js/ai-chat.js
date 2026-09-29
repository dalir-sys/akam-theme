/**
 * Akam AI support chat widget.
 *
 * The conversation is kept in sessionStorage for this tab only; every message is
 * sent to admin-ajax, which calls the AI provider with the site's API key.
 */
(function () {
	'use strict';

	var cfg = window.webmzAiChat;
	if (!cfg || !cfg.ajaxUrl) {
		return;
	}

	var i18n = cfg.i18n || {};
	var STORAGE_KEY = 'webmzAiChat:v1';
	var messages = loadMessages();
	var busy = false;
	var root, panel, list, form, input, sendBtn, toggleBtn, counter;

	function loadMessages() {
		try {
			var stored = JSON.parse(window.sessionStorage.getItem(STORAGE_KEY) || '[]');
			return Array.isArray(stored) ? stored : [];
		} catch (error) {
			return [];
		}
	}

	function saveMessages() {
		try {
			window.sessionStorage.setItem(STORAGE_KEY, JSON.stringify(messages.slice(-40)));
		} catch (error) {
			// Storage unavailable (private mode); the chat still works for this page view.
		}
	}

	function escapeHtml(text) {
		return String(text || '')
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;');
	}

	/**
	 * Minimal, safe formatting for AI replies: text is escaped first, then
	 * **bold**, [label](https://link), bare links, list items and line breaks.
	 */
	function formatReply(text) {
		var html = escapeHtml(text);

		html = html.replace(/\*\*([^*\n]+)\*\*/g, '<strong>$1</strong>');
		html = html.replace(/\[([^\]\n]+)\]\((https?:\/\/[^\s)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>');
		html = html.replace(/(^|[\s(])(https?:\/\/[^\s<)]+)/g, '$1<a href="$2" target="_blank" rel="noopener noreferrer">$2</a>');

		var lines = html.split(/\n/);
		var out = [];
		var inList = false;

		lines.forEach(function (line) {
			var item = line.match(/^\s*(?:[-*•]|\d+[.)])\s+(.*)$/);
			if (item) {
				if (!inList) {
					out.push('<ul>');
					inList = true;
				}
				out.push('<li>' + item[1] + '</li>');
				return;
			}
			if (inList) {
				out.push('</ul>');
				inList = false;
			}
			out.push(line === '' ? '<br>' : '<p>' + line + '</p>');
		});

		if (inList) {
			out.push('</ul>');
		}

		return out.join('');
	}

	function el(tag, className, html) {
		var node = document.createElement(tag);
		if (className) {
			node.className = className;
		}
		if (html !== undefined) {
			node.innerHTML = html;
		}
		return node;
	}

	function scrollToBottom() {
		list.scrollTop = list.scrollHeight;
	}

	function renderMessage(role, content) {
		var bubble = el('div', 'webmz-ai-chat__msg webmz-ai-chat__msg--' + role);
		bubble.innerHTML = role === 'assistant' ? formatReply(content) : escapeHtml(content).replace(/\n/g, '<br>');
		list.appendChild(bubble);
		scrollToBottom();
		return bubble;
	}

	function renderSuggestions() {
		var existing = list.querySelector('.webmz-ai-chat__suggestions');
		if (existing) {
			existing.remove();
		}

		if (messages.length || !cfg.suggestions || !cfg.suggestions.length) {
			return;
		}

		var wrap = el('div', 'webmz-ai-chat__suggestions');
		cfg.suggestions.forEach(function (text) {
			var chip = el('button', 'webmz-ai-chat__chip');
			chip.type = 'button';
			chip.textContent = text;
			chip.addEventListener('click', function () {
				send(text);
			});
			wrap.appendChild(chip);
		});
		list.appendChild(wrap);
	}

	function renderAll() {
		list.innerHTML = '';
		if (cfg.welcome) {
			renderMessage('assistant', cfg.welcome);
		}
		messages.forEach(function (msg) {
			renderMessage(msg.role, msg.content);
		});
		renderSuggestions();
	}

	function setBusy(state) {
		busy = state;
		sendBtn.disabled = state;
		input.disabled = state;
		root.classList.toggle('is-busy', state);
	}

	function post(action, data) {
		var body = new window.FormData();
		body.append('action', action);
		Object.keys(data || {}).forEach(function (key) {
			body.append(key, data[key]);
		});

		return window.fetch(cfg.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: body
		}).then(function (response) {
			return response.json().catch(function () {
				return { success: false, data: {} };
			}).then(function (json) {
				json.status = response.status;
				return json;
			});
		});
	}

	function refreshNonce() {
		return post('webmz_ai_chat_nonce', {}).then(function (json) {
			if (json && json.success && json.data && json.data.nonce) {
				cfg.nonce = json.data.nonce;
				return true;
			}
			return false;
		});
	}

	function requestReply(retried) {
		var history = messages.slice(-1 * (cfg.historyLimit || 10));

		return post('webmz_ai_chat_send', {
			nonce: cfg.nonce,
			messages: JSON.stringify(history)
		}).then(function (json) {
			// Page served from a full-page cache with an expired nonce: refresh once and retry.
			if (!json.success && json.data && json.data.code === 'bad_nonce' && !retried) {
				return refreshNonce().then(function (ok) {
					return ok ? requestReply(true) : json;
				});
			}
			return json;
		});
	}

	function send(text) {
		var content = String(text || '').trim();

		if (!content || busy) {
			return;
		}

		if (cfg.maxChars && content.length > cfg.maxChars) {
			renderMessage('error', i18n.tooLong || 'Too long');
			return;
		}

		messages.push({ role: 'user', content: content });
		saveMessages();
		renderSuggestions();
		renderMessage('user', content);
		input.value = '';
		updateCounter();
		setBusy(true);

		var typing = el('div', 'webmz-ai-chat__msg webmz-ai-chat__msg--assistant webmz-ai-chat__typing', '<span></span><span></span><span></span>');
		typing.setAttribute('aria-label', i18n.typing || '');
		list.appendChild(typing);
		scrollToBottom();

		requestReply(false).then(function (json) {
			typing.remove();

			if (json && json.success && json.data && json.data.reply) {
				messages.push({ role: 'assistant', content: json.data.reply });
				saveMessages();
				renderMessage('assistant', json.data.reply);
				return;
			}

			// Failed turns are dropped so the visitor can resend without a dangling question.
			messages.pop();
			saveMessages();
			renderMessage('error', (json && json.data && json.data.message) || i18n.error || 'Error');
		}).catch(function () {
			typing.remove();
			messages.pop();
			saveMessages();
			renderMessage('error', i18n.error || 'Error');
		}).then(function () {
			setBusy(false);
			input.focus();
		});
	}

	function updateCounter() {
		if (!counter || !cfg.maxChars) {
			return;
		}
		var left = cfg.maxChars - input.value.length;
		counter.textContent = left < 60 ? String(left) : '';
		counter.classList.toggle('is-over', left < 0);
	}

	function open() {
		root.classList.add('is-open');
		panel.hidden = false;
		toggleBtn.setAttribute('aria-expanded', 'true');
		toggleBtn.setAttribute('aria-label', i18n.close || '');
		scrollToBottom();
		window.setTimeout(function () {
			input.focus();
		}, 50);
	}

	function close() {
		root.classList.remove('is-open');
		panel.hidden = true;
		toggleBtn.setAttribute('aria-expanded', 'false');
		toggleBtn.setAttribute('aria-label', i18n.open || '');
		toggleBtn.focus();
	}

	function build() {
		root = el('div', 'webmz-ai-chat webmz-ai-chat--' + (cfg.position === 'left' ? 'left' : 'right'));
		root.setAttribute('dir', 'rtl');

		toggleBtn = el('button', 'webmz-ai-chat__toggle',
			'<svg class="webmz-ai-chat__icon-open" viewBox="0 0 24 24" width="26" height="26" aria-hidden="true"><path fill="currentColor" d="M12 3C6.5 3 2 6.9 2 11.7c0 2.4 1.1 4.6 3 6.2L4.3 21l3.6-1.8c1.2.4 2.6.6 4.1.6 5.5 0 10-3.9 10-8.7S17.5 3 12 3zm-4 9.9a1.3 1.3 0 1 1 0-2.6 1.3 1.3 0 0 1 0 2.6zm4 0a1.3 1.3 0 1 1 0-2.6 1.3 1.3 0 0 1 0 2.6zm4 0a1.3 1.3 0 1 1 0-2.6 1.3 1.3 0 0 1 0 2.6z"/></svg>' +
			'<svg class="webmz-ai-chat__icon-close" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true"><path fill="currentColor" d="M18.3 5.7a1 1 0 0 0-1.4 0L12 10.6 7.1 5.7a1 1 0 1 0-1.4 1.4l4.9 4.9-4.9 4.9a1 1 0 1 0 1.4 1.4l4.9-4.9 4.9 4.9a1 1 0 0 0 1.4-1.4L13.4 12l4.9-4.9a1 1 0 0 0 0-1.4z"/></svg>');
		toggleBtn.type = 'button';
		toggleBtn.setAttribute('aria-expanded', 'false');
		toggleBtn.setAttribute('aria-label', i18n.open || '');

		panel = el('section', 'webmz-ai-chat__panel');
		panel.hidden = true;
		panel.setAttribute('role', 'dialog');
		panel.setAttribute('aria-label', cfg.botName || '');

		var header = el('header', 'webmz-ai-chat__header');
		var title = el('div', 'webmz-ai-chat__title');
		title.innerHTML = '<span class="webmz-ai-chat__avatar" aria-hidden="true">AI</span>';
		var name = el('strong');
		name.textContent = cfg.botName || '';
		title.appendChild(name);
		header.appendChild(title);

		var resetBtn = el('button', 'webmz-ai-chat__reset', '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M12 5V2L7 6l5 4V7a5 5 0 1 1-5 5H5a7 7 0 1 0 7-7z"/></svg>');
		resetBtn.type = 'button';
		resetBtn.title = i18n.reset || '';
		resetBtn.setAttribute('aria-label', i18n.reset || '');
		resetBtn.addEventListener('click', function () {
			if (busy) {
				return;
			}
			messages = [];
			saveMessages();
			renderAll();
			input.focus();
		});
		header.appendChild(resetBtn);

		list = el('div', 'webmz-ai-chat__messages');
		list.setAttribute('aria-live', 'polite');

		form = el('form', 'webmz-ai-chat__form');
		input = el('textarea', 'webmz-ai-chat__input');
		input.rows = 1;
		input.placeholder = cfg.placeholder || '';
		input.setAttribute('aria-label', cfg.placeholder || '');
		counter = el('span', 'webmz-ai-chat__counter');
		sendBtn = el('button', 'webmz-ai-chat__send', '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="currentColor" d="M3.4 20.4 21 12 3.4 3.6 3.4 10l12.6 2-12.6 2z" transform="matrix(-1 0 0 1 24 0)"/></svg>');
		sendBtn.type = 'submit';
		sendBtn.setAttribute('aria-label', i18n.send || '');
		form.appendChild(input);
		form.appendChild(counter);
		form.appendChild(sendBtn);

		var footer = el('div', 'webmz-ai-chat__footer');
		var note = el('span', 'webmz-ai-chat__note');
		note.textContent = i18n.aiNote || '';
		footer.appendChild(note);
		if (cfg.fallbackUrl) {
			var link = el('a', 'webmz-ai-chat__fallback');
			link.href = cfg.fallbackUrl;
			link.textContent = cfg.fallbackText || cfg.fallbackUrl;
			footer.appendChild(link);
		}

		panel.appendChild(header);
		panel.appendChild(list);
		panel.appendChild(form);
		panel.appendChild(footer);
		root.appendChild(panel);
		root.appendChild(toggleBtn);
		document.body.appendChild(root);

		toggleBtn.addEventListener('click', function () {
			if (root.classList.contains('is-open')) {
				close();
			} else {
				open();
			}
		});

		form.addEventListener('submit', function (event) {
			event.preventDefault();
			send(input.value);
		});

		input.addEventListener('keydown', function (event) {
			// Enter sends; Shift+Enter adds a new line.
			if (event.key === 'Enter' && !event.shiftKey && !event.isComposing) {
				event.preventDefault();
				send(input.value);
			}
		});

		input.addEventListener('input', function () {
			input.style.height = 'auto';
			input.style.height = Math.min(input.scrollHeight, 120) + 'px';
			updateCounter();
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && root.classList.contains('is-open')) {
				close();
			}
		});

		renderAll();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', build);
	} else {
		build();
	}

	// Lets other buttons open the chat, e.g. <a href="#" data-webmz-ai-chat-open>.
	document.addEventListener('click', function (event) {
		var trigger = event.target.closest ? event.target.closest('[data-webmz-ai-chat-open]') : null;
		if (trigger && root) {
			event.preventDefault();
			open();
		}
	});
}());
