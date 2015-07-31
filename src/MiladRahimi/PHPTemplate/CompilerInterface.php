<?php namespace MiladRahimi\PHPTemplate;

/**
 * Interface CompilerInterface
 * Standard API for compiling template contents
 *
 * @package MiladRahimi\PHPTemplate
 * @author  Milad Rahimi <info@miladrahimi.com>
 */
interface CompilerInterface {
    /**
     * Compile the content and inject the data
     *
     * @param string $content Content to be compiled
     * @param array  $data    Data to be injected
     *
     * @return string Compiled content
     */
    public function compile($content, array $data = array());
}