<?php namespace MiladRahimi\PHPTemplate;

/**
 * Interface TemplateEngineInterface
 * Standard API for rendering template files
 *
 * @package MiladRahimi\PHPTemplate
 * @author  Milad Rahimi <info@miladrahimi.com>
 */
interface TemplateEngineInterface {

    /**
     * Render template file (open, compile and  return)
     *
     * @param string $template_file Template file to be rendered
     * @param array  $data          Data to be injected
     *
     * @return string Rendered content to be sent to user browser
     */
    public function render($template_file, array $data = array());
}