<div class="container">
    <h1>User Profile</h1>
    
    <div style="display: flex; align-items: center">
        <div style="max-width: 200px; margin-right: 1rem">
            <?php 
                echo $this->Html->image($user['image'], [
                    'alt' => 'User Image',
                    'id' => 'user-image',
                    'class' => 'img-thumbnail',
                    'style' => 'object-fit: cover;',
                    'escape' => false
                ]); ?>
        </div>
        <div>
            <?php
                $birthdate =  ($user['birthdate'])?date("F jS, Y", strtotime(($user['birthdate']))):'';
                echo "<h4>" . h($user['name']) .",<span>   ".h($user['age'])."</span></h4>";
                echo "<p>Email: " . h($user['email']) . "</p>";
                echo "<p>Gender: " . h($user['gender']) . "</p>";
                echo "<p>Birthdate: " .$birthdate. "</p>";
                echo "<p>Joined: " . date("F jS, Y", strtotime(($user['created']))) . "</p>";
                echo "<p>Last Joined: " .date("F jS, Y", strtotime(($user['modified']))) . "</p>";

            ?>
        </div>
    </div>
    <button class="btn my-4">
        <?php echo $this->Html->link('Update', ['controller' => 'Users', 'action' => 'edit']) ?>
    </button>
    <div>
        <p>Hobby:</p>
        <?php
            echo "<p>" . h($user['hobby']) . "</p>";
        ?>
    </div>
    
</div>