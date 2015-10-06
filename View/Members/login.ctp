<div class="row">
    <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <?php echo $this->Form->create('Member', array('action' => 'login')); ?>
        <h1 style="text-align: center">會員登入</h1>
        <?php echo $this->Form->input('username', array('label' => '帳戶', 'class' => 'form-control input-lg')); ?>
        <p>&nbsp;</p>
        <?php echo $this->Form->input('password', array('label' => '密碼', 'type' => 'password', 'class' => 'form-control input-lg')); ?>
        <p>&nbsp;</p>
        <button type="submit" class="btn btn-lg btn-primary btn-block">登入</button>
        <?php echo $this->Form->end(); ?>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
    </div>
</div>