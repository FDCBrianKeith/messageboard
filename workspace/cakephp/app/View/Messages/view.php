<style>
    .message{
    }
    .message-tile {
        border: 1px solid #ccc;
        display: flex;
        align-items: center; 
        padding: 10px;
        margin-bottom: 10px;
    }
    .message-tile-reverse{
        flex-direction: row-reverse;
    }
    .img-container {
        max-width: 100px;
        margin-right: 20px; 
    }
    .img-container img{
        max-height: 80px;
    }
    .message-section {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100;
        flex-grow: 1;
        margin-right: 20px
    }
    .message-section p{
        padding: 0;
        margin: 0;
    }
    .message-section-date{
        border-top: 1px solid #ccc;
        text-align: right;
    }
    #user-image {
        max-width: 100%;
        height: auto;
        object-fit: cover; /* Ensure the image covers the container */
    }
</style>
<div>
    <h3>Message Details</h3>
    <div class="alert alert-info" role="alert">
        Conversation with: <span class="fw-semibold" style="font-weight: bold"><?php echo h($recipient['User']['name']) ?></span>
    </div>
    <div style="display: flex; justify-content: end">
        <div style="width: 50%; margin-bottom: 1rem; text-align: right;">
            <?php $this->Form->create('Message'); 
            echo $this->Form->input('message',array('label' => false,'class' => 'form-control','id' => 'message-box'));
            echo $this->Form->button('Reply Message',array('label' => 'Password','class' => 'reply-btn btn btn-primary text-right mt-2'));  
            ?>
        </div>
    </div>
    
    <div id="messages">
    <?php foreach ($messages as $key=>$message): ?>
        <div class="message" id="message_<?php echo $message['Message']['id'] ?>">
            <div class="d-flex justify-content-end m-1">
                <button class="btn btn-danger" id="delete-btn" onclick="deleteMessage(<?php echo $message['Message']['id'] ?>)">Delete</button>
            </div>
            <?php if ($id != $message['Message']['sender_id']): ?>
                <div class="message-tile rounded">
            <?php else: ?>
                <div class="message-tile rounded message-tile-reverse bg-light">
            <?php endif; ?>
                <div class="img-container">
                    <?php
                        $type = ($id == $message['Message']['sender_id'])?'Sender':'Recipient';
                        $img = $message['Sender']['image']?$message['Sender']['image']:'app/webroot/img/pfp.jpg';
                        echo $this->Html->image('/'.$img, [
                            'alt' => 'User Image',
                            'id' => 'user-image',
                            'class' => 'img-thumbnail'
                        ]); 
                    ?>
                </div>
                <div class="d-flex align-items-start flex-column w-100 mx-4 ">
                    <p class="content" id="content_<?php echo $key ?>">
                        <?php 
                            $msgContent = $message['Message']['message'];     
                            echo h($msgContent);
                        ?>
                    </p>
                    <p class="text-muted message-section-date mb-0 w-100 mt-auto fw-light" style="font-size: 12px"><?php  echo date("F jS, Y - g:i A", strtotime(($message['Message']['created'])));?></p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>

    <div class="d-flex items-center justify-content-center mb-2 w-full">
        <div>
            <?php
            $paginator = $this->Paginator;
            if ($paginator->hasPrev()) {
                echo $paginator->prev(
                    __("Back"),
                    array(
                        'tag' => 'button',
                        'class' => 'btn mx-1',
                        'id' => 'see-less'
                        )
                    );
                }
            ?>
        </div>
        <div>
            <?php
            if ($paginator->hasNext()) {
                echo $paginator->next(
                    __("See more"),
                    array(
                        'tag' => 'button',
                        'class' => 'btn mx-1',
                        'id' => 'see-more'
                    )
                );
            }
            ?>
        </div>
    </div>
</div>

<script>
    let messages = [];
    $(document).ready(function() {
    })
    
    $('.reply-btn').click(function() {
        const recipientId = <?php echo ($recipientId); ?>;
        const data = {
            'recipient_id': recipientId ,
            'message': document.getElementById('message-box').value,
        };
        $.ajax({
            url: '<?php echo $this->Html->url(['controller' => 'Messages', 'action' => 'ajaxSendMessage']); ?>',
            data: data,
            type: 'POST',
            success: function(data) {
                var baseUrl = '<?php echo $this->Html->url(array('controller' => 'Messages', 'action' => 'index')); ?>';
                var url = `${baseUrl}/view/${recipientId}/page:1`
                window.location.href = url;
                const message = JSON.parse(data);
                const messagesDiv = document.getElementById('messages');
                const newMessageTile = `
                    <div class="message" id="message_${message.data.Message.id}">
                        <div class="d-flex justify-content-end m-1">
                            <button class="btn btn-danger" id="delete-btn" onclick="deleteMessage(${message.data.Message.id})">Delete</button>
                        </div>
                        <div class="message-tile message-tile-reverse">
                            <div class="img-container">
                                <?php
                                    $type = 'Sender';
                                    $img = $message[$type]['image'] ?? 'app/webroot/img/pfp.jpg';
                                    echo $this->Html->image('/'.$img, [
                                        'alt' => 'User Image',
                                        'id' => 'user-image',
                                        'class' => 'img-thumbnail'
                                    ]); 
                                ?>
                            </div>
                            <div class="message-section">
                                <p>${message.data.Message.message}</p>
                                <p class="message-section-date">July 23, 2024.</p>
                            </div>
                        </div>
                    </div>`;
                if (message.success) {
                    messagesDiv.insertAdjacentHTML('afterbegin', newMessageTile);
                    const currentMessageCount = '<?php echo (count($messages)); ?>';
                    if (currentMessageCount > 10) {
                        $('#messages').children().last().remove();
                    }
                }
            }
        })
    })

    const deleteMessage = (msg) => {
        const currentPage = getCurrentPage();
        const recipientId = <?php echo ($recipientId); ?>;
        const userId = <?php echo ($id); ?>;
        $.ajax({
            url: '<?php echo $this->Html->url(['controller' => 'Messages', 'action' => 'ajaxDeleteMessage']); ?>',
            data: {
                id: msg,
                currentPage: currentPage,
                recipientId: recipientId
            },
            type: 'POST',
            success: function(data) {
                const message = JSON.parse(data);
                if (message.success) {
                    $(`#message_${msg}`).fadeOut();
                    if (message.nextMessage) {
                        const type = (userId == message.nextMessage.Message.sender_id)?'Sender':'Recipient';
                        const img = message.nextMessage['Sender']['image'] ?? 'app/webroot/img/pfp.jpg';
                        const newMessageTile = `
                            <div class="message" id="message_${message.nextMessage.Message.id}">
                                <div class="d-flex justify-content-end m-1">
                                    <button class="btn btn-danger" id="delete-btn" onclick="deleteMessage(${message.nextMessage.Message.id})">Delete</button>
                                </div>
                                <div class="message-tile ${type === 'Sender' ? 'message-tile-reverse' : ''}">
                                    <div class="img-container">
                                        <img src="/cakephp/${encodeURI(img)}" alt="User Image" id="user-image" class="img-thumbnail">
                                    </div>
                                    <div class="message-section">
                                        <p>${message.nextMessage.Message.message}</p>
                                        <p class="message-section-date">July 23, 2024.</p>
                                    </div>
                                </div>
                            </div>`;

                        $('#messages').append(newMessageTile);
                    } 
                }
            }
        })
    }

    const getCurrentPage = () => {
        return <?php echo $this->Paginator->counter('{:page}'); ?>;
    }

    var minimizedEls = $('.content');
    minimizedEls.each(function() {
        var t = $(this).text();        
        if (t.length < 100) return;
        $(this).html(
            t.substring(0,100)+'<span>... </span><a href="#" class="more">More</a>'+
            '<span style="display:none;">'+ t.slice(25,t.length)+' <a href="#" class="less">Less</a></span>'
        );
    })

    $('a.more', minimizedEls).click(function(event) {
        event.preventDefault();
        $(this).hide().prev().hide();
        $(this).next().show();        
    });
    
    $('a.less', minimizedEls).click(function(event) {
        event.preventDefault();
        $(this).parent().hide().prev().show().prev().show();    
    });
    
</script>