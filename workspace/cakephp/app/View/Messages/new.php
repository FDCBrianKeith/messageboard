<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<h1>New Message</h1>
<div>
    <?php 
        $this->Form->create('Message');
        echo $this->Form->input(
            'email',
            array(
                'options' => array(),
                'empty' => '(choose one)', 
                'label' => 'Email',
                'class' => 'form-control',
                'id' => 'select-option'
            )
        ); 
        echo $this->Form->input('message',array('label' => 'Message','class' => 'form-control','id' => 'message')); 
        echo $this->Form->button('Send',array('label' => 'Send','class' => 'send-btn mt-4 btn btn-primary text-right'));  
        echo $this->Form->end();
    ?>
</div>

<script>
    const data = <?php echo json_encode($users); ?>;
    $('#select-option').select2({
        ajax: {
            url: '<?php echo $this->Html->url(['controller' => 'Messages', 'action' => 'ajaxGetUsers'])?>',
            data: function(params){
                return {q: params.term}
            },
            processResults: function(data){
                const mappedData = JSON.parse(data).data
                return {
                    results: mappedData.map((d,key) => {
                        return {
                            id: d.User.id,
                            text: `${d.User.email}`,
                            html: `
                                <div class="">
                                    ${d.User.image?`<img class="mr-1" width="40" src=/cakephp/${encodeURI(d.User.image)}>`:'<img class="mr-1" width="40" src=/cakephp/app/webroot/img/pfp.jpg>'}
                                    ${d.User.email}
                                </div>`,  
                            title: d.User.email
                        }
                    })
                }
            },
            cache: true
        },
        escapeMarkup: function(markup) {
            return markup;
        },
        templateResult: function(data) {
            return data.html;
        },
        templateSelection: function(data) {
            return data.text;
        }
    });

    $('#select-option').on('change', function() {
        var selectedValue = $(this).val();
    });

    $('.send-btn').click(function(){
        const data = {
            'recipient_id': $('#select-option').val() ,
            'message': document.getElementById('message').value,
        };
        $.ajax({
            url: '<?php echo $this->Html->url(['controller' => 'Messages', 'action' => 'ajaxSendMessage']); ?>',
            data: data,
            type: 'POST',
            success: function(data){
                const message = JSON.parse(data);
                if(message.success){
                    window.location.href="/cakephp/messages"
                }
            }
        })
    })

</script>