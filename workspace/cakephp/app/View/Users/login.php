<div class="container">
    <h1>Login</h1>
    <div class="">
        <?php echo $this->Form->create('User'); 
        echo $this->Form->input('email',array('label' => 'Email','class' => 'form-control')); 
        echo $this->Form->input('password',array('label' => 'Password','class' => 'form-control')); ?>
        <div class="text-right">
            <?php echo $this->Form->button('Login',array('label' => 'Password','class' => 'btn btn-primary text-right'));  
            echo $this->Form->end() ?>
        </div>
    </div>
</div>

<script>
</script>