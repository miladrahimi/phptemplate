<?php namespace MiladRahimi\PHPTemplate;

/**
 * Class TemplateEngineFactory
 * TemplateEngineFactory creates and prepares Template instance
 *
 * @package MiladRahimi\PHPTemplate
 * @author  Milad Rahimi <info@miladrahimi.com>
 */
class TemplateEngineFactory {
    /**
     * Create new Template instance and inject its dependency
     *
     * @return TemplateEngine
     */
    public static function create() {
        $te = new TemplateEngine();
        $compiler = new Compiler();
        $compiler->setTemplateEngine($te);
        $te->setCompiler($compiler);
        return $te;
    }
}