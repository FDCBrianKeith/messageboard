<nav class="navbar navbar-expand-lg navbar-light bg-light mb-2">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <h1 class="mr-2"><?php echo (AuthComponent::user('name')); ?></h1>
    <ul class="navbar-nav">
      <li class="nav-item active">
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/cakephp/messages">Message List</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/cakephp/users">Profile</a>
      </li>
      <li class="nav-item">
        <?php echo $this->Html->link('Logout', ['controller' => 'Users', 'action' => 'logout'], ['class' => 'nav-link']) ?>
      </li>
    </ul>
  </div>
</nav>