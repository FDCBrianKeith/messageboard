<h1>Change Password</h1>

<?php echo $this->Form->create('User') ?>
<div>
    <?php 
        echo $this->Form->input('password',array('label' => 'Current Password','class'=>'form-control my-1' ));
        echo $this->Form->input('newPassword',array('label' => 'New Password','class'=>'form-control my-1', 'type' => 'password' ));
        echo $this->Form->button('Update',array('class' => 'btn btn-primary text-right', 'type' => 'submit'));  
        echo $this->Form->end() 
    ?>
</div>