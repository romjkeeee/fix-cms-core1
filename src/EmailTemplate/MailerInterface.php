<?php

namespace AltSolution\Admin\EmailTemplate;

use Psr\Log\LoggerAwareInterface;

interface MailerInterface extends LoggerAwareInterface
{
    /**
     * @param TemplateInterface $template
     * @param array|null $data
     * @return int
     */
    function send(TemplateInterface $template, array $data = null);
}
