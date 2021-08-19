<?php
/**
 * @var Pollen\Form\FormTemplateInterface $this
 */
if ($errors = $this->get('notices.ERROR', [])) :
    echo $this->partial('tag', [
        'attrs'   => [
           'class' => 'FormNotice FormNotice--error'
        ],
        'tag'    => 'div',
        'content' => $this->fetch('notices-error', ['messages' => $errors])
    ]);
elseif (($success = $this->get('notices.SUCCESS', [])) || ($success = $this->get('notices.notice', []))) :
    echo $this->partial('tag', [
        'attrs'   => [
            'class' => 'FormNotice FormNotice--success'
        ],
        'tag'    => 'div',
        'content' => $this->fetch('notices-success', ['messages' => $success])
    ]);
elseif ($info = $this->get('notices.INFO', [])) :
    echo $this->partial('tag', [
        'attrs'   => [
            'class' => 'FormNotice FormNotice--info'
        ],
        'tag'    => 'div',
        'content' => $this->fetch('notices-info', ['messages' => $info])
    ]);
elseif ($warning = $this->get('notices.WARNING', [])) :
    echo $this->partial('tag', [
        'attrs'   => [
            'class' => 'FormNotice FormNotice--warning'
        ],
        'tag'    => 'div',
        'content' => $this->fetch('notices-warning', ['messages' => $warning])
    ]);
endif;