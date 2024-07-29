<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body class="container">
    <?php if (AuthComponent::user('id')): ?>
        <?php echo $this->element('navbar') ?>
    <?php else: ?>
        <?php echo $this->element('noauth-navbar') ?>
    <?php endif; ?> 
    <?php if ($this->Session->check('Message.flash')): ?>
        <?php echo $this->Flash->render(); ?>
    <?php endif; ?>
    <?php echo $this->fetch('content'); ?>
</body>
</html>