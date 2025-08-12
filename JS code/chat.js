$(function(){
    function loadMessages() {
        var group_id = $('input[name="group_id"]').val();
        $.get('fetch_messages.php?group_id=' + group_id, function(data) {
            $('#chat-box').html('');
            data.forEach(function(msg) {
                $('#chat-box').append(renderMessage(msg));
            });
            $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
        }, 'json');
    }
    $('#chat-form').submit(function(e){
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'send_message.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(){
                loadMessages();
                $('#chat-form input[name="message"]').val('');
                $('#file-input').val('');
            }
        });
    });
    setInterval(loadMessages, 2000);
    loadMessages();

    // Helper to format time as HH:MM AM/PM
    function formatTime(datetime) {
        if (!datetime) return '';
        var d = new Date(datetime.replace(' ', 'T'));
        var h = d.getHours();
        var m = d.getMinutes();
        var ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12;
        h = h ? h : 12;
        m = m < 10 ? '0'+m : m;
        return h + ':' + m + ' ' + ampm;
    }
    // Helper to escape HTML
    function escapeHtml(text) {
        return $('<div>').text(text).html();
    }
    $('#attach-btn').on('click', function(e) {
        e.preventDefault();
        $('#file-input').click();
    });

    function renderMessage(msg) {
        var isMine = msg.user_id == window.currentUserId;
        var align = isMine ? 'right' : 'left';
        var bubble = isMine ? 'bubble-mine' : 'bubble-other';
        var avatar = msg.image ? `<img src="${msg.image}" class="chat-avatar">` : `<div class="chat-avatar">${msg.user_name.charAt(0).toUpperCase()}</div>`;
        var fileHtml = '';
        if (msg.file_path) {
            // Show image if file is an image
            var ext = msg.file_path.split('.').pop().toLowerCase();
            if (['jpg','jpeg','png','gif','webp'].includes(ext)) {
                fileHtml = `<div class="chat-file"><img src="${msg.file_path}" style="max-width:180px;max-height:180px;border-radius:10px;margin-top:8px;"></div>`;
            } else {
                fileHtml = `<div class="chat-file"><a href="${msg.file_path}" target="_blank" style="color:#3a7bd5;font-weight:600;"><i class="bx bx-file"></i> Download file</a></div>`;
            }
        }
        return `
        <div class="chat-message ${align}">
            ${!isMine ? avatar : ''}
            <div class="chat-bubble ${bubble}">
                <div class="chat-header">
                    <span class="chat-user">${msg.user_name}</span>
                    <span class="chat-mbti">(${msg.mbti})</span>
                    <span class="chat-time">${msg.time || ''}</span>
                </div>
                <div class="chat-text">${msg.message}</div>
                ${fileHtml}
            </div>
        </div>`;
    }
});