<?php
namespace Concrete\Core\Support;

use Exception;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Helper class to handle coding style.
 *
 * @see https://mlocati.github.io/php-cs-fixer-configurator/?version=2.2
 */
class CodingStyle
{
    /**
     * Coding style rules flag: compatible with PHP versions prior to 5.5.
     *
     * @var int
     */
    const FLAG_OLDPHP = 0x01;

    /**
     * Coding style rules flag: PHP-only files.
     *
     * @var int
     */
    const FLAG_PHPONLY = 0x02;

    /**
     * Coding style rules flag: bootstrap files.
     *
     * @var int
     */
    const FLAG_BOOTSTRAP = 0x03; // FLAG_OLDPHP | FLAG_PHPONLY

    /**
     * Coding style rules flag: files that implements a class following the PSR-4 rules.
     *
     * @var int
     */
    const FLAG_PSR4CLASS = 0x06; // Includes FLAG_PHPONLY

    /**
     * The full path to the web directory.
     *
     * @var string
     */
    protected $webroot = '';

    /**
     * Set the path to the web directory.
     *
     * @param string $value
     *
     * @throws Exception throws an exception if the directory does not exist
     *
     * @return $this
     */
    public function setWebroot($value)
    {
        $value = (string) $value;
        if ('' === $value) {
            $this->webroot = '';
        } else {
            $webroot = @realpath($value);
            if (false === $webroot || !is_dir($webroot)) {
                throw new Exception(sprintf('Unable to find the directory %s', $value));
            }
            $this->webroot = str_replace(DIRECTORY_SEPARATOR, '/', $webroot);
        }

        return $this;
    }

    /**
     * Get the path to the web directory.
     *
     * @return string
     */
    public function getWebroot()
    {
        if ('' === $this->webroot) {
            $this->setWebroot(dirname(dirname(dirname(__DIR__))));
        }

        return $this->webroot;
    }

    /**
     * Return the rules.
     *
     * @param int $flags A combination of CodingStyle::FLAG_... flags.
     *
     * @return array
     */
    public function getRules($flags)
    {
        $flags = (int) $flags;

        return [
            // PHP arrays should be declared using the configured syntax
            'array_syntax' => [
                'syntax' => 0 === ($flags & static::FLAG_OLDPHP) ? 'short' : 'long',
            ],
            // Binary operators should be surrounded by at least one space.
            'binary_operator_spaces' => [
                'align_double_arrow' => false,
                'align_equals' => false,
            ],
            // There MUST be one blank line after the namespace declaration.
            'blank_line_after_namespace' => true,
            // Ensure there is no code on the same line as the PHP open tag and it is followed by a blank line.
            'blank_line_after_opening_tag' => ($flags && static::FLAG_PHPONLY) === static::FLAG_PHPONLY ? true : false,
            // An empty line feed should precede a return statement.
            'blank_line_before_return' => true,
            // The body of each structure MUST be enclosed by braces. Braces should be properly placed. Body of braces should be properly indented.
            'braces' => ($flags && static::FLAG_PHPONLY) === static::FLAG_PHPONLY ? ['allow_single_line_closure' => true] : false,
            // A single space should be between cast and variable.
            'cast_spaces' => true,
            // Whitespace around the keywords of a class, trait or interfaces definition should be one space.
            'class_definition' => [
                'singleLine' => true,
            ],
            // Concatenation should be spaced according configuration.
            'concat_space' => [
                'spacing' => 'one',
            ],
            // Equal sign in declare statement should be surrounded by spaces or not following configuration.
            'declare_equal_normalize' => [
                'space' => 'single',
            ],
            // Replaces `dirname(__FILE__)` expression with equivalent `__DIR__` constant.
            'dir_constant' => 0 === ($flags & static::FLAG_OLDPHP) ? true : false,
            // The keyword `elseif` should be used instead of `else if` so that all control keywords look like single words.
            'elseif' => true,
            // PHP code MUST use only UTF-8 without BOM (remove BOM).
            'encoding' => true,
            // Replace deprecated `ereg` regular expression functions with preg.
            'ereg_to_preg' => true,
            // PHP code must use the long `<?php` tags or short-echo `<?=` tags and not other tag variations.
            'full_opening_tag' => true,
            // Spaces should be properly placed in a function declaration.
            'function_declaration' => true,
            // Replace core functions calls returning constants with the constants.
            'function_to_constant' => true,
            // Add missing space between function's argument and its typehint.
            'function_typehint_space' => true,
            // Single line comments should use double slashes `//` and not hash `#`.
            'hash_to_slash_comment' => true,
            // Include/Require and file path should be divided with a single space. File path should not be placed under brackets.
            'include' => true,
            // Code MUST use configured indentation type.
            'indentation_type' => ($flags && static::FLAG_PHPONLY) === static::FLAG_PHPONLY ? true : false,
            // Replaces is_null(parameter) expression with `null === parameter`.
            'is_null' => [
                'use_yoda_style' => false,
            ],
            // Ensure there is no code on the same line as the PHP open tag.
            'linebreak_after_opening_tag' => ($flags && static::FLAG_PHPONLY) === static::FLAG_PHPONLY ? true : false,
            // All PHP files must use same line ending.
            'line_ending' => true,
            // Cast should be written in lower case.
            'lowercase_cast' => true,
            // The PHP constants `true`, `false`, and `null` MUST be in lower case.
            'lowercase_constants' => true,
            // PHP keywords MUST be in lower case.
            'lowercase_keywords' => true,
            // Magic constants should be referred to using the correct casing.
            'magic_constant_casing' => true,
            // In method arguments and method call, there MUST NOT be a space before each comma and there MUST be one space after each comma.
            'method_argument_space' => true,
            // Methods must be separated with one blank line.
            'method_separation' => true,
            // Replaces `intval`, `floatval`, `doubleval`, `strval` and `boolval` function calls with according type casting operator.
            'modernize_types_casting' => true,
            // Function defined by PHP should be called using the correct casing.
            'native_function_casing' => true,
            // All instances created with new keyword must be followed by braces.
            'new_with_braces' => true,
            // Master functions shall be used instead of aliases.
            'no_alias_functions' => true,
            // There should be no empty lines after class opening brace.
            'no_blank_lines_after_class_opening' => true,
            // There should not be blank lines between docblock and the documented element.
            'no_blank_lines_after_phpdoc' => true,
            // The closing PHP tag MUST be omitted from files containing only PHP.
            'no_closing_tag' => true,
            // There should not be any empty comments.
            'no_empty_comment' => true,
            // There should not be empty PHPDoc blocks.
            'no_empty_phpdoc' => true,
            // Remove useless semicolon statements.
            'no_empty_statement' => true,
            // Removes extra blank lines and/or blank lines following configuration.
            'no_extra_consecutive_blank_lines' => [
                'tokens' => ['curly_brace_block', 'extra', 'parenthesis_brace_block', 'square_brace_block', 'throw', 'use'],
            ],
            // Remove leading slashes in use clauses.
            'no_leading_import_slash' => true,
            // The namespace declaration line shouldn't contain leading whitespace.
            'no_leading_namespace_whitespace' => true,
            // Either language construct `print` or `echo` should be used.
            'no_mixed_echo_print' => [
                'use' => 'echo',
            ],
            // Operator `=>` should not be surrounded by multi-line whitespaces.
            'no_multiline_whitespace_around_double_arrow' => true,
            // Convert PHP4-style constructors to `__construct`.
            'no_php4_constructor' => true,
            // Short cast `bool` using double exclamation mark should not be used.
            'no_short_bool_cast' => true,
            // Replace short-echo `<?=` with long format `<?php echo` syntax.
            'no_short_echo_tag' => 0 === ($flags & static::FLAG_OLDPHP) ? false : true,
            // Single-line whitespace before closing semicolon are prohibited.
            'no_singleline_whitespace_before_semicolons' => true,
            // When making a method or function call, there MUST NOT be a space between the method or function name and the opening parenthesis.
            'no_spaces_after_function_name' => true,
            // There MUST NOT be spaces around offset braces.
            'no_spaces_around_offset' => true,
            // There MUST NOT be a space after the opening parenthesis. There MUST NOT be a space before the closing parenthesis.
            'no_spaces_inside_parenthesis' => true,
            // Remove trailing commas in list function calls.
            'no_trailing_comma_in_list_call' => true,
            // PHP single-line arrays should not have trailing comma.
            'no_trailing_comma_in_singleline_array' => true,
            // Remove trailing whitespace at the end of non-blank lines.
            'no_trailing_whitespace' => true,
            // There MUST be no trailing spaces inside comments and phpdocs.
            'no_trailing_whitespace_in_comment' => true,
            // Removes unneeded parentheses around control statements.
            'no_unneeded_control_parentheses' => true,
            // Unused use statements must be removed.
            'no_unused_imports' => true,
            // There should not be an empty return statement at the end of a function.
            'no_useless_return' => true,
            // In array declaration, there MUST NOT be a whitespace before each comma.
            'no_whitespace_before_comma_in_array' => true,
            // Remove trailing whitespace at the end of blank lines.
            'no_whitespace_in_blank_line' => true,
            // Remove Zero-width space (ZWSP), Non-breaking space (NBSP) and other invisible unicode symbols.
            'non_printable_character' => true,
            // Array index should always be written by using square braces.
            'normalize_index_brace' => true,
            // There should not be space before or after object `T_OBJECT_OPERATOR` `->`.
            'object_operator_without_whitespace' => true,
            // Orders the elements of classes/interfaces/traits.
            'ordered_class_elements' => true,
            // Ordering use statements.
            'ordered_imports' => true,
            // Phpdoc should contain @param for all params.
            'phpdoc_add_missing_param_annotation' => [
                'only_untyped' => false,
            ],
            // Phpdocs annotation descriptions should not be a sentence.
            'phpdoc_annotation_without_dot' => true,
            // Docblocks should have the same indentation as the documented subject.
            'phpdoc_indent' => true,
            // Fix phpdoc inline tags, make inheritdoc always inline.
            'phpdoc_inline_tag' => true,
            // No alias PHPDoc tags should be used.
            'phpdoc_no_alias_tag' => true,
            // @return void and @return null annotations should be omitted from phpdocs.
            'phpdoc_no_empty_return' => true,
            // Classy that does not inherit must not have inheritdoc tags.
            'phpdoc_no_useless_inheritdoc' => true,
            // Annotations in phpdocs should be ordered so that param annotations come first, then throws annotations, then return annotations.
            'phpdoc_order' => true,
            // The type of `@return` annotations of methods returning a reference to itself must the configured one.
            'phpdoc_return_self_reference' => true,
            // Scalar types should always be written in the same form. `int` not `integer`, `bool` not `boolean`, `float` not `real` or `double`.
            'phpdoc_scalar' => true,
            // Annotations in phpdocs should be grouped together so that annotations of the same type immediately follow each other, and annotations of a different type are separated by a single blank line.
            'phpdoc_separation' => true,
            // Single line @var PHPDoc should have proper spacing.
            'phpdoc_single_line_var_spacing' => true,
            // Phpdocs summary should end in either a full stop, exclamation mark, or question mark.
            'phpdoc_summary' => true,
            // Docblocks should only be used on structural elements.
            'phpdoc_to_comment' => true,
            // Phpdocs should start and end with content, excluding the very first and last line of the docblocks.
            'phpdoc_trim' => true,
            // The correct case must be used for standard PHP types in phpdoc.
            'phpdoc_types' => true,
            // @var and @type annotations should not contain the variable name.
            'phpdoc_var_without_name' => true,
            // Pre incrementation/decrementation should be used if possible.
            'pre_increment' => true,
            // Class names should match the file name.
            'psr4' => ($flags && static::FLAG_PSR4CLASS) === static::FLAG_PSR4CLASS ? true : false,
            // Replaces `rand`, `srand`, `getrandmax` functions calls with their `mt_*` analogs.
            'random_api_migration' => true,
            // There should be one or no space before colon, and one space after it in return type declarations, according to configuration.
            'return_type_declaration' => [
                'space_before' => 'none',
            ],
            // Inside a classy element "self" should be preferred to the class name itself.
            'self_accessor' => true,
            // Cast `(boolean)` and `(integer)` should be written as `(bool)` and `(int)`, `(double)` and `(real)` as `(float)`.
            'short_scalar_cast' => true,
            // Ensures deprecation notices are silenced.
            'silenced_deprecation_error' => true,
            // A PHP file without end tag must always end with a single empty line feed.
            'single_blank_line_at_eof' => true,
            // There should be exactly one blank line before a namespace declaration.
            'single_blank_line_before_namespace' => true,
            // There MUST NOT be more than one property or constant declared per statement.
            'single_class_element_per_statement' => true,
            // There MUST be one use keyword per declaration.
            'single_import_per_statement' => true,
            // Each namespace use MUST go on its own line and there MUST be one blank line after the use statements block.
            'single_line_after_imports' => true,
            // Convert double quotes to single quotes for simple strings.
            'single_quote' => true,
            // Fix whitespace after a semicolon.
            'space_after_semicolon' => true,
            // Replace all `<>` with `!=`.
            'standardize_not_equals' => true,
            // A case should be followed by a colon and not a semicolon.
            'switch_case_semicolon_to_colon' => true,
            // Removes extra spaces between colon and case value.
            'switch_case_space' => true,
            // Standardize spaces around ternary operator.
            'ternary_operator_spaces' => true,
            // PHP multi-line arrays should have a trailing comma.
            'trailing_comma_in_multiline_array' => true,
            // Arrays should be formatted like function/method arguments, without leading or trailing single line space.
            'trim_array_spaces' => true,
            // Unary operators should be placed adjacent to their operands.
            'unary_operator_spaces' => true,
            // Visibility MUST be declared on all properties and methods; abstract and final MUST be declared before the visibility; static MUST be declared after the visibility.
            'visibility_required' => true,
            // In array declaration, there MUST be a whitespace after each comma.
            'whitespace_after_comma_in_array' => true,
        ];
    }

    /**
     * Returns the coding style flags associated to a file.
     *
     * @param string $file
     *
     * @return int|null return NULL if the file should not be parsed, an integer otherwise
     */
    public function getPathFlags($file)
    {
        $result = null;
        $fullpath = @realpath($file);
        if ($fullpath !== false && is_file($fullpath)) {
            $fullpath = str_replace(DIRECTORY_SEPARATOR, '/', $fullpath);
            if (strpos($fullpath, $this->getWebroot()) === 0) {
                $result = $this->getFileFlags(new SplFileInfo($fullpath));
            }
        }

        return $result;
    }

    /**
     * Get the list of files corresponding to a specific category.
     *
     * @param int $flags A combination of CodingStyle::FLAG_... flags.
     *
     * @return SplFileInfo[]|\Generator
     */
    public function getFileList($flags)
    {
        $flags = (int) $flags;
        $bootstrapFilePaths = $this->getBootstrapFilePaths();
        if (($flags & static::FLAG_OLDPHP) === static::FLAG_OLDPHP) {
            foreach ($bootstrapFilePaths as $bootstrapFilePath) {
                yield new SplFileInfo($bootstrapFilePath);
            }
        } else {
            $directory = new RecursiveDirectoryIterator(
                $this->getWebroot(),
                FilesystemIterator::KEY_AS_PATHNAME
                | FilesystemIterator::CURRENT_AS_FILEINFO
                | FilesystemIterator::SKIP_DOTS
                | FilesystemIterator::UNIX_PATHS
            );
            $iterator = new RecursiveIteratorIterator(
                $directory,
                RecursiveIteratorIterator::LEAVES_ONLY
                | RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($iterator as $item) {
                /* @var SplFileInfo $item */
                $itemFlags = $this->getFileFlags($item);
                if (null !== $itemFlags) {
                    if ($itemFlags === $flags) {
                        yield $item;
                    }
                }
            }
        }
    }

    /**
     * Get a description of the specified flags.
     *
     * @param int $flags A combination of CodingStyle::FLAG_... flags.
     *
     * @return string
     */
    public function describeFlags($flags)
    {
        $flags = (int) $flags;
        if (($flags & static::FLAG_PSR4CLASS) === static::FLAG_PSR4CLASS) {
            $result = 'PHP-only file with the same name as the class it implements';
        } elseif (($flags & static::FLAG_BOOTSTRAP) === static::FLAG_BOOTSTRAP) {
            $result = 'PHP-only file with syntax compatible with old PHP versions';
        } elseif ($flags === 0) {
            $result = 'file with mixed contents';
        } else {
            $words = [];
            if (($flags & static::FLAG_OLDPHP) === static::FLAG_OLDPHP) {
                $words[] = 'file with syntax compatible with old PHP versions';
            }
            if (($flags & static::FLAG_PHPONLY) === static::FLAG_PHPONLY) {
                $words[] = 'PHP-only file';
            }
            $result = implode(', ', $words);
        }

        return $result;
    }

    /**
     * Get the files accessed diring bootstrap processes.
     *
     * @return string[]
     */
    protected function getBootstrapFilePaths()
    {
        $webroot = $this->getWebroot();

        return [
            $webroot . '/index.php',
            $webroot . '/concrete/dispatcher.php',
            $webroot . '/concrete/bin/concrete5.php',
        ];
    }

    /**
     * Returns the coding style flags associated to a file.
     *
     * @param SplFileInfo $file
     *
     * @return int|null return NULL if the file should not be parsed, an integer otherwise
     */
    protected function getFileFlags(SplFileInfo $file)
    {
        if (
            !$file->isFile()
            || (
                !in_array(strtolower($file->getExtension()), ['php'])
                &&
                strpos($file->getFilename(), '.php_cs.') !== 0
            )
        ) {
            $result = null;
        } else {
            $path = $file->getPathname();
            $relativePath = substr($path, strlen($this->getWebroot()));
            switch (true) {
                // Bootstrap files
                case in_array($path, $this->getBootstrapFilePaths(), true):
                    $result = static::FLAG_OLDPHP | static::FLAG_PHPONLY;
                    break;
                // Attribute controllers
                case 1 === preg_match('~^/application/attributes/\w+/controller\.php$~', $relativePath):
                case 1 === preg_match('~^/concrete/attributes/\w+/controller\.php$~', $relativePath):
                case 1 === preg_match('~^/packages/\w+/attributes/\w+/controller\.php$~', $relativePath):
                    $result = static::FLAG_PHPONLY;
                    break;
                // Authentication controllers
                case 1 === preg_match('~^/application/authentication/\w+/controller\.php$~', $relativePath):
                case 1 === preg_match('~^/concrete/authentication/\w+/controller\.php$~', $relativePath):
                case 1 === preg_match('~^/packages/\w+/authentication/\w+/controller\.php$~', $relativePath):
                    $result = static::FLAG_PHPONLY;
                    break;
                // Block controllers
                case 1 === preg_match('~^/application/blocks/\w+/controller\.php$~', $relativePath):
                case 1 === preg_match('~^/concrete/blocks/\w+/controller\.php$~', $relativePath):
                case 1 === preg_match('~^/packages/\w+/blocks/\w+/controller\.php$~', $relativePath):
                    $result = static::FLAG_PHPONLY;
                    break;
                // Application and core bootstrap files
                case 0 === strpos($relativePath, '/application/bootstrap/'):
                case 0 === strpos($relativePath, '/concrete/bootstrap/'):
                    $result = static::FLAG_PHPONLY;
                    break;
                // Runtime configuration files
                case 0 === strpos($relativePath, '/application/config/'):
                    $result = null;
                    break;
                // Core and package configuration files
                case 0 === strpos($relativePath, '/concrete/config/'):
                case 1 === preg_match('~^/packages/\w+/config/~', $relativePath):
                    $result = static::FLAG_PHPONLY;
                    break;
                // Other controller files
                case 0 === strpos($relativePath, '/application/controllers/'):
                case 0 === strpos($relativePath, '/concrete/controllers/'):
                case 1 === preg_match('~^/packages/\w+/controllers/~', $relativePath):
                    $result = static::FLAG_PHPONLY;
                    break;
                // Geolocation controllers
                case 1 === preg_match('~^/application/geolocation/\w+/controller\.php$~', $relativePath):
                case 1 === preg_match('~^/concrete/geolocation/\w+/controller\.php$~', $relativePath):
                case 1 === preg_match('~^/packages/\w+/geolocation/\w+/controller\.php$~', $relativePath):
                    $result = static::FLAG_PHPONLY;
                    break;
                // Automated jobs
                case 0 === strpos($relativePath, '/application/jobs/'):
                case 0 === strpos($relativePath, '/concrete/jobs/'):
                case 1 === preg_match('~^/packages/\w+/jobs/~', $relativePath):
                    $result = static::FLAG_PHPONLY;
                    break;
                // IDE Symbols
                case '/concrete/src/Support/.phpstorm.meta.php' === $relativePath:
                case '/concrete/src/Support/__IDE_SYMBOLS__.php' === $relativePath:
                    $result = null;
                    break;
                // Classes
                case 0 === strpos($relativePath, '/application/src/'):
                case 0 === strpos($relativePath, '/concrete/src/'):
                case 1 === preg_match('~^/packages/\w+/src/~', $relativePath):
                    $result = static::FLAG_PSR4CLASS;
                    break;
                // Theme controllers
                case 1 === preg_match('~^/application/themes/\w+/page_theme\.php$~', $relativePath):
                case 1 === preg_match('~^/concrete/themes/\w+/page_theme\.php$~', $relativePath):
                case 1 === preg_match('~^/packages/\w+/themes/\w+/page_theme\.php$~', $relativePath):
                    $result = static::FLAG_PHPONLY;
                    break;
                // Vendor files
                case 0 === strpos($relativePath, '/concrete/vendor/'):
                case 1 === preg_match('~^/packages/\w+/vendor/~', $relativePath):
                    $result = null;
                    break;
                // Application files
                case 0 === strpos($relativePath, '/application/files/'):
                    $result = null;
                    break;
                // All the other files
                default:
                    $result = 0;
                    break;
            }
        }

        return $result;
    }
}
