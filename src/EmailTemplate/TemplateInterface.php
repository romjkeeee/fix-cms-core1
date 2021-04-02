<?php

namespace AltSolution\Admin\EmailTemplate;

interface TemplateInterface
{
    /**
     * @return string
     */
    function getName();

    /**
     * @return string
     */
    function getDescription();

    /**
     * @return array
     */
    function getLegend();

    /**
     * @return string
     */
    function getSubject();

    /**
     * @return array
     */
    function getFrom();

    /**
     * @return array
     */
    function getTo();

    /**
     * @return string
     */
    function getView();

    /**
     * @return bool
     */
    function isMultilingual();
}
