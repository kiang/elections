<?php
if(Configure::read('loginMember.group_id') !== '1') {
    echo $count;
} else {
    echo $this->Html->link($count, '/admin/candidates/submits');
}