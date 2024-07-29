<div class="container">
    <h1>Register</h1>
    <div class="">
        <?php echo $this->Form->create('User'); ?>
        <div class="mb-2">
            <label>Name</label>
            <?php echo $this->Form->text('name',array('label' =>false ,'class' => 'form-control'));  ?>
        </div>
      <?php 
        echo $this->Form->input('email',array('label' => 'Email','class' => 'form-control')); 
        echo $this->Form->input('password',array('label' => 'Password','class' => 'form-control')); 
        echo $this->Form->input('password_confirmation',array('label' => 'Confirm Password','class' => 'form-control')); 
    ?>
    <div class="text-right">
        <?php echo $this->Form->button('Register',array('class' => 'btn btn-primary text-right', 'type' => 'submit'));  
        echo $this->Form->end() ?>
    </div>
    </div>
</div>