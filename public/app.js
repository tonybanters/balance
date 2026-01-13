document.addEventListener('DOMContentLoaded', function() {
    init_task_drag_drop();
    init_keyboard_shortcuts();
});

function init_keyboard_shortcuts() {
    let selected_index = -1;

    function get_tasks() {
        return Array.from(document.querySelectorAll('.task_item'));
    }

    function update_selection(tasks, new_index) {
        tasks.forEach(function(t) { t.classList.remove('task_selected'); });
        if (new_index >= 0 && new_index < tasks.length) {
            selected_index = new_index;
            tasks[selected_index].classList.add('task_selected');
            tasks[selected_index].scrollIntoView({ block: 'nearest' });
        } else {
            selected_index = -1;
        }
    }

    document.addEventListener('keydown', function(e) {
        const target = e.target;
        const in_input = target.tagName === 'INPUT' || target.tagName === 'TEXTAREA' || target.tagName === 'SELECT';

        if (in_input && e.key !== 'Escape') return;

        const tasks = get_tasks();

        switch (e.key) {
            case 'n':
                e.preventDefault();
                const title_input = document.querySelector('.add_task_form input[name="title"]');
                if (title_input) title_input.focus();
                break;

            case 'j':
                e.preventDefault();
                if (tasks.length === 0) return;
                const next = selected_index < tasks.length - 1 ? selected_index + 1 : 0;
                update_selection(tasks, next);
                break;

            case 'k':
                e.preventDefault();
                if (tasks.length === 0) return;
                const prev = selected_index > 0 ? selected_index - 1 : tasks.length - 1;
                update_selection(tasks, prev);
                break;

            case 'x':
                e.preventDefault();
                if (selected_index >= 0 && tasks[selected_index]) {
                    const toggle_form = tasks[selected_index].querySelector('form[action*="/toggle"]');
                    if (toggle_form) toggle_form.submit();
                }
                break;

            case 'd':
                e.preventDefault();
                if (selected_index >= 0 && tasks[selected_index]) {
                    if (confirm('Delete this task?')) {
                        const delete_form = tasks[selected_index].querySelector('form[action*="/delete"]');
                        if (delete_form) delete_form.submit();
                    }
                }
                break;

            case 'e':
                e.preventDefault();
                if (selected_index >= 0 && tasks[selected_index]) {
                    const edit_link = tasks[selected_index].querySelector('.task_edit');
                    if (edit_link) window.location = edit_link.href;
                }
                break;

            case '/':
                e.preventDefault();
                const search_input = document.querySelector('.search_input');
                if (search_input) search_input.focus();
                break;

            case 'Escape':
                update_selection(tasks, -1);
                if (in_input) target.blur();
                break;
        }
    });
}

function init_task_drag_drop() {
    const task_list = document.getElementById('task_list');
    if (!task_list) return;

    const project_id = task_list.dataset.projectId;
    let dragged = null;

    task_list.addEventListener('dragstart', function(e) {
        const item = e.target.closest('.task_item');
        if (!item) return;
        dragged = item;
        item.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
    });

    task_list.addEventListener('dragend', function(e) {
        const item = e.target.closest('.task_item');
        if (!item) return;
        item.classList.remove('dragging');
        dragged = null;
        const items = task_list.querySelectorAll('.task_item');
        items.forEach(function(i) { i.classList.remove('drag_over'); });
    });

    task_list.addEventListener('dragover', function(e) {
        e.preventDefault();
        const item = e.target.closest('.task_item');
        if (!item || item === dragged) return;
        e.dataTransfer.dropEffect = 'move';

        const items = task_list.querySelectorAll('.task_item');
        items.forEach(function(i) { i.classList.remove('drag_over'); });
        item.classList.add('drag_over');
    });

    task_list.addEventListener('drop', function(e) {
        e.preventDefault();
        const target = e.target.closest('.task_item');
        if (!target || target === dragged) return;

        const rect = target.getBoundingClientRect();
        const midY = rect.top + rect.height / 2;

        if (e.clientY < midY) {
            task_list.insertBefore(dragged, target);
        } else {
            task_list.insertBefore(dragged, target.nextSibling);
        }

        save_task_order(project_id);
    });
}

function save_task_order(project_id) {
    const task_list = document.getElementById('task_list');
    const items = task_list.querySelectorAll('.task_item');
    const task_ids = [];

    items.forEach(function(item) {
        task_ids.push(item.dataset.taskId);
    });

    fetch('/projects/' + project_id + '/tasks/reorder', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ task_ids: task_ids })
    });
}
