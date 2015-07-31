<?php namespace MiladRahimi\PHPTemplate;

use MiladRahimi\PHPTemplate\Exceptions\CompilerNotSetException;
use MiladRahimi\PHPTemplate\Exceptions\FileNotFoundException;
use MiladRahimi\PHPTemplate\Exceptions\InvalidArgumentException;

/**
 * Class TemplateEngine
 * TemplateEngineFactory class works with template files (views) and render them.
 *
 * @package MiladRahimi\PHPTemplate
 * @author  Milad Rahimi <info@miladrahimi.com>
 */
class TemplateEngine implements TemplateEngineInterface {

    /**
     * Base data for adding to controller given data
     *
     * @var array
     */
    private $base_data = array();

    /**
     * Template files base directory (views directory)
     *
     * @var string|null
     */
    private $base_directory = null;

    /**
     * CompilerInterface object
     *
     * @var CompilerInterface|null
     */
    private $compiler = null;

    /**
     * Constructor
     *
     * @param CompilerInterface|null $compiler CompilerInstance
     */
    public function __construct(CompilerInterface $compiler = null) {
        if ($compiler != null) {
            $this->setCompiler($compiler);
        }
    }

    /**
     * Render template file (open, compile and  return)
     *
     * @param string $template_file Template file to be rendered
     * @param array  $data          Data to be injected
     *
     * @return string Rendered content to send to user browser
     * @throws \MiladRahimi\PHPTemplate\Exceptions\BadTemplateException
     * @throws \MiladRahimi\PHPTemplate\Exceptions\CompilerNotSetException
     * @throws \MiladRahimi\PHPTemplate\Exceptions\FileNotFoundException
     */
    public function render($template_file, array $data = array()) {
        if (!isset($template_file) || !is_scalar($template_file)) {
            throw new InvalidArgumentException("Template file must be a string value");
        }
        if (is_null($this->compiler)) {
            throw new CompilerNotSetException();
        }
        $file = $this->base_directory . $template_file;
        if (!file_exists($file)) {
            throw new FileNotFoundException($file . " does not exist");
        }
        $content = file_get_contents($file);
        return $this->compiler->compile($content, array_merge($this->base_data, $data));
    }

    /**
     * @return string
     */
    public function getBaseDirectory() {
        return $this->base_directory;
    }

    /**
     * @param string|null $path
     */
    public function setBaseDirectory($path = null) {
        if (!isset($path) || (!is_string($path) && !is_null($path))) {
            throw new InvalidArgumentException("Base directory must be a string/null value");
        }
        if (!is_null($path) && !(substr($path, -1) == "/") && !(substr($path, -1) == "\\")) {
            $path = $path . "/";
        }
        $this->base_directory = $path;
    }

    /**
     * @return array
     */
    public function getBaseData() {
        return $this->base_data;
    }

    /**
     * @param array $base_data
     */
    public function setBaseData(array $base_data = array()) {
        $this->base_data = $base_data;
    }

    /**
     * @return Compiler
     */
    public function getCompiler() {
        return $this->compiler;
    }

    /**
     * @param CompilerInterface $compiler
     */
    public function setCompiler(CompilerInterface $compiler) {
        $this->compiler = $compiler;
    }

}