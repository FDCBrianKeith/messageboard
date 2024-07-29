<!-- app/View/Users/add.ctp -->
<?php echo $this->Html->script('https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js'); ?>
<?php echo $this->Html->script('https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js'); ?>
<?php echo $this->Html->css('https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css'); ?>

<div class="container">
    
    <?php echo $this->Form->create('User', ['enctype' => 'multipart/form-data','type' => 'put']); ?>
    <fieldset>
        <div class="flex" style="display: flex; align-items: flex-end; margin: 1rem 0">
            <div class="width: 100px">
                <?php 
                echo $this->Html->image('/'.$user['image'], [
                        'alt' => 'User Image',
                        'id' => 'user-image',
                        'class' => 'img-thumbnail',
                        'style' => 'object-fit: cover; width: 150px'
                ]); ?>
            </div>

            <div>
                <?php 
                    echo $this->Form->file('submittedfile',['type' => 'file', 'onchange' => 'loadFile(this)']);
                ?>
            </div>
        </div>
        <?php 
        echo '<span>Name: </span>' . $this->Form->text('name', array(
            'label' => 'Name',
            'class' => 'form-control my-1',
            'value' => h($user['name'])
        ));
        echo $this->Form->input('email',array('label' => 'Email','class'=>'form-control my-1', 'value' => h($user['email']) ));
        echo $this->Form->input('dob', array('label' => 'Date of Birth', 'class' => 'datepicker', 'value' => $user['birthdate'] ?? ''));
        echo $this->Form->radio('gender',  ['Male'=>'Male', 'Female'=>'Female'],['value' => $user['gender'] ?? '']);
        echo $this->Form->input('hobby',array('label' => 'Hobby','class'=>'form-control','value' => h($user['hobby']) ?? ''));
        ?>
    </fieldset>
    <?php 
        echo $this->Form->button('Update',array('class' => 'btn btn-primary text-right', 'type' => 'submit'));  
        echo $this->Form->end() 
    ?>
</div>

<script>
    $(document).ready(function() {
        $('.datepicker').datepicker({
            dateFormat: 'yy-mm-dd', 
            changeMonth: true,      
            changeYear: true,       
            yearRange: '1900:2024', 
            maxDate: 0
        });
    });

    var loadFile = function(event) {
        var output = document.getElementById('user-image');
        output.src = URL.createObjectURL(event.files[0]);
        output.onload = function() {
            URL.revokeObjectURL(output.src)
        } // free memory
    };
</script>