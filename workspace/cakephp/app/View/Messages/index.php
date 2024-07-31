<style>
    .message-tile {
        padding: 10px;
        border: 1px solid #ccc;
        margin-bottom: 10px;
    }
    .img-container {
        max-width: 150px;
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
        max-height: 100px;
        object-fit: cover; /* Ensure the image covers the container */
    }
    .message-tile:hover{
        background-color: #f8fafc;
        cursor: pointer
    }
    a{
        text-decoration: none !important;
    } 
</style>

<div>
    <div class="head d-flex justify-content-between">
        <h1>Message List</h1>
        <div class="form d-flex  align-items-center">
            <input type="text" label="Search for message" class="form-control" id="search-query">
            <button class="btn btn-secondary ml-3" id="submit-search">Search</button>
        </div>
    </div>
    <div style="text-align: right; margin-bottom: 1rem">
        <a href="messages/new" class="btn btn-primary">New Message</a>
    </div>
    <div id="messages">
    </div>
    <div style="text-align: center">
        <button id="see-more" class="btn btn-primary">See more</button>
    </div>
</div>

<script>
    let page = 1;
    const userId = <?php echo AuthComponent::user('id'); ?>;
    function loadMessages(page){
        console.log(userId);
        const data = {
            'page': page,
            'search': document.getElementById('search-query').value,
        };
        $.ajax({
            url: '<?php echo $this->Html->url(['controller' => 'Messages', 'action' => 'ajaxGetLists']); ?>',
            data: data,
            type: 'GET',
            success: function(fetchedData){
                const data = JSON.parse(fetchedData);
                console.log(data);
                if(data.success){
                    if(page === data.totalPages){
                        $('#see-more').hide();
                    }else{
                        $('#see-more').show();
                    }
                    const messagesDiv = document.getElementById('messages');
                    data.data.forEach(msg => {
                        const newMsgTile = `
                            <div class="message" id="message_${msg.Message.recipient_id}" style="margin: 25px 0">
                            <div class="d-flex position-relative align-items-end justify-content-between mb-2">
                                <h5 class="text-secondary mb-0" style="font-size: 18px">${msg.User.name}</h5>
                                <button class="btn btn-danger btn-sm" id="delete-btn" onclick="deleteConversation(${msg.Message.recipient_id})">Delete</button>
                            </div>
                            <a href="/cakephp/messages/view/${msg.User.id}">
                                <div class="message-tile rounded row h-100" style="min-height: 120px">
                                    <div class="img-container col-2 d-flex align-items-center">
                                        <img src="${msg.User.image?msg.User.image:'app/webroot/img/pfp.jpg'}" class="img-thumbnail" id="user-image"/>
                                    </div>
                                   <div class="d-flex flex-column flex-fill w-100 col">
                                        <div class="h-100 flex-grow-1 d-flex flex-column align-items-between justify-content-between">
                                            <p class="mb-0"><span class="text-secondary fw-light fs-5 ${(userId ===  msg.Message.sender_id)?'':'d-none'}">You: </span>${msg.Message.message}</p>
                                            <div class="">
                                                <p class="message-section-date fw-light mb-0"><small>${new Date(msg.Message.created).toLocaleString("en-PH", { timeZone: "Asia/Manila" })}</small></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            </div>
                        `;
                        
                        $('#messages').append(newMsgTile);
                    });
                }else{
                    $('#messages').append(`<h5 class="text-center">${data.message}</h5>`);
                    $('#see-more').remove();
                }

            }
        })
    }

    $(document).ready(function(){
        console.log('page loaded');
        loadMessages(1);
    });
    
</script>