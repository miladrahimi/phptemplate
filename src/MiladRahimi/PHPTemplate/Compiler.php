<?php namespace MiladRahimi\PHPTemplate;

use MiladRahimi\PHPTemplate\Exceptions\BadDataException;
use MiladRahimi\PHPTemplate\Exceptions\BadTemplateException;
use MiladRahimi\PHPTemplate\Exceptions\TemplateEngineNotSetException;

/**
 * Class Compiler
 * Compiler compiles template content and injects data to it
 *
 * @package MiladRahimi\PHPTemplate
 * @author  Milad Rahimi <info@miladrhimi.com>
 */
class Compiler implements CompilerInterface {

    /**
     * TemplateEngineInterface object
     *
     * @var TemplateEngineInterface|null
     */
    public $template_engine = null;

    /**
     * Compile the content and inject the data to it
     *
     * @param string $content Content to be compile
     * @param array  $data    Data to be injected
     *
     * @return string Compiled content
     * @throws BadTemplateException
     */
    public function compile($content, array $data = array()) {
        // Check Parameter
        if (!is_scalar($content)) {
            throw new BadTemplateException("Template content is not valid");
        }
        // Self == This : for using in closures!
        $self = $this;
        // <import file="...">
        $p = '@<\s*import\s+file="([^"]+)"\s*/?>@s';
        $content = preg_replace_callback($p, function ($matches) use ($self, $data) {
            return $self->external($data, $matches[1]);
        }, $content);
        // <array value="..."> ... </array>
        $p = '@<\s*([^\s]+)\s+key="([^"]+)"\s+value="([^"]+)"\s*>(?!</\s*\\1\s*>)(.+)</\s*\\1\s*>@s';
        $content = preg_replace_callback($p, function ($matches) use ($self, $data) {
            return $self->arrayKeyValue($data, $matches[0], $matches[1], $matches[2], $matches[3], $matches[4]);
        }, $content);
        // <array value="..."> ... </array>
        $p = '@<\s*([^\s]+)\s+value="([^"]+)"\s*>(?!</\s*\\1\s*>)(.+)</\s*\\1\s*>@s';
        $content = preg_replace_callback($p, function ($matches) use ($self, $data) {
            return $self->arrayValue($data, $matches[0], $matches[1], $matches[2], $matches[3]);
        }, $content);
        // <bool> ... </bool>
        // <array> ... </array>
        $p = "@<\s*([^>]+)\s*>(?!<\s*/\\1\s*>)(.+)<\s*/\\1\s*>@s";
        $content = preg_replace_callback($p, function ($matches) use ($self, $data) {
            return $self->tag($data, $matches[0], $matches[1], $matches[2]);
        }, $content);
        // {!phrase-raw}
        $content = preg_replace_callback("@{!\s*([^}]+)\s*}@s", function ($matches) use ($self, $data) {
            return $self->phrase($data, $matches[0], $matches[1], false);
        }, $content);
        // {phrase-safe}
        $content = preg_replace_callback("@{\s*([^}]+)\s*}@s", function ($matches) use ($self, $data) {
            return $self->phrase($data, $matches[0], $matches[1]);
        }, $content);
        // Return compiled content
        return $content;
    }

    /**
     * Replace phrases
     *
     * @param array  $data      Data to be injected
     * @param string $statement Raw phrase statement
     * @param string $name      Phrase name
     * @param bool   $safe_html Being safe by htmlspecialchars()
     *
     * @return string Phrase replacement
     * @throws \MiladRahimi\PHPTemplate\Exceptions\BadDataException
     */
    private function phrase(array $data, $statement, $name, $safe_html = true) {
        $name = trim($name);
        if (array_key_exists($name, $data)) {
            $result = $this->value($data[$name], $name, "s");
            if ($safe_html) {
                return htmlspecialchars($result);
            }
            return $result;
        }
        $count = 1;
        return str_replace($name, $this->compile($name, $data), $statement, $count);
    }

    /**
     * Replace tags
     *
     * @param array  $data      Data to be injected
     * @param string $statement Raw tag statement
     * @param string $name      Tag name
     * @param string $content   Tag content (inside)
     *
     * @return string Tag replacement
     * @throws \MiladRahimi\PHPTemplate\Exceptions\BadDataException
     * @throws \MiladRahimi\PHPTemplate\Exceptions\BadTemplateException
     */
    private function tag(array $data, $statement, $name, $content) {
        $name = trim($name);
        if (array_key_exists($name, $data)) {
            $item = $this->value($data[$name], $name);
            if (is_scalar($item) && boolval($item)) {
                return $content;
            } elseif (is_scalar($item) && !boolval($item)) {
                return "";
            } elseif (is_array($item)) {
                $a = array();
                foreach ($item as $k => $v) {
                    $a[$k] = $v;
                }
                return $this->compile($content, array_merge($data, $a));
            }
        }
        $count = 1;
        return str_replace($content, $this->compile($content, $data), $statement, $count);
    }

    /**
     * Replace arrays
     *
     * @param array  $data      Data to be injected
     * @param string $statement Raw tag statement
     * @param string $name      Array name
     * @param mixed  $value     Value name
     * @param string $content   Array content (inside)
     *
     * @return string Array replacement
     * @throws \MiladRahimi\PHPTemplate\Exceptions\BadDataException
     * @throws \MiladRahimi\PHPTemplate\Exceptions\BadTemplateException
     */
    private function arrayValue(array $data, $statement, $name, $value, $content) {
        $name = trim($name);
        $value = trim($value);
        if (array_key_exists($name, $data)) {
            $item = $this->value($data[$name], $name, "a");
            $r = "";
            foreach ($item as $v) {
                $r .= $this->compile($content, array_merge($data, array($value => $v)));
            }
            return $r;
        }
        $count = 1;
        return str_replace($content, $this->compile($content, $data), $statement, $count);
    }

    /**
     * Replace arrays
     *
     * @param array  $data      Data to be injected
     * @param string $statement Raw array statement
     * @param string $name      Array name
     * @param string $key       Key name
     * @param mixed  $value     Value name
     * @param string $content   Array content (inside)
     *
     * @return string Array replacement
     * @throws \MiladRahimi\PHPTemplate\Exceptions\BadDataException
     * @throws \MiladRahimi\PHPTemplate\Exceptions\BadTemplateException
     */
    private function arrayKeyValue(array $data, $statement, $name, $key, $value, $content) {
        $name = trim($name);
        $value = trim($value);
        $key = trim($key);
        if (array_key_exists($name, $data)) {
            $item = $this->value($data[$name], $name, "a");
            $r = "";
            foreach ($item as $k => $v) {
                $r .= $this->compile($content, array_merge($data, array($key => $k, $value => $v)));
            }
            return $r;
        }
        $count = 1;
        return str_replace($content, $this->compile($content, $data), $statement, $count);
    }

    /**
     * Include external template files
     *
     * @param array  $data Data to pass to external template file
     * @param string $file Template file to be loaded
     *
     * @return string The external template content (compiled)
     * @throws \MiladRahimi\PHPTemplate\Exceptions\TemplateEngineNotSetException
     */
    private function external(array $data, $file) {
        $file = trim($file);
        if (is_null($this->template_engine)) {
            throw new TemplateEngineNotSetException();
        }
        return $this->template_engine->render($file, $data);
    }

    /**
     * Convert to ultimate value
     *
     * @param mixed       $item        Item to be converted
     * @param string      $name        Item name to be mentioned on errors
     * @param string|null $expectation "s" for scalar and "a" for array
     *
     * @return array|string
     * @throws BadDataException
     */
    private function value($item, $name, $expectation = null) {
        if (is_scalar($item) || is_null($item)) {
            $result = (string)$item;
        } elseif (is_object($item) && method_exists($item, "__toString")) {
            $result = (string)$item;
        } elseif (is_object($item) && $item instanceof \Closure) {
            $ref = new \ReflectionFunction($item);
            if ($ref->getNumberOfRequiredParameters()) {
                throw new BadDataException($name . " has required parameters");
            }
            $result = $this->value($ref->invoke(), $name . " : closure");
        } elseif (is_array($item) || (is_object($item) && $item instanceof \Traversable)) {
            $result = (array)$item;
        } else {
            throw new BadDataException($name . " is a unsupported data");
        }
        if ($expectation == "s" && !is_scalar($result)) {
            throw new BadDataException($name . " must be a scalar value");
        }
        if ($expectation == "a" && !is_array($result)) {
            throw new BadDataException($name . " must be an array");
        }
        return $result;
    }

    /**
     * @return TemplateEngineInterface|null
     */
    public function getTemplateEngine() {
        return $this->template_engine;
    }

    /**
     * @param TemplateEngineInterface $template_engine
     */
    public function setTemplateEngine(TemplateEngineInterface $template_engine) {
        $this->template_engine = $template_engine;
    }

}