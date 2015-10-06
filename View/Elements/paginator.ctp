<?php

if (!isset($url)) {
    $url = array();
}
echo '<nav>';
echo '<ul class="pagination">';
// echo $this->Paginator->first('<<', array('url' => $url));
echo $this->Paginator->prev('<span aria-hidden="true">&laquo;</span>', array('url' => $url, 'tag' => 'li', 'escape' => false));
echo $this->Paginator->numbers(array('url' => $url, 'tag' => 'li', 'escape' => false, 'separator' => '', 'currentTag' => 'a', 'currentClass' => 'active'));
echo $this->Paginator->next('<span aria-hidden="true">&raquo;</span>', array('url' => $url, 'tag' => 'li', 'escape' => false));
// echo ' &nbsp; ' . $this->Paginator->last('>>', array('url' => $url));
echo '</ul>';
echo '</nav>';