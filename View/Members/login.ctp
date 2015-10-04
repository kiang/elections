<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <?php echo $this->Form->create('Member', array('action' => 'login')); ?>
        <h1 style="text-align: center">會員登入</h1>
        <?php echo $this->Form->input('帳戶', array('class' => 'form-control input-lg')); ?>
        <p>&nbsp;</p>
        <?php echo $this->Form->input('密碼', array('type' => 'password', 'class' => 'form-control input-lg')); ?>
        <p>&nbsp;</p>
        <button type="submit" name="go" class="btn btn-lg btn-primary btn-block">登入</button>
        <?php echo $this->Form->end(); ?>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
    </div>
</div>