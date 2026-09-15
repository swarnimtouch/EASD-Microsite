import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const panel = document.querySelector('[data-webinar-comments]');
    if (!panel) return;

    const form = panel.querySelector('[data-webinar-comment-form]');
    const input = panel.querySelector('[data-webinar-comment-input]');
    const parentInput = panel.querySelector('[data-webinar-comment-parent]');
    const replying = panel.querySelector('[data-webinar-replying]');
    const replyingName = panel.querySelector('[data-webinar-replying-name]');
    const list = panel.querySelector('[data-webinar-comments-list]');
    const counter = panel.querySelector('[data-webinar-comments-count]');
    const knownComments = new Set(Array.from(list?.querySelectorAll('[data-comment-id]') || []).map(item => item.dataset.commentId));
    const currentUserId = Number(panel.dataset.currentUserId);

    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, character => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[character]));
    const voteButton = comment => `<button type="button" data-comment-upvote="${escapeHtml(comment.id)}" class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400"><i class="ph-fill ph-arrow-fat-up"></i><span data-upvote-count>${Number(comment.upvotes_count || 0)}</span> Upvote</button>`;
    const replyMarkup = comment => `<article data-comment-id="${escapeHtml(comment.id)}" class="flex gap-3"><img src="${escapeHtml(comment.avatar)}" alt="${escapeHtml(comment.name)}" class="h-8 w-8 shrink-0 rounded-full border border-slate-200 object-cover"><div class="min-w-0 flex-1 rounded-2xl rounded-tl-none bg-blue-50/60 p-3"><div class="flex items-center justify-between gap-2"><p class="text-[11px] font-black text-slate-900">${escapeHtml(comment.name)}</p><time class="text-[9px] text-slate-400">${escapeHtml(comment.created_at)}</time></div><p class="mt-1 whitespace-pre-line text-xs leading-5 text-slate-600">${escapeHtml(comment.body)}</p><div class="mt-2">${voteButton(comment)}</div></div></article>`;
    const topLevelMarkup = comment => `<article data-comment-id="${escapeHtml(comment.id)}" data-upvotes="${Number(comment.upvotes_count || 0)}" class="webinar-comment flex gap-3"><img src="${escapeHtml(comment.avatar)}" alt="${escapeHtml(comment.name)}" class="h-9 w-9 shrink-0 rounded-full border border-slate-200 object-cover"><div class="min-w-0 flex-1"><div class="rounded-2xl rounded-tl-none bg-slate-50 p-4"><div class="flex flex-wrap items-center justify-between gap-2"><p class="text-xs font-black text-slate-900">${escapeHtml(comment.name)}</p><time class="text-[9px] font-semibold text-slate-400">${escapeHtml(comment.created_at)}</time></div><p class="mt-1 whitespace-pre-line text-xs leading-6 text-slate-600">${escapeHtml(comment.body)}</p><div class="mt-3 flex items-center gap-4">${voteButton(comment)}${form ? `<button type="button" data-comment-reply="${escapeHtml(comment.id)}" data-comment-name="${escapeHtml(comment.name)}" class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 hover:text-escBlue"><i class="ph ph-arrow-bend-up-left"></i> Reply</button>` : ''}</div></div><div data-replies-for="${escapeHtml(comment.id)}" class="ml-5 mt-3 space-y-3 border-l-2 border-slate-100 pl-4"></div></div></article>`;

    const sortComments = () => {
        if (!list) return;
        Array.from(list.querySelectorAll(':scope > .webinar-comment')).sort((first, second) => Number(second.dataset.upvotes) - Number(first.dataset.upvotes) || Number(second.dataset.commentId) - Number(first.dataset.commentId)).forEach(item => list.append(item));
    };
    const addComment = comment => {
        const id = String(comment.id);
        if (!list || knownComments.has(id)) return;
        knownComments.add(id);
        list.classList.remove('hidden');
        if (comment.parent_id) {
            list.querySelector(`[data-replies-for="${CSS.escape(String(comment.parent_id))}"]`)?.insertAdjacentHTML('beforeend', replyMarkup(comment));
        } else {
            list.insertAdjacentHTML('beforeend', topLevelMarkup(comment));
            if (counter) counter.textContent = String(list.querySelectorAll(':scope > .webinar-comment').length);
            sortComments();
        }
        list.scrollTop = list.scrollHeight;
    };

    const resetReply = () => { if (parentInput) parentInput.value = ''; replying?.classList.add('hidden'); replying?.classList.remove('flex'); };
    panel.addEventListener('click', async event => {
        const replyButton = event.target.closest('[data-comment-reply]');
        if (replyButton && parentInput) {
            parentInput.value = replyButton.dataset.commentReply;
            if (replyingName) replyingName.textContent = replyButton.dataset.commentName;
            replying?.classList.remove('hidden'); replying?.classList.add('flex'); input?.focus(); return;
        }
        if (event.target.closest('[data-webinar-reply-cancel]')) return resetReply();
        const upvoteButton = event.target.closest('[data-comment-upvote]');
        if (!upvoteButton) return;
        upvoteButton.disabled = true;
        try {
            const response = await window.axios.post(`${panel.dataset.upvoteBase}/${upvoteButton.dataset.commentUpvote}/upvote`, {}, {headers:{Accept:'application/json'}});
            updateVote(upvoteButton.dataset.commentUpvote, response.data.upvotes_count, response.data.upvoted);
        } finally { upvoteButton.disabled = false; }
    });

    const updateVote = (commentId, count, upvoted = null) => {
        const item = list?.querySelector(`[data-comment-id="${CSS.escape(String(commentId))}"]`);
        if (!item) return;
        const button = item.querySelector(':scope [data-comment-upvote]');
        if (button) {
            button.querySelector('[data-upvote-count]').textContent = String(count);
            if (upvoted !== null) { button.classList.toggle('text-escRed', upvoted); button.classList.toggle('text-slate-400', !upvoted); }
        }
        if (item.classList.contains('webinar-comment')) { item.dataset.upvotes = String(count); sortComments(); }
    };

    form?.addEventListener('submit', async event => {
        event.preventDefault();
        const body = input?.value.trim();
        if (!body) return input?.focus();
        const button = form.querySelector('button[type="submit"]');
        button?.setAttribute('disabled', 'disabled');
        try {
            const response = await window.axios.post(form.action, new FormData(form), {headers:{Accept:'application/json'}});
            addComment(response.data.comment);
            input.value = '';
            resetReply();
        } catch (error) {
            window.alert(error.response?.data?.message || 'Unable to submit your question right now.');
        } finally {
            button?.removeAttribute('disabled');
        }
    });

    if (window.Echo && panel.dataset.commentsChannel) {
        window.Echo.channel(panel.dataset.commentsChannel)
            .listen('.comment.created', event => addComment(event.comment))
            .listen('.comment.upvoted', event => updateVote(event.comment_id, event.upvotes_count, Number(event.user_id) === currentUserId ? event.upvoted : null));
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const panel = document.querySelector('[data-comments-panel], [data-session-comments-panel]');

    if (!panel) {
        return;
    }

    const form = panel.querySelector('[data-comment-form], [data-session-comment-form]');
    const textarea = panel.querySelector('[data-comment-input], [data-session-comment-input]');
    const parentInput = panel.querySelector('[data-comment-parent-input]');
    const replyingBanner = panel.querySelector('[data-comment-replying]');
    const replyingName = panel.querySelector('[data-comment-replying-name]');
    const replyCancel = panel.querySelector('[data-comment-reply-cancel]');
    const list = panel.querySelector('[data-comments-list], [data-session-comments-list]');
    const counter = panel.querySelector('[data-comments-count], [data-session-comments-count]');
    const emptyState = panel.querySelector('[data-comments-empty], [data-session-comments-empty]');
    const loadMoreButton = panel.querySelector('[data-comments-load-more]');
    const loadMoreWrap = panel.querySelector('[data-comments-load-wrap]');
    const channel = panel.dataset.commentsChannel || (panel.dataset.sessionId ? `comments.${panel.dataset.sessionId}` : null);
    let totalComments = Number(panel.dataset.commentsTotal || 0);
    let visibleComments = Number(panel.dataset.commentsVisible || 0);
    let isLoadingMore = false;
    const comments = new Set(
        Array.from(panel.querySelectorAll('[data-comment-id]')).map((item) => item.dataset.commentId)
    );

    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    }[char]));

    const updateCount = () => {
        if (counter) {
            counter.textContent = String(totalComments);
        }

        loadMoreWrap?.classList.toggle('d-none', visibleComments >= totalComments);
    };

    const commentsLoadUrl = () => {
        if (panel.matches('[data-comments-panel]')) {
            return new URL(`${window.location.pathname.replace(/\/$/, '')}/comments`, window.location.origin);
        }

        return new URL(panel.dataset.commentsLoadUrl || window.location.href, window.location.href);
    };

    const commentUpvoteUrl = (commentId) => `${window.location.pathname.replace(/\/$/, '')}/comments/${encodeURIComponent(commentId)}/upvote`;

    const sortTopLevelComments = () => {
        if (!panel.matches('[data-comments-panel]') || !list) {
            return;
        }

        Array.from(list.querySelectorAll(':scope > .module-comment-item'))
            .sort((first, second) => {
                const firstVotes = Number(first.querySelector(':scope [data-comment-upvotes]')?.textContent || 0);
                const secondVotes = Number(second.querySelector(':scope [data-comment-upvotes]')?.textContent || 0);

                if (secondVotes !== firstVotes) {
                    return secondVotes - firstVotes;
                }

                return Number(second.dataset.commentId || 0) - Number(first.dataset.commentId || 0);
            })
            .forEach((item) => list.append(item));
    };

    const renderReply = (comment, highlight = false) => {
        const item = document.createElement('div');
        item.className = `module-comment-reply${highlight ? ' is-new' : ''}`;
        item.dataset.commentId = comment.id;
        item.innerHTML = `
            <div class="module-comment-avatar">${escapeHtml(comment.initial || 'U')}</div>
            <div class="module-comment-body">
                <div class="module-comment-topline">
                    <strong>${escapeHtml(comment.name || 'User')}</strong>
                    <span>${escapeHtml(comment.created_at || 'just now')}</span>
                </div>
                <p>${escapeHtml(comment.comment || '')}</p>
                ${panel.matches('[data-comments-panel]') ? `
                    <div class="module-comment-actions">
                        <button
                            type="button"
                            class="btn module-comment-upvote-btn${comment.upvoted_by_user ? ' is-upvoted' : ''}"
                            data-comment-upvote
                            data-upvote-url="${escapeHtml(comment.upvote_url || commentUpvoteUrl(comment.id))}"
                            aria-pressed="${comment.upvoted_by_user ? 'true' : 'false'}"
                        >
                            <i class="bi bi-arrow-up"></i>
                            <span>Upvote</span>
                            <span data-comment-upvotes>${escapeHtml(comment.upvotes_count || 0)}</span>
                        </button>
                    </div>
                ` : ''}
            </div>
        `;

        return item;
    };

    const renderComment = (comment, highlight = false) => {
        const item = document.createElement('div');
        item.className = `module-comment-item session-comment-item${highlight ? ' is-new' : ''}`;
        item.dataset.commentId = comment.id;
        item.innerHTML = `
            <div class="module-comment-avatar">${escapeHtml(comment.initial || 'U')}</div>
            <div class="module-comment-body">
                <div class="module-comment-topline">
                    <strong>${escapeHtml(comment.name || 'User')}</strong>
                    <span>${escapeHtml(comment.created_at || 'just now')}</span>
                </div>
                <p>${escapeHtml(comment.comment || '')}</p>
                ${panel.matches('[data-comments-panel]') ? `
                    <div class="module-comment-actions">
                        <button
                            type="button"
                            class="btn module-comment-upvote-btn${comment.upvoted_by_user ? ' is-upvoted' : ''}"
                            data-comment-upvote
                            data-upvote-url="${escapeHtml(comment.upvote_url || commentUpvoteUrl(comment.id))}"
                            aria-pressed="${comment.upvoted_by_user ? 'true' : 'false'}"
                        >
                            <i class="bi bi-arrow-up"></i>
                            <span>Upvote</span>
                            <span data-comment-upvotes>${escapeHtml(comment.upvotes_count || 0)}</span>
                        </button>
                        <button
                            type="button"
                            class="btn module-comment-reply-btn"
                            data-comment-reply
                            data-comment-id="${escapeHtml(comment.id)}"
                            data-comment-name="${escapeHtml(comment.name || 'User')}"
                        >
                            <i class="bi bi-reply"></i>
                            <span>Reply</span>
                        </button>
                    </div>
                    <div class="module-comment-replies${comment.replies?.length ? '' : ' d-none'}" data-comment-replies></div>
                ` : ''}
            </div>
        `;

        const replies = item.querySelector('[data-comment-replies]');
        comment.replies?.forEach((reply) => {
            comments.add(String(reply.id));
            replies?.append(renderReply(reply));
        });

        return item;
    };

    const showComment = (comment, highlight = false, placement = 'prepend') => {
        if (!comment?.id || comments.has(String(comment.id))) {
            return;
        }

        if (comment.parent_id) {
            const parent = list?.querySelector(`[data-comment-id="${CSS.escape(String(comment.parent_id))}"]`);
            const replies = parent?.querySelector('[data-comment-replies]');
            const item = renderReply(comment, highlight);

            if (!replies) {
                return;
            }

            comments.add(String(comment.id));
            replies.classList.remove('d-none');
            replies.append(item);

            if (highlight) {
                window.setTimeout(() => item.classList.remove('is-new'), 1400);
            }

            return;
        }

        comments.add(String(comment.id));
        emptyState?.remove();

        const item = renderComment(comment, highlight);

        if (placement === 'append') {
            list?.append(item);
        } else {
            list?.prepend(item);
            totalComments += 1;
        }

        visibleComments += 1;
        updateCount();
        sortTopLevelComments();

        if (highlight) {
            window.setTimeout(() => item.classList.remove('is-new'), 1400);
        }
    };

    const resetReplyTarget = () => {
        if (parentInput) {
            parentInput.value = '';
        }

        replyingBanner?.classList.add('d-none');

        if (textarea && panel.matches('[data-comments-panel]')) {
            textarea.placeholder = 'Write a comment...';
        }
    };

    list?.addEventListener('click', (event) => {
        const upvoteButton = event.target.closest('[data-comment-upvote]');

        if (upvoteButton) {
            event.preventDefault();

            if (upvoteButton.disabled || !upvoteButton.dataset.upvoteUrl) {
                return;
            }

            upvoteButton.disabled = true;

            window.axios.post(upvoteButton.dataset.upvoteUrl, {}, {
                headers: {Accept: 'application/json'},
            })
                .then((response) => {
                    const count = upvoteButton.querySelector('[data-comment-upvotes]');
                    upvoteButton.classList.toggle('is-upvoted', Boolean(response.data.upvoted));
                    upvoteButton.setAttribute('aria-pressed', response.data.upvoted ? 'true' : 'false');

                    if (count) {
                        count.textContent = String(response.data.upvotes_count ?? 0);
                    }

                    sortTopLevelComments();
                })
                .catch(() => window.toastr?.error('Unable to update upvote right now'))
                .finally(() => {
                    upvoteButton.disabled = false;
                });

            return;
        }

        const button = event.target.closest('[data-comment-reply]');

        if (!button || !parentInput || !textarea) {
            return;
        }

        parentInput.value = button.dataset.commentId || '';

        if (replyingName) {
            replyingName.textContent = button.dataset.commentName || 'this comment';
        }

        replyingBanner?.classList.remove('d-none');
        textarea.placeholder = `Reply to ${button.dataset.commentName || 'this comment'}...`;
        textarea.focus();
    });

    replyCancel?.addEventListener('click', resetReplyTarget);

    loadMoreButton?.addEventListener('click', async () => {
        if (isLoadingMore) {
            return;
        }

        const renderedComments = Array.from(list?.querySelectorAll(':scope > .module-comment-item[data-comment-id]') || []);
        const offset = renderedComments.length;

        if (!offset) {
            loadMoreWrap?.classList.add('d-none');
            return;
        }

        isLoadingMore = true;
        loadMoreButton.setAttribute('disabled', 'disabled');
        loadMoreButton.classList.add('is-loading');

        try {
            const url = commentsLoadUrl();
            url.searchParams.set('offset', offset);

            const response = await window.axios.get(url.toString(), {
                headers: {Accept: 'application/json'},
            });

            response.data.comments?.forEach((comment) => showComment(comment, false, 'append'));

            if (!response.data.has_more) {
                totalComments = visibleComments;
            }

            updateCount();
        } catch (error) {
            window.toastr?.error('Unable to load more comments right now');
        } finally {
            isLoadingMore = false;
            loadMoreButton.removeAttribute('disabled');
            loadMoreButton.classList.remove('is-loading');
        }
    });

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();

        const button = form.querySelector('button[type="submit"]');
        const comment = textarea?.value.trim();

        if (!comment) {
            textarea?.focus();
            return;
        }

        button?.setAttribute('disabled', 'disabled');
        panel.classList.add('is-posting');

        try {
            const response = await window.axios.post(form.action, new FormData(form), {
                headers: {
                    Accept: 'application/json',
                },
            });

            textarea.value = '';
            resetReplyTarget();
            showComment(response.data.comment, true);
        } catch (error) {
            const message = error.response?.data?.message || 'Unable to post comment right now';
            window.toastr?.error(message);
        } finally {
            button?.removeAttribute('disabled');
            panel.classList.remove('is-posting');
        }
    });

    if (window.Echo && channel) {
        window.Echo.channel(channel)
            .listen('.comment.created', (event) => showComment(event.comment, true));
    }

    updateCount();
});
